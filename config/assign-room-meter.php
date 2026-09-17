<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";

$meter_id = (int)($_POST['meter_id'] ?? 0);
$room_id = $_POST['room_id'] ?? null; // '' or missing = unassign

if (!$meter_id) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

if ($room_id === '' || $room_id === null) {
    $stmt = $conn->prepare("UPDATE meter SET room_id = NULL WHERE id = ?");
    $stmt->bind_param('i', $meter_id);
} else {
    $room_id = (int)$room_id;

    // ตรวจสอบว่ามิเตอร์กับห้องอยู่กลุ่ม/ตึกเดียวกัน ก่อนผูก
    $chk = $conn->prepare(
        "SELECT m.id FROM meter m
         INNER JOIN rooms r ON r.building_id = m.group_id
         WHERE m.id = ? AND r.id = ?"
    );
    $chk->bind_param('ii', $meter_id, $room_id);
    $chk->execute();
    if ($chk->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'มิเตอร์นี้ไม่ได้อยู่ในกลุ่ม/ตึกเดียวกับห้องนี้']);
        exit;
    }
    $chk->close();

    $stmt = $conn->prepare("UPDATE meter SET room_id = ? WHERE id = ?");
    $stmt->bind_param('ii', $room_id, $meter_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'อัปเดตข้อมูลสำเร็จ']);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตได้']);
}

$stmt->close();
$conn->close();
