<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";

$room_id = (int)($_POST['room_id'] ?? 0);
$existing_user_id = (int)($_POST['user_id'] ?? 0); // ถ้าเลือกผู้เช่าที่มีอยู่แล้ว
$full_name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$id_card = trim($_POST['id_card'] ?? '');
$address = trim($_POST['address'] ?? '');
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

if (!$room_id || !$start_date || !$end_date) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// ห้ามมีสัญญา active ซ้อนกันในห้องเดียวกัน
$chk = $conn->prepare("SELECT id FROM contract WHERE room_id = ? AND status = 'active'");
$chk->bind_param('i', $room_id);
$chk->execute();
if ($chk->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'ห้องนี้มีผู้เช่าที่มีสัญญา active อยู่แล้ว']);
    exit;
}
$chk->close();

if ($existing_user_id) {
    $user_id = $existing_user_id;
} else {
    if ($full_name === '') {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกชื่อผู้เช่า']);
        exit;
    }

    // สร้าง username จากเบอร์โทร (หรือสุ่มถ้าไม่มี) แล้วเช็คไม่ให้ซ้ำ
    $base = $phone !== '' ? preg_replace('/[^0-9]/', '', $phone) : 'tenant';
    $username = $base;
    $suffix = 0;
    do {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->bind_param('s', $username);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->num_rows > 0;
        $checkStmt->close();
        if ($exists) {
            $suffix++;
            $username = $base . $suffix;
        }
    } while ($exists);

    $randomPassword = bin2hex(random_bytes(6));
    $passwordHash = password_hash($randomPassword, PASSWORD_DEFAULT);

    $ustmt = $conn->prepare(
        "INSERT INTO users (username, full_name, phone, email, password, id_card, address, is_admin, is_deleted)
         VALUES (?, ?, ?, ?, ?, ?, ?, '0', 0)"
    );
    $ustmt->bind_param('sssssss', $username, $full_name, $phone, $email, $passwordHash, $id_card, $address);
    if (!$ustmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถสร้างข้อมูลผู้เช่าได้']);
        exit;
    }
    $user_id = $ustmt->insert_id;
    $ustmt->close();
}

$cstmt = $conn->prepare(
    "INSERT INTO contract (user_id, room_id, start_date, end_date, status, movein_date)
     VALUES (?, ?, ?, ?, 'active', ?)"
);
$cstmt->bind_param('iisss', $user_id, $room_id, $start_date, $end_date, $start_date);
if ($cstmt->execute()) {
    $conn->query("UPDATE rooms SET status = 'occupied' WHERE id = " . (int)$room_id);
    echo json_encode(['success' => true, 'message' => 'เพิ่มผู้เช่าและสัญญาสำเร็จ', 'user_id' => $user_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถสร้างสัญญาได้']);
}

$cstmt->close();
$conn->close();
