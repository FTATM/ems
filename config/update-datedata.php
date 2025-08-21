<?php
set_time_limit(300);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/no-crash.php";
include "../config/connect.php";



// 1. ดึงข้อมูลทั้งหมดเรียงตาม create_date
$sql = "SELECT id, create_date FROM meter_data";
$result = $conn->query($sql);

$rows = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    // ใช้เวลาเริ่มต้นจากแถวแรก
    $baseTime = new DateTime($rows[0]['create_date']);

    foreach ($rows as $index => $row) {
        // คำนวณกลุ่ม (ทุก 18 แถวถือเป็น 1 กลุ่ม)
        $group = floor($index / 18);
        $newTime = clone $baseTime;
        $newTime->modify("+" . ($group * 30) . " seconds");
        $newTimeStr = $newTime->format('Y-m-d H:i:s');

        // เตรียมและอัปเดตกลับไปยังฐานข้อมูล
        $id = $row['id'];
        $updateSql = "UPDATE meter_data SET create_date = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("si", $newTimeStr, $id);
        $stmt->execute();

        echo "Updated ID {$id} → {$newTimeStr}<br>";
    }
} else {
    echo "ไม่พบข้อมูลในตาราง meter";
}

$json = ['status' => true, 'message' => 'success'];

// ส่งข้อมูลกลับในรูปแบบ JSON
header('Content-Type: application/json');
echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// ปิดการเชื่อมต่อ
$conn->close();
