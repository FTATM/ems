<?php
/**
 * Meter data simulator — CLI only.
 *
 *   php config/simulate-data.php                 # all active meters, every 60s (same as the real poller)
 *   php config/simulate-data.php --meters=203,207
 *   php config/simulate-data.php --interval=5    # faster, for demos
 *   php config/simulate-data.php --once          # send one round and exit
 *
 * Reads active meters (meter.is_active=1, is_deleted=0) from the DB and posts to config/meter-data.php.
 *  - meter_type_id 1 (Electrical): 3-phase model, values are consistent with each other
 *      (kVA = √3·V·I, kW = kVA·PF, kVAR = √(kVA²−kW²), V_LL = √3·V_LN)
 *  - meter_type_id 2 (Water): Flow / Velocity / Positive Cumulative
 *  - Load follows a daily curve (Asia/Bangkok), weekends are lighter, each meter has its own scale.
 *  - Cumulative registers (kWh, kVAh, kVARh, Positive Cumulative) continue from the last stored value
 *    and only ever increase.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

date_default_timezone_set('Asia/Bangkok');
require __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

$api_url  = "http://localhost/ems/config/meter-data.php";
$opts     = getopt('', ['meters::', 'interval::', 'once']);
$interval = max(1, (int)($opts['interval'] ?? 60));
$only     = isset($opts['meters']) ? array_map('intval', explode(',', $opts['meters'])) : null;

// Peak electrical demand per meter (kW). data_type.maxvaluecolumn for kW is 10, so stay below it.
const PEAK_KW = 8.0;
// Water: peak flow (m³/h) and pipe inner diameter (m) for DN50
const PEAK_FLOW = 6.0;
const PIPE_AREA = 0.0019635;

function jitter($sigma)
{
    // approx. normal noise (sum of uniforms)
    return ((mt_rand() + mt_rand() + mt_rand()) / mt_getrandmax() - 1.5) * 2 * $sigma;
}

/** 0..1 load factor for the time of day; smooth office-hours curve with a lunch dip. */
function loadFactor($ts)
{
    $h = (int)date('G', $ts) + (int)date('i', $ts) / 60;
    $weekend = (int)date('N', $ts) >= 6;
    $base = 0.15;                                   // night standby
    $day  = exp(-pow(($h - 10.5) / 2.8, 2)) * 0.75  // morning peak
          + exp(-pow(($h - 15.0) / 3.0, 2)) * 0.85; // afternoon peak (AC)
    $lunch = exp(-pow(($h - 12.3) / 0.6, 2)) * 0.25;
    $f = $base + max(0, $day - $lunch);
    if ($weekend) $f = $base + ($f - $base) * 0.35;
    return min(1, $f);
}

/** Slow per-meter wander so two meters never look identical. */
function wander($id, $ts, $period = 1800)
{
    return sin(($ts / $period) + $id * 1.7) * 0.06 + sin(($ts / ($period * 0.37)) + $id) * 0.03;
}

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($_ENV['DB_HOST'] ?? '127.0.0.1', $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '', $_ENV['DB_NAME'] ?? 'ams', (int)($_ENV['DB_PORT'] ?? 3306));
if ($conn->connect_error) {
    exit("DB error: {$conn->connect_error}\n");
}

function loadMeters($conn, $only)
{
    $res = $conn->query("SELECT id, name, meter_type_id FROM meter WHERE is_active = 1 AND is_deleted = 0 ORDER BY id");
    $meters = [];
    while ($r = $res->fetch_assoc()) {
        if ($only === null || in_array((int)$r['id'], $only, true)) $meters[] = $r;
    }
    return $meters;
}

/** Last stored value of a data_type for a meter (for cumulative registers), or $default. */
function lastValue($conn, $meterId, $typeName, $default)
{
    $stmt = $conn->prepare("SELECT md.value FROM meter_data md JOIN data_type dt ON dt.id = md.type_value_id
                            WHERE md.meter_id = ? AND dt.name = ? ORDER BY md.create_date DESC LIMIT 1");
    $stmt->bind_param("is", $meterId, $typeName);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_row();
    $stmt->close();
    return $row ? (float)$row[0] : $default;
}

$state = [];   // per-meter cumulative counters
$last  = [];   // last tick timestamp per meter

function simulateElectric($id, $ts, $dt, &$st)
{
    $lf = max(0.05, loadFactor($ts) + wander($id, $ts) + jitter(0.015));
    $scale = 0.8 + ($id % 5) * 0.08;               // meter-specific size
    $kwTarget = PEAK_KW * $scale * $lf;

    // Voltages: 230 V ±, sags slightly under load; phases differ a little
    $vln = [];
    foreach ([0, 1, 2] as $p) {
        $vln[$p] = 231 - 3.5 * $lf + jitter(0.6) + sin($ts / 900 + $p + $id) * 0.8;
    }
    $vll = [
        sqrt(3) * ($vln[0] + $vln[1]) / 2,
        sqrt(3) * ($vln[1] + $vln[2]) / 2,
        sqrt(3) * ($vln[2] + $vln[0]) / 2,
    ];

    // PF drops at light load (idle motors/transformers)
    $pf = min(0.99, max(0.72, 0.78 + 0.19 * $lf + jitter(0.006)));

    // Phase current: slightly unbalanced
    $vavg = array_sum($vln) / 3;
    $iBase = ($kwTarget * 1000) / (3 * $vavg * $pf);
    $imb = [1 + 0.05 * sin($ts / 700 + $id) + jitter(0.01), 1 - 0.04 * sin($ts / 500 + $id) + jitter(0.01), 1 + jitter(0.012)];
    $I = [];
    foreach ([0, 1, 2] as $p) $I[$p] = max(0.05, $iBase * $imb[$p]);
    $iAvg = array_sum($I) / 3;

    // Power from the actual phase values so everything is consistent
    $kva = ($vln[0] * $I[0] + $vln[1] * $I[1] + $vln[2] * $I[2]) / 1000;
    $kw  = $kva * $pf;
    $kvar = sqrt(max(0, $kva * $kva - $kw * $kw));

    $st['kWh']   += $kw   * $dt / 3600;
    $st['kVAh']  += $kva  * $dt / 3600;
    $st['kVARh'] += $kvar * $dt / 3600;

    return [
        "kW" => round($kw, 2),
        "kWh" => round($st['kWh'], 2),
        "kVA" => round($kva, 2),
        "kVAh" => round($st['kVAh'], 2),
        "kVAR" => round($kvar, 2),
        "kVARh" => round($st['kVARh'], 2),
        "Voltage A-N" => round($vln[0], 2),
        "Voltage B-N" => round($vln[1], 2),
        "Voltage C-N" => round($vln[2], 2),
        "Voltage A_B" => round($vll[0], 2),
        "Voltage B_C" => round($vll[1], 2),
        "Voltage C_A" => round($vll[2], 2),
        "Current A" => round($I[0], 2),
        "Current B" => round($I[1], 2),
        "Current C" => round($I[2], 2),
        "Current avg" => round($iAvg, 2),
        "Pf" => round($pf, 3),
        "Frequency" => round(50 + jitter(0.04) + sin($ts / 1200) * 0.03, 2),
    ];
}

function simulateWater($id, $ts, $dt, &$st)
{
    $h = (int)date('G', $ts) + (int)date('i', $ts) / 60;
    // household/building usage: morning + evening peaks, near zero overnight
    $f = 0.03 + exp(-pow(($h - 7.0) / 1.3, 2)) + 0.6 * exp(-pow(($h - 12.5) / 1.5, 2)) + 0.9 * exp(-pow(($h - 19.0) / 1.8, 2));
    $f = max(0, $f + wander($id, $ts, 600) + jitter(0.05));
    $flow = max(0, PEAK_FLOW * (0.8 + ($id % 3) * 0.1) * min(1, $f));     // m³/h
    $vel  = $flow / 3600 / PIPE_AREA;                                      // m/s

    $st['pos'] += $flow * $dt / 3600;                                      // m³

    return [
        "Flow" => round($flow, 2),
        "Velocity" => round($vel, 2),
        "Positive Cumulative" => round($st['pos'], 2),
        "Negative Cumulative" => round($st['neg'], 2),
    ];
}

function post($url, $payload)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 10,
    ]);
    $resp = curl_exec($ch);
    $err = curl_errno($ch) ? curl_error($ch) : null;
    curl_close($ch);
    return [$resp, $err];
}

while (true) {
    $now = time();
    $meters = loadMeters($conn, $only);
    if (!$meters) echo "No active meters found" . PHP_EOL;

    foreach ($meters as $m) {
        $id = (int)$m['id'];
        $isWater = (int)$m['meter_type_id'] === 2;

        if (!isset($state[$id])) {
            $state[$id] = $isWater
                ? ['pos' => lastValue($conn, $id, 'Positive Cumulative', 0.0), 'neg' => lastValue($conn, $id, 'Negative Cumulative', 0.0)]
                : ['kWh' => lastValue($conn, $id, 'kWh', 1000.0), 'kVAh' => lastValue($conn, $id, 'kVAh', 1100.0), 'kVARh' => lastValue($conn, $id, 'kVARh', 400.0)];
            $last[$id] = $now - $interval;
        }
        $dt = max(1, $now - $last[$id]);
        $last[$id] = $now;

        $values = $isWater ? simulateWater($id, $now, $dt, $state[$id]) : simulateElectric($id, $now, $dt, $state[$id]);
        [$resp, $err] = post($api_url, ["meter_id" => $id, "datetime" => date("Y-m-d H:i:s", $now), "data" => $values]);

        $summary = $isWater
            ? sprintf("Flow %.2f m3/h", $values['Flow'])
            : sprintf("%.2f kW, PF %.2f, %.1f A", $values['kW'], $values['Pf'], $values['Current avg']);
        echo $err ? "❌ #$id {$m['name']}: $err" : "✅ #$id {$m['name']} | $summary | $resp";
        echo PHP_EOL;
    }

    if (isset($opts['once'])) break;
    sleep($interval);
}
