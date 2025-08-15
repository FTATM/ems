<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";

//ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    echo json_encode(["success" => false, "message" => "ไม่สามารถเชื่อมต่อฐานข้อมูลได้"]);
    exit;
}

// ใช้ Ternary operator
$id = $_POST['id'] ?? null;
$value = $_POST['value'] ?? ''; 
// $status = $_POST['status'] ?? ''; 


if (!$id || !$value) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$fields = ['full_name', 'phone', 'address'];
if (in_array($_POST['action'], $fields)) {
    $sql = "UPDATE users SET {$_POST['action']} = ? WHERE id = ?" ;
    $stmt = $conn->prepare($sql); 
    if ($stmt) {
        $stmt->bind_param("si", $value, $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'ดำเนินการสำเร็จ']); 

    }else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการดำเนินการ']); 
    }
    $stmt->close(); 
    }else {
    echo json_encode(['success' => false, 'message' => 'SQL Error']);
    }

}else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการดำเนินการ']);
}

$conn->close();
