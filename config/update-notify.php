<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";



// รับค่าจาก POST
$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? '';

if (!$status) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

switch ($status) {
    case "active":
        $is_active = $_POST['value'] ?? '';
        $stmt = $conn->prepare("UPDATE notify SET is_active = ? WHERE id = ?");
        $stmt->bind_param("ii", $is_active, $id);
        break;

    case "update":
        $mark = $_POST['mark'] ?? '';
        $value = $_POST['value'] ?? 0.00;
        $token = $_POST['token'] ?? '';
        $email = $_POST['email'] ?? '';
        $stmt = $conn->prepare("UPDATE notify SET mark = ?, value_condition = ?, token_line = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sdssi", $mark, $value, $token, $email, $id);
        break;

    case "create":
        $meter_id = $_POST['meter_id'] ?? '';
        $name = $_POST['name'] ?? '';
        $mark = $_POST['mark'] ?? '';
        $value = $_POST['value'] ?? 0.00;
        $token = $_POST['token'] ?? '';
        $email = $_POST['email'] ?? '';
        $stmt = $conn->prepare("INSERT INTO notify (meter_id,name, mark, value_condition, token_line, email) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("issdss", $meter_id, $name, $mark, $value, $token, $email);
        break;
    case "delete":
        $stmt = $conn->prepare("UPDATE notify SET is_deleted = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'สถานะไม่ถูกต้อง']);
        exit;
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => "อัปเดตข้อมูล [$id] สำเร็จ"]);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตได้']);
}

$stmt->close();
$conn->close();
