<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";




// รับค่าจาก POST
$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? '';
$value = $_POST['value'] ?? '';

if (!$id || !$action || $value === '') {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

switch ($action) {
    case "rename":
        $stmt = $conn->prepare("UPDATE buildings SET `name` = ? WHERE id = ?");
        $stmt->bind_param("si", $value, $id);
        break;

    case "delete":
        $stmt = $conn->prepare("UPDATE buildings SET is_deleted = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        break;

    case "new":
        $location_id = $_POST['location_id'] ?? '';
        $stmt = $conn->prepare("INSERT INTO buildings (name,location_id) VALUES (?,?)");
        $stmt->bind_param("ss", $value, $location_id);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'สถานะไม่ถูกต้อง']);
        exit;
}

// ทำการ execute
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'อัปเดตข้อมูลสำเร็จ']);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตได้']);
}

$stmt->close();
$conn->close();
