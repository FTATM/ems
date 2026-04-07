<?php
ini_set('display_errors', 0);
mysqli_report(MYSQLI_REPORT_OFF);
require_once __DIR__ . '/config.php';
require_once '../components/serial.php';
if (!checktoken()) {
    header("../pages/notallow.php");
}

$servername = $db_config['host'] ?? '127.0.0.1';
$database = $db_config['name'] ?? "database";
$port = $db_config['port'] ?? 3306;
$username = $db_config['user'] ?? "root";
$password = $db_config['pass'] ?? "";


$conn = new mysqli($servername, $username, $password, $database, $port);


// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Not found server please contact Administrators.']);
    exit;
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
