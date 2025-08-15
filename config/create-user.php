<?php
include '../config/connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'] ;
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $id_card = $_POST['id_card'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO users (username, full_name, phone, email, password, id_card, address)
            VALUES (?, ?, ?, ?, ?, ?, ?)");

    // ตรวจสอบ username ซ้ำ
    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?") ;
    $stmt->bind_param('s', $username) ;

    if($stmt->execute() ) {
       $stmt->store_result();
       if($stmt->num_rows > 0) {
           echo json_encode(['success' => false, 'message' => 'Username นี้ถูกใช้แล้ว']);
           exit;
       }
       else {
         $stmt->close();
       }
    }

    $stmt->bind_param("sssssss", $username, $full_name, $phone, $email, $password, $id_card, $address);
    // เพิ่มข้อมูล


    
    if ($stmt->execute()) {
        // header("Location: ../pages/user.php");
        echo json_encode(['success' => true]);
        exit();
    } else {
        echo "Error" . $stmt->error;
    }

}