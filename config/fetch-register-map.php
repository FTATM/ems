<?php
// A model's register map, plus the data_type rows that are valid for its
// meter type — so the editor can offer the right measurements to map.
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

$model_id = (int) ($_GET['model_id'] ?? $_POST['model_id'] ?? 0);
if ($model_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรุ่นมิเตอร์นี้']);
    exit;
}

$stmt = $conn->prepare(
    "SELECT mm.id, mm.name, mm.brand, mm.meter_type_id, mm.note, mt.name AS meter_type_name
     FROM meter_model mm JOIN meter_type mt ON mt.id = mm.meter_type_id
     WHERE mm.id = ? AND mm.is_deleted = 0"
);
$stmt->bind_param("i", $model_id);
$stmt->execute();
$model = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$model) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรุ่นมิเตอร์นี้']);
    exit;
}
$model['id'] = (int) $model['id'];
$model['meter_type_id'] = (int) $model['meter_type_id'];

$stmt = $conn->prepare(
    "SELECT rm.id, rm.data_type_id, dt.name AS data_type_name, rm.register,
            rm.word_count, rm.function_code, rm.encoding, rm.scale
     FROM register_map rm
     JOIN data_type dt ON dt.id = rm.data_type_id
     WHERE rm.model_id = ? AND rm.is_deleted = 0
     ORDER BY rm.register"
);
$stmt->bind_param("i", $model_id);
$stmt->execute();
$result = $stmt->get_result();

$registers = [];
while ($row = $result->fetch_assoc()) {
    $registers[] = [
        'id'             => (int) $row['id'],
        'data_type_id'   => (int) $row['data_type_id'],
        'data_type_name' => $row['data_type_name'],
        'register'       => (int) $row['register'],
        'word_count'     => (int) $row['word_count'],
        'function_code'  => (int) $row['function_code'],
        'encoding'       => $row['encoding'],
        'scale'          => (float) $row['scale'],
    ];
}
$stmt->close();

// Every measurement the app knows about. The editor filters this itself rather
// than the server guessing which ones suit a given meter type — a water meter
// that also reports temperature shouldn't be blocked from mapping it.
$data_types = [];
$result = $conn->query("SELECT id, name FROM data_type WHERE is_deleted = 0 ORDER BY id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data_types[] = ['id' => (int) $row['id'], 'name' => $row['name']];
    }
}

echo json_encode([
    'success'    => true,
    'model'      => $model,
    'registers'  => $registers,
    'data_types' => $data_types,
]);

$conn->close();
