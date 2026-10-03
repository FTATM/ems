<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/no-crash.php";
include "../config/connect.php";
require_once "../components/theme-presets.php";

$currentKey = emsGetCurrentPreset($conn);
$presets = emsThemePresets();

$list = [];
foreach ($presets as $key => $p) {
    $list[] = [
        'key' => $key,
        'label_th' => $p['label_th'],
        'label_en' => $p['label_en'],
        'accent' => $p['accent'],
        'accent_dark' => $p['accent_dark'],
    ];
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'current' => $currentKey, 'presets' => $list]);

$conn->close();
