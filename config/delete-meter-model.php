<?php
// Soft-delete a meter model.
//
// Refused while meters still reference it: silently unassigning them would
// stop those meters being polled with nothing on screen to explain why.
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรุ่นมิเตอร์นี้']);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) AS n FROM meter WHERE model_id = ? AND is_deleted = 0");
$stmt->bind_param("i", $id);
$stmt->execute();
$in_use = (int) $stmt->get_result()->fetch_assoc()['n'];
$stmt->close();

if ($in_use > 0) {
    echo json_encode([
        'success'   => false,
        'message'   => 'ยังมีมิเตอร์ใช้รุ่นนี้อยู่',
        'meter_count' => $in_use,
    ]);
    exit;
}

$stmt = $conn->prepare("UPDATE meter_model SET is_deleted = 1 WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'ลบสำเร็จ']);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบได้']);
}

$stmt->close();
$conn->close();
