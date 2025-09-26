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

    $sqlmeter = "SELECT * FROM meter WHERE is_deleted = 0 AND group_id = ? AND meter_type_id = ?";
    $stmt = $conn->prepare($sqlmeter);
    $stmt->bind_param('ii', $gid, $tid);
    $stmt->execute();
    $result = $stmt->get_result();
    $sql = "SELECT * FROM data_type WHERE is_deleted = 0";
    $column = $conn->query($sql);

    $meters = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $meter_id = $row['id'];

            // สร้าง array ของ data type สำหรับ meter นี้
            $data_types = [];
            if ($column->num_rows > 0) {
                // ต้องรีเซ็ต pointer ก่อน fetch ใหม่
                $column->data_seek(0);
                while ($rows = $column->fetch_assoc()) {
                    $data_types[$rows['id']] = [];
                }
            }

            if (!isset($meters[$meter_id])) {
                $meters[$meter_id] = $row;
                $meters[$meter_id] += [
                    'data' => $data_types
                ];
            }
            $sql_data = "SELECT * FROM meter_data WHERE meter_id = ?";
            $stmt = $conn->prepare($sql_data);
            $stmt->bind_param('i', $meter_id);
            $stmt->execute();
            $resultdata = $stmt->get_result();

            if ($resultdata->num_rows > 0) {
                while ($rowdata = $resultdata->fetch_assoc()) {
                    if ($rowdata['meter_id'] == $meter_id) {
                        $type_id = $rowdata['type_value_id'];
                        if (isset($meters[$meter_id]['data'][$type_id])) {
                            $meters[$meter_id]['data'][$type_id][] = $rowdata;
                        }
                    }
                }
            }
        }
    }

    // ส่งข้อมูลกลับในรูปแบบ JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $meters, 'dataType' => $data_types]);

    // ปิดการเชื่อมต่อ
    $conn->close();
    exit;
}
