<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";
require_once "../components/theme-presets.php";

$presetKey = $_POST['preset'] ?? '';
$presets = emsThemePresets();

if (!isset($presets[$presetKey])) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบธีมสีนี้']);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO app_settings (setting_key, setting_value) VALUES ('theme_preset', ?)
     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
);
$stmt->bind_param('s', $presetKey);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'เปลี่ยนธีมสีสำเร็จ', 'preset' => $presetKey]);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกได้']);
}

$stmt->close();
$conn->close();
