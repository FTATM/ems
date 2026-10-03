<?php
// Replace a model's whole register map in one transaction.
//
// The editor posts the complete list, so rows the user removed simply aren't
// in it. Doing this as one atomic replace avoids a half-saved map: a model
// left with kW but no kWh would silently poll incomplete data.
//
// POST model_id, registers = JSON array of
//   {data_type_id, register, word_count, function_code, encoding, scale}
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

$model_id = (int) ($_POST['model_id'] ?? 0);
$payload  = $_POST['registers'] ?? '[]';

if ($model_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรุ่นมิเตอร์นี้']);
    exit;
}

$registers = json_decode($payload, true);
if (!is_array($registers)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูล register ไม่ถูกต้อง']);
    exit;
}

$allowed_encodings = ['float32_be', 'float32_le', 'uint16', 'int16', 'uint32_be'];

// Validate everything before touching the table: a model that ends up half
// mapped is worse than one that rejected the save outright.
$clean = [];
$seen_types = [];
foreach ($registers as $i => $row) {
    $data_type_id  = (int) ($row['data_type_id'] ?? 0);
    $register      = (int) ($row['register'] ?? -1);
    $word_count    = (int) ($row['word_count'] ?? 2);
    $function_code = (int) ($row['function_code'] ?? 3);
    $encoding      = (string) ($row['encoding'] ?? 'float32_be');
    $scale         = (float) ($row['scale'] ?? 1);

    if ($data_type_id <= 0 || $register < 0) {
        echo json_encode(['success' => false, 'message' => "แถวที่ " . ($i + 1) . ": ข้อมูลไม่ครบ"]);
        exit;
    }
    if (isset($seen_types[$data_type_id])) {
        echo json_encode(['success' => false, 'message' => "แถวที่ " . ($i + 1) . ": ค่านี้ถูกกำหนดซ้ำ"]);
        exit;
    }
    if (!in_array($encoding, $allowed_encodings, true)) {
        echo json_encode(['success' => false, 'message' => "แถวที่ " . ($i + 1) . ": encoding ไม่ถูกต้อง"]);
        exit;
    }
    if (!in_array($function_code, [3, 4], true)) {
        echo json_encode(['success' => false, 'message' => "แถวที่ " . ($i + 1) . ": function code ไม่ถูกต้อง"]);
        exit;
    }
    if ($word_count < 1 || $word_count > 4) {
        echo json_encode(['success' => false, 'message' => "แถวที่ " . ($i + 1) . ": word count ต้องอยู่ระหว่าง 1-4"]);
        exit;
    }

    $seen_types[$data_type_id] = true;
    $clean[] = [$data_type_id, $register, $word_count, $function_code, $encoding, $scale];
}

$conn->begin_transaction();

try {
    // Hard delete rather than is_deleted = 1: uq_model_data would otherwise
    // collide when the same measurement is re-added later.
    $stmt = $conn->prepare("DELETE FROM register_map WHERE model_id = ?");
    $stmt->bind_param("i", $model_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare(
        "INSERT INTO register_map
            (model_id, data_type_id, register, word_count, function_code, encoding, scale, is_deleted)
         VALUES (?, ?, ?, ?, ?, ?, ?, 0)"
    );
    foreach ($clean as $row) {
        [$data_type_id, $register, $word_count, $function_code, $encoding, $scale] = $row;
        $stmt->bind_param(
            "iiiiisd",
            $model_id, $data_type_id, $register, $word_count, $function_code, $encoding, $scale
        );
        $stmt->execute();
    }
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'saved' => count($clean), 'message' => 'บันทึกสำเร็จ']);
} catch (\Throwable $th) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกได้', 'output' => $th->getMessage()]);
}

$conn->close();
