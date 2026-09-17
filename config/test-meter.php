<?php
// Test a meter's connection using the same code the collector service runs.
//
// Replaces config/tcp.php + config/rs485.php, which interpolated 5-9 raw
// $_POST values straight into shell_exec (trivially injectable) and were the
// only endpoints in config/ that skipped connect.php's auth check. They also
// each drove a *separate* copy of the Modbus logic that had drifted from the
// poller's, so a meter could test green and never actually be polled — or,
// more often, fail the test while the poller read it fine.
//
// Only the meter id crosses the boundary now; Python reads the connection
// settings from the meter row itself. That means the meter must be saved
// before it can be tested, which is what the UI already told people to do.
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

$meter_id = (int) ($_POST['meter_id'] ?? 0);

if ($meter_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid meter id.",
        "output"  => "Save the meter before testing its connection."
    ]);
    exit;
}

// Run from connector/ so `python -m ems` resolves the package. Under mod_php
// the CWD is config/, but `php -S` serves from the repo root, so the path is
// built from __DIR__ rather than assumed.
$connector_dir = realpath(__DIR__ . '/../connector');

if ($connector_dir === false) {
    echo json_encode([
        "success" => false,
        "message" => "Collector not installed.",
        "output"  => "connector/ directory not found."
    ]);
    exit;
}

// Apache's PATH is not the desktop PATH, so a bare `python` usually is not
// found. EMS_PYTHON in .env names the interpreter that has pymodbus.
$python = $_ENV['EMS_PYTHON'] ?? 'python';

$base = 'cd /d ' . escapeshellarg($connector_dir)
      . ' && ' . escapeshellarg($python)
      . ' -m ems test --meter-id ' . escapeshellarg((string) $meter_id);

// stdout carries only the JSON result; the library's own log output goes to
// stderr, so it must be kept out of the way rather than merged in.
$cmd = $base . ' 2>NUL';

try {
    $response = shell_exec($cmd);
    $decoded = json_decode((string) $response, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($decoded['success'])) {
        echo json_encode($decoded);
    } else {
        // No JSON at all — usually python missing from Apache's PATH, or
        // pymodbus not installed for the interpreter Apache invokes. Re-run
        // with stderr attached so the reason reaches "Detail more...".
        $diagnostic = shell_exec($base . ' 2>&1');
        echo json_encode([
            "success" => false,
            "message" => "Invalid response from collector.",
            "output"  => trim((string) $diagnostic)
        ]);
    }
} catch (\Throwable $th) {
    echo json_encode([
        "success" => false,
        "message" => "PHP Exception",
        "output"  => $th->getMessage()
    ]);
}
