<?php
// require_once '../config/error_log.php';
// require_once __DIR__ . '/error_log.php';
// ข้อมูลการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$database = "ams";

$conn = new mysqli($servername, $username, $password, $database);
// // ถ้าไม่สามารถเชื่อมต่อฐานข้อมูล
// if (!$conn) {
//     log_error("ไม่สามารถเชื่อมต่อฐานข้อมูล: " . mysqli_connect_error(), 'database');
// } 
