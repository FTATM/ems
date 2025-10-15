<?php
ini_set('display_errors', 0);
mysqli_report(MYSQLI_REPORT_OFF);

require_once '../components/serial.php';
if (!checktoken()) {
    header("../pages/notallow.php");
}

// run localhost 
$servername = "localhost";
$username = "root";
$password = "";
$database = "ams";

$conn = new mysqli($servername, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Not found server please contact Administrators.']);
    exit;
}
