<?php
// Health of the collector service, for the badge on the meter management page.
//
// "Is it running?" is inferred from the heartbeat the service writes at the end
// of every cycle: if the last cycle is more recent than a few poll intervals,
// it is alive. That avoids having PHP inspect the process list, which would not
// work once the collector runs as a Windows service under another account.
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

// Keep in step with EMS_POLL_INTERVAL; a couple of missed cycles is normal on a
// busy RS485 bus, so only call it down after three.
$poll_interval = (int) ($_ENV['EMS_POLL_INTERVAL'] ?? 60);
$stale_after = $poll_interval * 3;

// Age is computed by MySQL, not by PHP: the heartbeat is written with the
// database server's clock, and PHP's default timezone here is not the same one,
// so a PHP-side time() - strtotime() comparison came out hours adrift.
$status = null;
$result = $conn->query(
    "SELECT *, TIMESTAMPDIFF(SECOND, last_cycle_at, NOW()) AS seconds_since_cycle
     FROM collector_status WHERE id = 1"
);
if ($result && $result->num_rows > 0) {
    $status = $result->fetch_assoc();
}

$seconds_since_cycle = null;
$is_running = false;

if ($status && $status['seconds_since_cycle'] !== null) {
    $seconds_since_cycle = (int) $status['seconds_since_cycle'];
    $is_running = $seconds_since_cycle <= $stale_after;
}

// Meters whose most recent poll failed outright.
$failing = [];
$sql = "SELECT mh.meter_id, m.name, mh.last_error, mh.last_error_at, mh.consecutive_failures
        FROM meter_health mh
        JOIN meter m ON m.id = mh.meter_id
        WHERE mh.consecutive_failures > 0 AND m.is_deleted = 0 AND m.is_active = 1
        ORDER BY mh.consecutive_failures DESC, mh.meter_id";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['meter_id'] = (int) $row['meter_id'];
        $row['consecutive_failures'] = (int) $row['consecutive_failures'];
        $failing[] = $row;
    }
}

// Per-meter health for the dots in the meter list.
$health = [];
$result = $conn->query("SELECT meter_id, last_ok_at, last_error_at, last_error, consecutive_failures FROM meter_health");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $health[$row['meter_id']] = [
            'last_ok_at'           => $row['last_ok_at'],
            'last_error_at'        => $row['last_error_at'],
            'last_error'           => $row['last_error'],
            'consecutive_failures' => (int) $row['consecutive_failures'],
        ];
    }
}

echo json_encode([
    'success'              => true,
    'is_running'           => $is_running,
    'poll_interval'        => $poll_interval,
    'seconds_since_cycle'  => $seconds_since_cycle,
    'started_at'           => $status['started_at'] ?? null,
    'last_cycle_at'        => $status['last_cycle_at'] ?? null,
    'last_cycle_ms'        => isset($status['last_cycle_ms']) ? (int) $status['last_cycle_ms'] : null,
    'meters_ok'            => isset($status['meters_ok']) ? (int) $status['meters_ok'] : 0,
    'meters_failed'        => isset($status['meters_failed']) ? (int) $status['meters_failed'] : 0,
    'host'                 => $status['host'] ?? null,
    'failing'              => $failing,
    'health'               => $health,
]);

$conn->close();
