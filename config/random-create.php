<?php
require '../config/connect.php';
header('Content-Type: application/json');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    // รับค่า POST
    $name = $_POST['name'] ?? null;
    $create_date = $_POST['create_date'] ?? null;

    // รายการ field ที่เหลือ
    $fields = [
        'Kw',
        'KwHr',
        'kVA',
        'kVAHr',
        'kVAR',
        'kVARHr',
        'Vch-P1',
        'Vch-P2',
        'Vch-P3',
        'Amp-L1',
        'Amp-L2',
        'Amp-L3',
        'Amp-N',
        'Pf',
        'Frequency',
        'is_deleted'
    ];

    // ตรวจสอบข้อมูลที่จำเป็น
    if (!$name || !$create_date) {
        throw new Exception("ข้อมูลไม่ครบ: ต้องมี name และ create_date");
    }

    // เตรียมค่า field
    $values = [];
    foreach ($fields as $field) {
        if (!isset($_POST[$field])) {
            throw new Exception("Missing field: $field");
        }
        $values[$field] = $_POST[$field];
    }

    // เริ่ม transaction
    $conn->begin_transaction();

    // เพิ่มข้อมูลลงฐานข้อมูล
    $stmt = $conn->prepare("
        INSERT INTO device_realtime (
            name, create_date, Kw, KwHr, kVA, kVAHr, kVAR, kVARHr,
            Vch_P1, Vch_P2, Vch_P3,
            Amp_L1, Amp_L2, Amp_L3, Amp_N,
            Pf, Frequency, is_deleted
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssdddddddddddddddi",
        $name,
        $create_date,
        $values['Kw'],
        $values['KwHr'],
        $values['kVA'],
        $values['kVAHr'],
        $values['kVAR'],
        $values['kVARHr'],
        $values['Vch-P1'],
        $values['Vch-P2'],
        $values['Vch-P3'],
        $values['Amp-L1'],
        $values['Amp-L2'],
        $values['Amp-L3'],
        $values['Amp-N'],
        $values['Pf'],
        $values['Frequency'],
        $values['is_deleted']
    );

    $stmt->execute();

    $conn->commit();

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    $conn->rollBack();

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
