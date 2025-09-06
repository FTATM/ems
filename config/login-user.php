<?php
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');


// รองรับทั้ง fetch (JSON) และ form ปกติ (POST)
if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $data = json_decode(file_get_contents("php://input"), true);
    $login = $data['username'] ?? '';
    $password = $data['password'] ?? '';
} else {
    $login = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
}

if (!$login || !$password) {
    echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบ']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND (is_deleted = 0)");
$stmt->bind_param("ss", $login, $login); //can login with username or email 
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
    exit;
} else {
    //เมื่อ login === success ---> keep session  
    session_start();
    $_SESSION['user_id'] = $user['id'] ; 
    $_SESSION['username'] = $user['username'] ; 
    $_SESSION['password'] = $user['password'] ; 
    $_SESSION['is_admin'] = $user['is_admin'] ;
    $_SESSION['user'] = $user; 

    echo json_encode([
        'success'  => true,
        'message'  => 'เข้าสู่ระบบสำเร็จ',
        'username' => $_SESSION['username'],
        'is_admin' => $_SESSION['is_admin']
    ]);
    exit;
    
}

