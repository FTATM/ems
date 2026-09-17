<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/no-crash.php";
include "../config/connect.php";

$gid = (int)($_POST['gid'] ?? $_GET['gid'] ?? 0);

if ($gid > 0) {
    $sql = "SELECT * FROM rooms WHERE building_id = ? ORDER BY name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $gid);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM rooms ORDER BY building_id ASC, name ASC";
    $result = $conn->query($sql);
}

$rows = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $room_id = $row['id'];

        $cstmt = $conn->prepare(
            "SELECT c.id, c.start_date, c.end_date, c.status, c.movein_date, u.full_name, u.phone
             FROM contract c LEFT JOIN users u ON u.id = c.user_id
             WHERE c.room_id = ? ORDER BY c.id DESC LIMIT 1"
        );
        $cstmt->bind_param('i', $room_id);
        $cstmt->execute();
        $row['contract'] = $cstmt->get_result()->fetch_assoc();
        $cstmt->close();

        $bstmt = $conn->prepare(
            "SELECT id, type, totalamount, paymentstatus, endbill_date
             FROM bill WHERE room_id = ? ORDER BY id DESC LIMIT 1"
        );
        $bstmt->bind_param('i', $room_id);
        $bstmt->execute();
        $row['latest_bill'] = $bstmt->get_result()->fetch_assoc();
        $bstmt->close();

        $rows[] = $row;
    }
}

// ส่งข้อมูลกลับในรูปแบบ JSON
header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => $rows]);

// ปิดการเชื่อมต่อ
$conn->close();
