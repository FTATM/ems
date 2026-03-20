<?php
date_default_timezone_set('Asia/Bangkok');
$api_url = "http://localhost/ems/config/meter-data.php"; // 🔥 เปลี่ยนเป็น URL ของคุณ

function randomFloat($min, $max, $decimals = 2) {
    return round($min + mt_rand() / mt_getrandmax() * ($max - $min), $decimals);
}

while (true) {

    $data = [
        "meter_id" => 1,
        "datetime" => date("Y-m-d H:i:s"),
        "data" => [
            "kW" => randomFloat(10, 100),
            "kWh" => randomFloat(1000, 5000),
            "kVA" => randomFloat(10, 120),
            "kVAh" => randomFloat(1000, 6000),
            "kVAR" => randomFloat(5, 50),
            "kVARh" => randomFloat(500, 3000),

            "Voltage A-N" => randomFloat(210, 240),
            "Voltage B-N" => randomFloat(210, 240),
            "Voltage C-N" => randomFloat(210, 240),

            "Voltage A_B" => randomFloat(360, 420),
            "Voltage B_C" => randomFloat(360, 420),
            "Voltage C_A" => randomFloat(360, 420),

            "Current A" => randomFloat(5, 50),
            "Current B" => randomFloat(5, 50),
            "Current C" => randomFloat(5, 50),
            "Current avg" => randomFloat(5, 50),

            "Pf" => randomFloat(0.7, 1.0, 3),
            "Frequency" => randomFloat(49.5, 50.5, 2),
        ]
    ];

    $ch = curl_init($api_url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "❌ Error: " . curl_error($ch) . PHP_EOL;
    } else {
        echo "✅ Sent: " . date("H:i:s") . " | Response: " . $response . PHP_EOL;
    }

    curl_close($ch);

    sleep(30); // 🔥 ปรับเวลายิง (วินาที)
}