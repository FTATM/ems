<?php
// กำหนดค่า parameter
$meter_id = 1;
$ip = "192.168.0.7";
$port = 8800;

try {
    $output = shell_exec("python ../config/pymodbustcp.py $meter_id $ip $port 2>&1");

    echo json_encode(['success' => true, 'output' => $output]);
} catch (\Throwable $th) {
    echo json_encode(['success' => false, 'output' => $output]);
}
