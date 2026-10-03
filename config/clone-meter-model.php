<?php
// Duplicate a model together with its register map.
//
// Most new models are a variant of one already entered — a different size in
// the same family, or the same meter behind a gateway that shifts addresses.
// Cloning then editing a few rows beats re-entering twenty by hand.
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

$source_id = (int) ($_POST['id'] ?? 0);
$name      = trim($_POST['name'] ?? '');

if ($source_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรุ่นมิเตอร์นี้']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM meter_model WHERE id = ? AND is_deleted = 0");
$stmt->bind_param("i", $source_id);
$stmt->execute();
$source = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$source) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรุ่นมิเตอร์นี้']);
    exit;
}

if ($name === '') {
    $name = $source['name'] . ' (copy)';
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare(
        "INSERT INTO meter_model (name, brand, meter_type_id, note, is_deleted) VALUES (?, ?, ?, ?, 0)"
    );
    $stmt->bind_param("ssis", $name, $source['brand'], $source['meter_type_id'], $source['note']);
    $stmt->execute();
    $new_id = $conn->insert_id;
    $stmt->close();

    $stmt = $conn->prepare(
        "INSERT INTO register_map (model_id, data_type_id, register, word_count, function_code, encoding, scale, is_deleted)
         SELECT ?, data_type_id, register, word_count, function_code, encoding, scale, 0
         FROM register_map WHERE model_id = ? AND is_deleted = 0"
    );
    $stmt->bind_param("ii", $new_id, $source_id);
    $stmt->execute();
    $copied = $stmt->affected_rows;
    $stmt->close();

    $conn->commit();
    echo json_encode([
        'success'  => true,
        'id'       => $new_id,
        'copied'   => $copied,
        'message'  => 'คัดลอกสำเร็จ',
    ]);
} catch (\Throwable $th) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถคัดลอกได้', 'output' => $th->getMessage()]);
}

$conn->close();
