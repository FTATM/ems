<?php
include "../config/no-crash.php";
include "../config/connect.php";

header('Content-Type: application/json');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB Connection failed"]));
}

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $meter_id = $data['meter_id'];
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
                            VALUES (?, NOW(), ?, ?)");

    foreach ($data_array as $key => $valueData) {
        foreach ($column as $type) {
            if ($key === $type['name']) {
                $type_value_id = $type['id'];
                $stmt->bind_param("iid", $meter_id, $type_value_id, $valueData);
                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }
        }
    }

    $response = [
        "status" => "success",
        "inserted" => $success_count,
        "errors" => $error_count
    ];
} else {
    $response = [
        "status" => "error",
        "message" => "Invalid request"
    ];
}

echo json_encode($response);
