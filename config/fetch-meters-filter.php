<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/no-crash.php";
include "../config/connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tid = $_POST['tid'] ?? 0;
    $gid = $_POST['gid'] ?? 0;

    if ($tid === 0 && $gid === 0) {
        echo json_encode(['success' => false, 'data' => [], 'message', 'Failed variable not found.']);
        exit;
    }

    $sqlmeter = "SELECT * FROM meter WHERE is_deleted = 0  AND is_active = 1 AND group_id = ? AND meter_type_id = ?";
    $stmt = $conn->prepare($sqlmeter);
    $stmt->bind_param('ii', $gid, $tid);
    $stmt->execute();
    $result = $stmt->get_result();
    $sql = "SELECT * FROM data_type WHERE is_deleted = 0";
    $column = $conn->query($sql);

    $meters = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $meters[] = $row;
        }
    }

    // ส่งข้อมูลกลับในรูปแบบ JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $meters, 'dataType' => $data_types]);

    // ปิดการเชื่อมต่อ
    $conn->close();
    exit;
}