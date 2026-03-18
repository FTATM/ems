<?php 

session_start();
session_unset();
session_destroy();
// ส่ง query string ไป login.php เพื่อให้ JS แสดง alert
header("Location: ../pages/login.php?logout=success");
exit();