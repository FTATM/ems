<?php
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $meter_id = $data['meter_id'];
    $datetime = $data['datetime'];
    $data_array = $data['data'];

    // Resolve data_type names to ids once, as a lookup rather than a nested
    // scan per incoming key. Soft-deleted types are excluded, matching every
    // other endpoint (this query used to select them too).
    $sql = "SELECT id, name FROM data_type WHERE is_deleted = 0";
    $result = $conn->query($sql);

    $type_ids = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $type_ids[$row['name']] = (int) $row['id'];
        }
    }

    $success_count = 0;
    $error_count = 0;
    $unknown_keys = [];

    // เตรียม statement ล่วงหน้า
    $stmt = $conn->prepare("INSERT INTO meter_data (meter_id, create_date, type_value_id, value)
                            VALUES (?, ?, ?, ?)");

    // One transaction per meter instead of 18 separate autocommitted inserts.
    $conn->begin_transaction();

    foreach ($data_array as $key => $valueData) {
        if (!isset($type_ids[$key])) {
            $unknown_keys[] = $key;
            continue;
        }
        // The collector omits registers it could not read, so anything arriving
        // here should be a real number. Refuse non-numerics rather than letting
        // bind_param cast them to 0.00 — that silently turned dead registers
        // into genuine-looking zero readings.
        if (!is_numeric($valueData)) {
            $error_count++;
            continue;
        }
        $type_value_id = $type_ids[$key];
        $value = (float) $valueData;
        $stmt->bind_param("isid", $meter_id, $datetime, $type_value_id, $value);
        if ($stmt->execute()) {
            $success_count++;
        } else {
            $error_count++;
        }
    }

    $conn->commit();

    $response = [
        "success" => true,
        "inserted" => $success_count,
        "errors" => $error_count
    ];
    if ($unknown_keys) {
        $response["unknown_keys"] = $unknown_keys;
    }
} else {
    $response = [
        "success" => false,
        "message" => "Invalid request"
    ];
}

echo json_encode($response);
