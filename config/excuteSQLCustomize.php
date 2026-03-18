<?php
set_time_limit(300);
date_default_timezone_set('Asia/Bangkok');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ส่งข้อมูลกลับในรูปแบบ JSON

include "../config/no-crash.php";
include "../config/connect.php";



// รับ SQL จาก POST
$sql = $_POST['text'] ?? '';

// เริ่มจับเวลา
$start = microtime(true);

// ตรวจสอบว่า SQL ไม่ว่าง
if (!$sql) {
    echo json_encode([
        'success' => false,
        'message' => 'No SQL provided',
        'rows' => [],
        'duration_seconds' => 0
    ]);
    exit;
}

try {
    $result = $conn->query($sql);
    
    $rows = [];

    if ($result) {
        // ถ้าเป็น SELECT, FETCH ข้อมูล
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        // ถ้าเป็น INSERT/UPDATE/DELETE
        $affected = $conn->affected_rows;

        // สิ้นสุดจับเวลา
        $end = microtime(true);
        $duration = $end - $start;

        echo json_encode([
            'success' => true,
            'message' => 'Query executed successfully',
            'rows' => $rows,
            'affected_rows' => $affected,
            'duration_seconds' => round($duration, 4)
        ]);
    } else {
        // query error
        $end = microtime(true);
        $duration = $end - $start;

        echo json_encode([
            'success' => false,
            'message' => 'SQL Error: ' . $conn->error,
            'rows' => [],
            'duration_seconds' => round($duration, 4)
        ]);
    }
} catch (Exception $e) {
    $end = microtime(true);
    $duration = $end - $start;

    echo json_encode([
        'success' => false,
        'message' => 'Exception: ' . $e->getMessage(),
        'rows' => [],
        'duration_seconds' => round($duration, 4)
    ]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>