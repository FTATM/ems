<?php

require_once __DIR__ . '/python-bin.php';   // ems_python_bin() — reads PYTHON_BIN from .env

$meter_id = $_POST['meter_id'] ?? "";
$ip = $_POST['ip'] ?? "";
$port = $_POST['port'] ?? "";
$qua = $_POST['quality'] ?? 2;
$sid = $_POST['slaveid'] ?? 1;

try {
    // Python interpreter comes from .env (PYTHON_BIN); defaults to the Windows
    // "py" launcher, which is on the system PATH Apache sees (a bare "python"
    // from a per-user install is not).
    // Absolute script path + escaped args (these come straight from POST).
    $script = __DIR__ . '/../connector/pymodbustcp.py';
    $cmd = sprintf(
        '%s %s %s %s %s %s %s 2>&1',
        ems_python_bin(),
        escapeshellarg($script),
        escapeshellarg($meter_id),
        escapeshellarg($ip),
        escapeshellarg($port),
        escapeshellarg($qua),
        escapeshellarg($sid)
    );
    $response = shell_exec($cmd);
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
