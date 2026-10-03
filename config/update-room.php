<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";



// รับค่าจาก POST
$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? '';
$value = $_POST['value'] ?? '';

if (!$action || ($action !== 'new' && !$id)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

switch ($action) {
    case "rename":
        if ($value === '') {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE rooms SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $value, $id);
        break;

    case "status":
        if ($value === '') {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE rooms SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $value, $id);
        break;

    case "delete":
        $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $id);
        break;

    case "edit":
        $name = trim($_POST['name'] ?? '');
        $size_sqm = ($_POST['size_sqm'] ?? '') === '' ? null : (float)$_POST['size_sqm'];
        $price_per_month = ($_POST['price_per_month'] ?? '') === '' ? null : (float)$_POST['price_per_month'];
        $type = trim($_POST['type'] ?? '');
        $note = trim($_POST['note'] ?? '');
        if ($name === '') {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            exit;
        }
        $stmt = $conn->prepare(
            "UPDATE rooms SET name = ?, size_sqm = ?, price_per_month = ?, type = ?, note = ? WHERE id = ?"
        );
        $stmt->bind_param("sddssi", $name, $size_sqm, $price_per_month, $type, $note, $id);
        break;

    case "new":
        $group_id = $_POST['gid'] ?? ($_POST['group_id'] ?? 0);
        if (!$group_id || $value === '') {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            exit;
        }
        $stmt = $conn->prepare("INSERT INTO rooms (building_id, name, status) VALUES (?, ?, 'empty')");
        $stmt->bind_param("is", $group_id, $value);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'สถานะไม่ถูกต้อง']);
        exit;
}

// ทำการ execute
try {
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'อัปเดตข้อมูลสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตได้']);
    }
} catch (mysqli_sql_exception $e) {
    if ($action === 'delete') {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบห้องนี้ได้ เนื่องจากมีสัญญาเช่าหรือบิลผูกอยู่']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตได้']);
    }
}

$stmt->close();
$conn->close();
