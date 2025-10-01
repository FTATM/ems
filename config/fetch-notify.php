<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/no-crash.php";
include "../config/connect.php";



$sql = "SELECT * FROM notify WHERE is_deleted = 0";
$result = $conn->query($sql);

$rows = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}


// ส่งข้อมูลกลับในรูปแบบ JSON
header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => $rows]);

// ปิดการเชื่อมต่อ
$conn->close();
