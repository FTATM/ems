<?php
ini_set('display_errors', 0);
mysqli_report(MYSQLI_REPORT_OFF);

require_once '../components/serial.php';
if (!checktoken()) {
    header("../pages/notallow.php");
}

// ข้อมูลการเชื่อมต่อฐานข้อมูล server 
// $servername = "49.0.69.152";
// $username = "php_user";
// $password = "123456";
// $database = "ams";


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
