<?php

$meter_id = $_POST['meter_id'] ?? "";
$ip = $_POST['ip'] ?? "";
$port = $_POST['port'] ?? "";

try {
    $response = shell_exec("python ../config/pymodbustcp.py $meter_id $ip $port 2>&1");
    $decoded = json_decode($response, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($decoded['success'])) {
        echo json_encode($decoded);
    } else {
        // ⛔ Python ไม่ได้ส่ง JSON (เช่น print อื่นออกมา)
        echo json_encode([
            "success" => false,
            "message" => "Invalid response from Python script.",
            "output"  => $response
        ]);
    }
} catch (\Throwable $th) {
    echo json_encode([
        "success" => false,
        "message" => "PHP Exception",
        "output"  => $th->getMessage()
    ]);
}
