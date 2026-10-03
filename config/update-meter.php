<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";


$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบมิเตอร์นี้']);
    exit;
}

// Only the fields actually submitted are updated. The form disables the
// connection fields belonging to the protocol that isn't selected, so editing
// a TCP meter no longer blanks out its RS485 settings (and vice versa) — and a
// field that simply isn't present must not be written as NULL into a NOT NULL
// column.
// Bind type 'n' is a nullable int: an empty value is stored as NULL instead of
// 0, which model_id needs — 0 is not a real meter_model row and would trip the
// foreign key.
//   POST field  =>  [column, bind type]
$fields = [
    'name'            => ['name',          's'],
    'group_id'        => ['group_id',      'i'],
    'meter_type_id'   => ['meter_type_id', 'i'],
    'model_id'        => ['model_id',      'n'],
    'select_protocol' => ['protocol',      's'],
    'dns'             => ['dns',           's'],
    'port'            => ['port',          'i'],
    'ip'              => ['ip_address',    's'],
    'submask'         => ['submask',       's'],
    'serialport'      => ['serial_port',   's'],
    'buad_rate'       => ['buad_rate',     's'],
    'data_bits'       => ['data_bits',     'i'],
    'parily'          => ['parily',        's'],
    'stop_bits'       => ['stop_bits',     'i'],
    'slave_id'        => ['slave_id',      'i'],
    'address'         => ['address',       's'],
    'quality'         => ['quality',       'i'],
    'x'               => ['x',             'i'],
    'y'               => ['y',             'i'],
    'z'               => ['z',             'i'],
];

$set = [];
$types = '';
$values = [];

foreach ($fields as $post_key => [$column, $type]) {
    if (!isset($_POST[$post_key])) {
        continue;
    }
    $raw = $_POST[$post_key];
    $set[] = "$column = ?";

    if ($type === 'n') {
        $types .= 'i';
        $values[] = ($raw === '' || $raw === null) ? null : (int) $raw;
    } elseif ($type === 'i') {
        $types .= 'i';
        $values[] = (int) $raw;
    } else {
        $types .= 's';
        $values[] = $raw;
    }
}

// An unchecked checkbox isn't posted at all, so is_active is always written
// rather than being treated as "absent". Without the ?? this raised an
// undefined-index warning which, with display_errors on above, was printed
// ahead of the JSON body and broke res.json() in the browser.
$set[] = "is_active = ?";
$types .= 'i';
$values[] = ($_POST['is_active'] ?? '') == "on" ? 1 : 0;

$types .= 'i';
$values[] = (int) $id;

$stmt = $conn->prepare("UPDATE meter SET " . implode(', ', $set) . " WHERE id = ?");
$stmt->bind_param($types, ...$values);
// ทำการ execute
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'อัปเดตข้อมูลสำเร็จ']);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตได้']);
}

$stmt->close();
$conn->close();
