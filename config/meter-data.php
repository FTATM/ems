<?php
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $meter_id = $data['meter_id'];
    $datetime = $data['datetime'];
    $data_array = $data['data'];

    $sql = "SELECT * FROM data_type";
    $result = $conn->query($sql);

    $column = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $column[] = $row;
        }
    }

    $success_count = 0;
    $error_count = 0;

    // เตรียม statement ล่วงหน้า
    $stmt = $conn->prepare("INSERT INTO meter_data (meter_id, create_date, type_value_id, value) 
                            VALUES (?, ?, ?, ?)");

    foreach ($data_array as $key => $valueData) {
        foreach ($column as $row) {
            if ($key === $row['name']) {
                $type_value_id = $row['id'];
                $stmt->bind_param("isid", $meter_id, $datetime, $type_value_id, $valueData);
                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }
        }
    }

    $response = [
        "success" => true,
        "inserted" => $success_count,
        "errors" => $error_count
    ];
} else {
    $response = [
        "success" => false,
        "message" => "Invalid request"
    ];
}

echo json_encode($response);
