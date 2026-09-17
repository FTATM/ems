<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/no-crash.php";
include "../config/connect.php";

// ── สรุปจำนวนห้องแยกตามตึก (building_id = groups.id) พร้อมชื่อกลุ่ม/สถานที่ ──
$sql = "SELECT g.id AS building_id, g.name AS building_name, l.id AS location_id, l.name AS location_name,
               r.status, COUNT(*) AS cnt
        FROM rooms r
        INNER JOIN groups g ON g.id = r.building_id
        LEFT JOIN locations l ON l.id = g.location_id
        GROUP BY g.id, g.name, l.id, l.name, r.status
        ORDER BY g.id ASC";
$result = $conn->query($sql);

$buildings = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bid = $row['building_id'];
        if (!isset($buildings[$bid])) {
            $buildings[$bid] = [
                'building_id' => $bid,
                'building_name' => $row['building_name'],
                'location_id' => $row['location_id'],
                'location_name' => $row['location_name'],
                'total' => 0,
                'empty' => 0,
                'occupied' => 0,
                'maintenance' => 0,
            ];
        }
        $status = strtolower($row['status'] ?? '');
        $cnt = (int)$row['cnt'];
        $buildings[$bid]['total'] += $cnt;
        if ($status === 'empty' || $status === 'available') {
            $buildings[$bid]['empty'] += $cnt;
        } elseif ($status === 'maintenance') {
            $buildings[$bid]['maintenance'] += $cnt;
        } else {
            $buildings[$bid]['occupied'] += $cnt;
        }
    }
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => array_values($buildings)]);

$conn->close();
