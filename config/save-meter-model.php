<?php
// Create or update a meter model (device profile).
// POST id (omit to create), name, brand, meter_type_id, note
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

$id            = (int) ($_POST['id'] ?? 0);
$name          = trim($_POST['name'] ?? '');
$brand         = trim($_POST['brand'] ?? '');
$meter_type_id = (int) ($_POST['meter_type_id'] ?? 0);
$note          = trim($_POST['note'] ?? '');

if ($name === '' || $meter_type_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

if ($id > 0) {
    $stmt = $conn->prepare(
        "UPDATE meter_model SET name = ?, brand = ?, meter_type_id = ?, note = ? WHERE id = ?"
    );
    $stmt->bind_param("ssisi", $name, $brand, $meter_type_id, $note, $id);
} else {
    $stmt = $conn->prepare(
        "INSERT INTO meter_model (name, brand, meter_type_id, note, is_deleted) VALUES (?, ?, ?, ?, 0)"
    );
    $stmt->bind_param("ssis", $name, $brand, $meter_type_id, $note);
}

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'id'      => $id > 0 ? $id : $conn->insert_id,
        'message' => 'บันทึกสำเร็จ',
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกได้']);
}

$stmt->close();
$conn->close();
