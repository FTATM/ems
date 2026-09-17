<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";

// รับค่าจาก POST
$name = trim($_POST['name'] ?? '');
$is_active = ($_POST['is_active'] ?? '') == "on" ? 1 : 0;
$group_id = $_POST['group_id'] ?? null;
$meter_type_id = $_POST['meter_type_id'] ?? null;
// NULL, not 0: a meter with no model chosen yet has no register map, and 0 is
// not a real meter_model row.
$model_id = ($_POST['model_id'] ?? '') === '' ? null : (int) $_POST['model_id'];
$protocol = $_POST['select_protocol'] ?? 'tcp';
$dns = $_POST['dns'] ?? '';
$port = $_POST['port'] ?? 0;
$ip = $_POST['ip'] ?? '';
$submask = $_POST['submask'] ?? '255.255.255.0';
$serialport = $_POST['serialport'] ?? '';
$buad_rate = $_POST['buad_rate'] ?? '';
$data_bits = $_POST['data_bits'] ?? 0;
$parily = $_POST['parily'] ?? 'node';
$stop_bits = $_POST['stop_bits'] ?? 0;
$slave_id = $_POST['slave_id'] ?? 0;
$address = $_POST['address'] ?? '';
$quality = $_POST['quality'] ?? 0;
$x = $_POST['x'] ?? 0;
$y = $_POST['y'] ?? 0;
$z = $_POST['z'] ?? 0;

if (!$name || !$group_id || !$meter_type_id) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO meter
        (name, is_active, group_id, meter_type_id, model_id, protocol, dns, port, ip_address, submask,
         serial_port, buad_rate, data_bits, parily, stop_bits, slave_id, address, quality, x, y, z, is_deleted)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)"
);
$stmt->bind_param(
    "siiiississssisiisiiii",
    $name, $is_active, $group_id, $meter_type_id, $model_id, $protocol, $dns, $port, $ip, $submask,
    $serialport, $buad_rate, $data_bits, $parily, $stop_bits, $slave_id, $address, $quality, $x, $y, $z
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'เพิ่มมิเตอร์สำเร็จ', 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถเพิ่มมิเตอร์ได้']);
}

$stmt->close();
$conn->close();
