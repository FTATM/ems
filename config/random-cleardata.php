<?php
require '../config/connect.php';
header('Content-Type: application/json');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    $select = $_POST['select'] ?? null;

    // เริ่ม transaction
    $conn->begin_transaction();
    if ($select == 'all') {
        $sql = "DELETE FROM device_realtime";
        $stmt = $conn->prepare($sql);
    } else {
        $sql = "DELETE FROM device_realtime WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
    }
    $stmt->execute();
    $conn->commit();

    echo json_encode(['status' => true, 'message' => 'success']);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode([ 'status' => false, 'message' => $e->getMessage() ]);
}
