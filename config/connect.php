<?php

// ข้อมูลการเชื่อมต่อฐานข้อมูล
$servername = "49.0.69.152";
$username = "php_user";
$password = "123456";
$database = "ams";

// $servername = "localhost";
// $username = "root";
// $password = "";
// $database = "ams";
$conn = new mysqli($servername, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "ams";
    $conn = new mysqli($servername, $username, $password, $database);
}
