<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";

$id = (int)($_POST['id'] ?? 0);
$end_date = $_POST['end_date'] ?? '';
$status = $_POST['status'] ?? '';

if (!$id || !$end_date || !$status) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$stmt = $conn->prepare("SELECT room_id FROM contract WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$contract = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$contract) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบสัญญานี้']);
    exit;
}

$ustmt = $conn->prepare("UPDATE contract SET end_date = ?, status = ? WHERE id = ?");
$ustmt->bind_param('ssi', $end_date, $status, $id);

if ($ustmt->execute()) {
    // ถ้าสิ้นสุดสัญญาแล้ว และห้องไม่มีสัญญา active อื่นค้างอยู่ ให้ปรับสถานะห้องกลับเป็นว่าง
    if ($status !== 'active') {
        $room_id = (int)$contract['room_id'];
        $chk = $conn->prepare("SELECT id FROM contract WHERE room_id = ? AND status = 'active'");
        $chk->bind_param('i', $room_id);
        $chk->execute();
        if ($chk->get_result()->num_rows === 0) {
            $conn->query("UPDATE rooms SET status = 'empty' WHERE id = " . $room_id);
        }
        $chk->close();
    }
    echo json_encode(['success' => true, 'message' => 'อัปเดตสัญญาสำเร็จ']);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตได้']);
}

$ustmt->close();
$conn->close();
