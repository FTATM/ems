<?php
set_time_limit(300);
date_default_timezone_set('Asia/Bangkok');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ส่งข้อมูลกลับในรูปแบบ JSON

include "../config/no-crash.php";
include "../config/connect.php";


// ⏱ เริ่มจับเวลา
$start = microtime(true);

// 1. ดึงข้อมูลทั้งหมดเรียงตาม create_date
$sql = "SELECT id, create_date FROM meter_data ORDER BY id ASC";
$result = $conn->query($sql);

$rows = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $len = count($rows);
    $times = ceil($len / 18);
    $rowCount = 1;

    $baseTime = new DateTime('now');
    $baseTime->modify("-" . ($times * 60) . " second");
    $batches = array_chunk($rows, 180); // หั่น array เป็นก้อนละ 18

    foreach ($batches as $i => $batch) {
        $ids = array_column($batch, 'id');
        $idList = implode(",", $ids);

        $sql = "UPDATE meter_data SET create_date = CASE id ";
        foreach ($batch as $index => $row) {
            $group = floor($rowCount / 18); // ทุก 18 แถวเป็น 1 group
            $newTime = clone $baseTime;
            $newTime->modify("+" . ($group * 60) . " seconds");
            $newTimeStr = $newTime->format('Y-m-d H:i:s');
            $sql .= "WHEN {$row['id']} THEN '{$newTimeStr}' ";
            $rowCount++;
        }
        $sql .= "END WHERE id IN ($idList)";
        $conn->query($sql);
    }
    // ⏱ สิ้นสุดจับเวลา
    $end = microtime(true);
    $duration = $end - $start; // หน่วย: วินาที
    echo json_encode(['success' => true, 'message' => 'All data updated successfully.', 'time' => $baseTime, 'duration' => round($duration, 4)]);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลในตาราง meter']);
}

// ปิดการเชื่อมต่อ
$conn->close();
