<?php
// เรียกใช้ Python script
$meter_id   = $_POST['meter_id']   ?? "";
$serialport = $_POST['serialport'] ?? "";
$buadrate   = $_POST['buadrate']   ?? "";
$databits   = $_POST['databits']   ?? "";
$parity     = $_POST['parity']     ?? "";
$stopbits   = $_POST['stopbits']   ?? "";
$slaveid    = $_POST['slaveid']    ?? "";
$address    = $_POST['address']    ?? "";
$quality    = $_POST['quality']    ?? "";

try {
    $response = shell_exec("python ../config/pymodbusrs485.py $meter_id $serialport $buadrate $databits $parity $stopbits $slaveid $address $quality 2>&1");
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
} catch (Throwable $th) {
    echo json_encode([
        "success" => false,
        "message" => "PHP Exception",
        "output"  => $th->getMessage()
    ]);
}