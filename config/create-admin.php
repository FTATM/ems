<?php 

include "../config/no-crash.php"; 
include "../config/connect.php"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // บังคับให้เป็น Admin เสมอ
    $is_admin = 1;  

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new admin into the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $email, $hashed_password, $is_admin);

    if ($stmt->execute()) {
        // Redirect or display success message
        echo json_encode(["success" => true]);
        header("Location: ../pages/adminList.php");
        exit;
    } else {
        // Handle error
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
}
