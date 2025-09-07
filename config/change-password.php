<?php
include '../components/session.php';
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

// ตรวจสอบสิทธิ์ (ต้องเป็น admin เท่านั้น)

if (!isset($_SESSION['user']) || $_SESSION['is_admin'] == 0) {
    http_response_code(403);
    echo json_encode(["success" => false, "error" => "Permission denied"]);
    exit;
}


$user_id = $_POST['user_id'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';


// ตรวจสอบความถูกต้องของรหัสผ่านใหม่
if ($new_password !== $confirm_password) {
    echo json_encode(["success" => false, "error" => "New password and confirmation do not match"]);
    exit;
}

// อัปเดตรหัสผ่านใหม่ (admin ไม่ต้องเช็ค old password)
$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $new_password_hash, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Password changed successfully"]);
} else {
    echo json_encode(["success" => false, "error" => "Database error"]);
}
