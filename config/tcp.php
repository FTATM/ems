<?php
// เรียกใช้ Python script
try {
    $output = shell_exec("python ../config/pymodbustcp.py 2>&1");

    echo json_encode(['success' => true, 'output' => $output]);
} catch (\Throwable $th) {
    echo json_encode(['success' => false, 'output' => $output]);
}
