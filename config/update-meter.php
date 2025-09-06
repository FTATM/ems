<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";


// รับค่าจาก POST
$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? null;
$is_active = $_POST['is_active'] == "on" ? 1 : 0;
$room_id = $_POST['room_id'] ?? null;
$protocol = $_POST['select_protocol'] ?? null;
$dns = $_POST['dns'] ?? null;
$port = $_POST['port'] ?? null;
$ip = $_POST['ip'] ?? null;
$submask = $_POST['submask'] ?? null;
$serialport = $_POST['serialport'] ?? null;
$buad_rate = $_POST['buad_rate'] ?? null;
$data_bits = $_POST['data_bits'] ?? null;
$parily = $_POST['parily'] ?? null;
$stop_bits = $_POST['stop_bits'] ?? null;
$slave_id = $_POST['slave_ID'] ?? null;
$address = $_POST['address'] ?? null;
$quality = $_POST['quality'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบมิเตอร์นี้']);
    exit;
}


$stmt = $conn->prepare("UPDATE meter SET name = ?, is_active = ?, room_id = ?, protocol = ?, dns = ?, port = ?, ip_address = ?, submask = ?, serial_port = ?, buad_rate = ?, data_bits = ?, parily = ?, stop_bits = ?, slave_id = ?, address = ?, quality = ?  WHERE id = ?");
$stmt->bind_param( "siississsiiisiisi", $name, $is_active, $room_id, $protocol, $dns, $port, $ip, $submask, $serialport, $buad_rate, $data_bits, $parily, $stop_bits, $slave_id, $address, $quality, $id);
// ทำการ execute
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'อัปเดตข้อมูลสำเร็จ']);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตได้']);
}

$stmt->close();
$conn->close();
