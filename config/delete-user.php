<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";

//ตรวจสอบการเชื่อมต่อ
if($_POST['action' === 'delete']) {
  $sql = "DELETE FROM users WHERE id = ?" ;
  $stmt = $conn->prepare($sql); 
  $stmt->bind_param('i', $_POST['id']); 
  if($stmt->execute()) {
      echo json_encode(['success' => true, 'message' => 'ลบข้อมูลสำเร็จ']);
  } else {
      echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล']);
  }
}