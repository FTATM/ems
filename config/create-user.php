<?php
include '../config/connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $id_card = $_POST['id_card'];
    $address = $_POST['address'];

    // ตรวจสอบ username ซ้ำ
    $checkStmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $checkStmt->bind_param('s', $username);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        $checkStmt->close();
        echo json_encode(['success' => false, 'message' => 'Username นี้ถูกใช้แล้ว']);
        exit;
    }
    $checkStmt->close();

    // เพิ่มข้อมูล
    $insertStmt = $conn->prepare("INSERT INTO users (username, full_name, phone, email, password, id_card, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insertStmt->bind_param("sssssss", $username, $full_name, $phone, $email, $password, $id_card, $address);
    if ($insertStmt->execute()) {
        $insertStmt->close();
        echo json_encode(['success' => true]);
        exit();
    } else {
        $errorMsg = $insertStmt->error;
        $insertStmt->close();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $errorMsg]);
        exit();
    }
}