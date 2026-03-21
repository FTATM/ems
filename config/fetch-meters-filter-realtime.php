<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/no-crash.php";
include "../config/connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tid = (int)($_POST['tid'] ?? 0);
    $gid = (int)($_POST['gid'] ?? 0);

    if ($tid === 0 && $gid === 0) {
        echo json_encode([
            'success' => false,
            'data' => [],
            'message' => 'Failed variable not found.'
        ]);
        exit;
    }

    // 🔹 ดึง meter
    $sqlmeter = "SELECT * FROM meter
                 WHERE is_deleted = 0 AND group_id = ? AND meter_type_id = ?";
    $stmt = $conn->prepare($sqlmeter);
    $stmt->bind_param('ii', $gid, $tid);
    $stmt->execute();
    $result = $stmt->get_result();

    // 🔹 ดึง data_type ทั้งหมด
    $sql = "SELECT * FROM data_type WHERE is_deleted = 0 ORDER BY id ASC";
    $column_result = $conn->query($sql);

    $data_types = [];
    while ($row = $column_result->fetch_assoc()) {
        $data_types[] = $row;
    }

    $meters = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $meter_id = $row['id'];
            $meters[$meter_id] = $row;
            $meters[$meter_id]['data'] = [];

            // 🔹 loop data_type ใหม่ทุกครั้ง
            foreach ($data_types as $type) {

                $sql_data = "SELECT value, create_date
                             FROM meter_data
                             WHERE meter_id = ? AND type_value_id = ? ORDER BY create_date DESC LIMIT 1";
                $stmt2 = $conn->prepare($sql_data);
                $stmt2->bind_param('ii', $meter_id, $type['id']);
                $stmt2->execute();
                $data = $stmt2->get_result();

                if ($data->num_rows > 0) {
                    while ($d = $data->fetch_assoc()) {
                        $meters[$meter_id]['data'][] = [
                            'type_id' => $type['id'],
                            'value' => $d['value'],
                            'create_date' => $d['create_date']
                        ];
                    }
                }
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $meters,
        'dataType' => $data_types
    ]);

    $conn->close();
    exit;
}