<?php
set_time_limit(300);
date_default_timezone_set('Asia/Bangkok');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/no-crash.php";
include "../config/connect.php";
$conn->query("SET time_zone = '+07:00'");

$start = microtime(true);

// ── scope: 'electrical' (meter_type_id=1) หรือ 'water' (meter_type_id=2) ──
$scope = ($_POST['scope'] ?? $_GET['scope'] ?? 'electrical') === 'water' ? 'water' : 'electrical';
$meterTypeId = $scope === 'water' ? 2 : 1;

// ── ระยะห่างระหว่างรอบอ่านค่า (วินาที) — ผู้ใช้กำหนดได้ (เช่น 5, 30, 60) ──
$intervalSeconds = (int)($_POST['interval'] ?? $_GET['interval'] ?? 60);
if ($intervalSeconds < 1) $intervalSeconds = 60;

// ── หา "รอบการอ่านค่าจริง" = ชุดแถวที่มี meter_id + create_date ตรงกัน
//    (เขียนพร้อมกันในรอบเดียวจากมิเตอร์ตัวนั้น) แทนการนับทีละ 18 แถวแบบเดิม
//    ซึ่งใช้ไม่ได้อีกต่อไปหลังจากมีชนิดข้อมูลของมิเตอร์น้ำ (4 ชนิด) เพิ่มเข้ามา ──
$sql = "SELECT md.meter_id, md.create_date
        FROM meter_data md
        INNER JOIN meter m ON m.id = md.meter_id
        WHERE m.meter_type_id = ?
        GROUP BY md.meter_id, md.create_date
        ORDER BY md.meter_id ASC, md.create_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $meterTypeId);
$stmt->execute();
$result = $stmt->get_result();

$momentsByMeter = [];
while ($row = $result->fetch_assoc()) {
    $momentsByMeter[$row['meter_id']][] = $row['create_date'];
}
$stmt->close();

if (empty($momentsByMeter)) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลมิเตอร์ในกลุ่มนี้']);
    exit;
}

// ── คำนวณ timestamp ใหม่ต่อมิเตอร์: รอบล่าสุด = ตอนนี้ ย้อนหลังทีละ interval ──
$now = new DateTime('now');
$updates = []; // [['meter_id'=>, 'old_ts'=>, 'new_ts'=>], ...]

foreach ($momentsByMeter as $meterId => $timestamps) {
    $count = count($timestamps);
    foreach ($timestamps as $i => $oldTs) {
        $secondsAgo = ($count - 1 - $i) * $intervalSeconds;
        $newTime = (clone $now)->modify("-{$secondsAgo} seconds");
        $newTs = $newTime->format('Y-m-d H:i:s');
        if ($newTs !== $oldTs) {
            $updates[] = ['meter_id' => (int)$meterId, 'old_ts' => $oldTs, 'new_ts' => $newTs];
        }
    }
}

// ── อัปเดตเป็นชุด (batched CASE WHEN) เพื่อความเร็ว แทนที่จะยิงทีละ query ──
$updatedRows = 0;
$batches = array_chunk($updates, 150);

foreach ($batches as $batch) {
    $caseSql = "UPDATE meter_data SET create_date = CASE ";
    $whereParts = [];
    foreach ($batch as $u) {
        $meterId = $u['meter_id'];
        $oldTs = $conn->real_escape_string($u['old_ts']);
        $newTs = $conn->real_escape_string($u['new_ts']);
        $caseSql .= "WHEN meter_id = {$meterId} AND create_date = '{$oldTs}' THEN '{$newTs}' ";
        $whereParts[] = "(meter_id = {$meterId} AND create_date = '{$oldTs}')";
    }
    $caseSql .= "ELSE create_date END WHERE " . implode(' OR ', $whereParts);
    $conn->query($caseSql);
    $updatedRows += $conn->affected_rows;
}

$duration = microtime(true) - $start;

echo json_encode([
    'success' => true,
    'message' => ($scope === 'water' ? 'อัปเดตเวลาข้อมูลมิเตอร์น้ำสำเร็จ' : 'อัปเดตเวลาข้อมูลมิเตอร์ไฟฟ้าสำเร็จ'),
    'scope' => $scope,
    'interval' => $intervalSeconds,
    'moments' => count($updates),
    'updated_rows' => $updatedRows,
    'duration' => round($duration, 4),
]);

$conn->close();
