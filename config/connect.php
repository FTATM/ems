<?php
ini_set('display_errors', 0);
mysqli_report(MYSQLI_REPORT_OFF);

// require_once '../components/serial.php';
// if (!checktoken()) {
//     header("../pages/notallow.php");
// }

// run localhost 
// $servername = "192.168.1.105:3306";
// $username = "mysql";
// $password = "FTATM54164000";
// $database = "ams";

// $conn = new mysqli($servername, $username, $password, $database);
$servername = "49.0.69.152";
$username = "mysql";
$password = "FTATM54164000";
$database = "ams";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);


// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Not found server please contact Administrators.']);
    exit;
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);