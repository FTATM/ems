<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";
require_once "../config/fpdf.php"; // calculateBill(), calculateWaterBill()

$room_id = (int)($_POST['room_id'] ?? 0);
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

if (!$room_id || !$start_date || !$end_date) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}
if (strtotime($end_date) < strtotime($start_date)) {
    echo json_encode(['success' => false, 'message' => 'ช่วงวันที่ไม่ถูกต้อง']);
    exit;
}

// ── ห้อง ──
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param('i', $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$room) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบห้องนี้']);
    exit;
}

// ── ผู้เช่าปัจจุบัน (ต้องมีสัญญาที่ active จึงจะออกบิลได้) ──
$cstmt = $conn->prepare(
    "SELECT user_id FROM contract WHERE room_id = ? AND status = 'active' ORDER BY id DESC LIMIT 1"
);
$cstmt->bind_param('i', $room_id);
$cstmt->execute();
$contract = $cstmt->get_result()->fetch_assoc();
$cstmt->close();
if (!$contract) {
    echo json_encode(['success' => false, 'message' => 'ห้องนี้ยังไม่มีผู้เช่าที่มีสัญญา active จึงยังออกบิลไม่ได้']);
    exit;
}
$user_id = $contract['user_id'];

$startDateTime = $start_date . ' 00:00:00';
$endDateTime = $end_date . ' 23:59:59';

// ── หา type id ของ kWh (ไฟฟ้า) และ Positive Cumulative (น้ำ) ──
function findTypeId($conn, $pattern)
{
    $res = $conn->query("SELECT id, name FROM data_type WHERE is_deleted = 0");
    while ($row = $res->fetch_assoc()) {
        if (preg_match($pattern, $row['name'])) return (int)$row['id'];
    }
    return null;
}

function nearestReading($conn, $meterId, $typeId, $atOrBefore)
{
    $stmt = $conn->prepare(
        "SELECT value, create_date FROM meter_data
         WHERE meter_id = ? AND type_value_id = ? AND create_date <= ?
         ORDER BY create_date DESC LIMIT 1"
    );
    $stmt->bind_param('iis', $meterId, $typeId, $atOrBefore);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? (float)$row['value'] : null;
}

function findAssignedMeter($conn, $roomId, $meterTypeId)
{
    $stmt = $conn->prepare(
        "SELECT id FROM meter WHERE room_id = ? AND meter_type_id = ? AND is_deleted = 0 LIMIT 1"
    );
    $stmt->bind_param('ii', $roomId, $meterTypeId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? (int)$row['id'] : null;
}

function billAlreadyExists($conn, $roomId, $type, $start, $end)
{
    $stmt = $conn->prepare(
        "SELECT id FROM bill WHERE room_id = ? AND type = ? AND startbill_date = ? AND endbill_date = ?"
    );
    $stmt->bind_param('isss', $roomId, $type, $start, $end);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $exists;
}

function insertBill($conn, $userId, $roomId, $start, $end, $unitStart, $unitEnd, $type, $usage, $unitPrice, $total)
{
    $stmt = $conn->prepare(
        "INSERT INTO bill (user_id, room_id, startbill_date, endbill_date, unit_start, unit_end, type, usageamount, unitprice, totalamount, paymentstatus)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Unpaid')"
    );
    $stmt->bind_param(
        'iissddsddd',
        $userId, $roomId, $start, $end, $unitStart, $unitEnd, $type, $usage, $unitPrice, $total
    );
    $stmt->execute();
    $stmt->close();
}

$lines = [];
$grandTotal = 0.0;

// ── ค่าไฟฟ้า ──
$elecMeter = findAssignedMeter($conn, $room_id, 1);
if ($elecMeter) {
    $kwhTypeId = findTypeId($conn, '/^kWh$/i');
    $startVal = nearestReading($conn, $elecMeter, $kwhTypeId, $startDateTime) ?? 0;
    $endVal = nearestReading($conn, $elecMeter, $kwhTypeId, $endDateTime);

    if ($endVal === null) {
        $lines[] = ['type' => 'Electricity', 'created' => false, 'reason' => 'ไม่มีข้อมูลมิเตอร์ไฟฟ้าในช่วงที่เลือก'];
    } else {
        $usage = max(0, $endVal - $startVal);
        $calc = calculateBill($usage);
        $unitPrice = $usage > 0 ? round($calc['total'] / $usage, 4) : 0;
        if (billAlreadyExists($conn, $room_id, 'Electricity', $start_date, $end_date)) {
            $lines[] = ['type' => 'Electricity', 'created' => false, 'reason' => 'มีบิลค่าไฟฟ้าของรอบนี้อยู่แล้ว'];
        } else {
            insertBill($conn, $user_id, $room_id, $start_date, $end_date, $startVal, $endVal, 'Electricity', $usage, $unitPrice, $calc['total']);
            $lines[] = ['type' => 'Electricity', 'created' => true, 'unit_start' => $startVal, 'unit_end' => $endVal, 'usageamount' => $usage, 'unitprice' => $unitPrice, 'totalamount' => round($calc['total'], 2)];
            $grandTotal += $calc['total'];
        }
    }
} else {
    $lines[] = ['type' => 'Electricity', 'created' => false, 'reason' => 'ห้องนี้ยังไม่ได้ผูกมิเตอร์ไฟฟ้า'];
}

// ── ค่าน้ำ ──
$waterMeter = findAssignedMeter($conn, $room_id, 2);
if ($waterMeter) {
    $posCumId = findTypeId($conn, '/positive.*cumulative/i');
    $startVal = nearestReading($conn, $waterMeter, $posCumId, $startDateTime) ?? 0;
    $endVal = nearestReading($conn, $waterMeter, $posCumId, $endDateTime);

    if ($endVal === null) {
        $lines[] = ['type' => 'Water', 'created' => false, 'reason' => 'ไม่มีข้อมูลมิเตอร์น้ำในช่วงที่เลือก'];
    } else {
        $usage = max(0, $endVal - $startVal);

        $rstmt = $conn->prepare(
            "SELECT unit_price FROM water_rates WHERE is_deleted = 0 AND start_date <= ?
             AND (end_date IS NULL OR end_date >= ?) ORDER BY start_date DESC LIMIT 1"
        );
        $rstmt->bind_param('ss', $end_date, $end_date);
        $rstmt->execute();
        $rateRow = $rstmt->get_result()->fetch_assoc();
        $rstmt->close();
        $unitPrice = $rateRow ? (float)$rateRow['unit_price'] : 0;

        $calc = calculateWaterBill($usage, $unitPrice);
        if (billAlreadyExists($conn, $room_id, 'Water', $start_date, $end_date)) {
            $lines[] = ['type' => 'Water', 'created' => false, 'reason' => 'มีบิลค่าน้ำของรอบนี้อยู่แล้ว'];
        } else {
            insertBill($conn, $user_id, $room_id, $start_date, $end_date, $startVal, $endVal, 'Water', $usage, $unitPrice, $calc['total']);
            $lines[] = ['type' => 'Water', 'created' => true, 'unit_start' => $startVal, 'unit_end' => $endVal, 'usageamount' => $usage, 'unitprice' => $unitPrice, 'totalamount' => round($calc['total'], 2)];
            $grandTotal += $calc['total'];
        }
    }
} else {
    $lines[] = ['type' => 'Water', 'created' => false, 'reason' => 'ห้องนี้ยังไม่ได้ผูกมิเตอร์น้ำ'];
}

// ── ค่าเช่า ──
$rent = (float)($room['price_per_month'] ?? 0);
if ($rent > 0) {
    if (billAlreadyExists($conn, $room_id, 'Rent', $start_date, $end_date)) {
        $lines[] = ['type' => 'Rent', 'created' => false, 'reason' => 'มีบิลค่าเช่าของรอบนี้อยู่แล้ว'];
    } else {
        insertBill($conn, $user_id, $room_id, $start_date, $end_date, 0, 0, 'Rent', 1, $rent, $rent);
        $lines[] = ['type' => 'Rent', 'created' => true, 'usageamount' => 1, 'unitprice' => $rent, 'totalamount' => round($rent, 2)];
        $grandTotal += $rent;
    }
} else {
    $lines[] = ['type' => 'Rent', 'created' => false, 'reason' => 'ห้องนี้ยังไม่ได้ตั้งค่าเช่า/เดือน'];
}

echo json_encode([
    'success' => true,
    'lines' => $lines,
    'grand_total' => round($grandTotal, 2),
]);

$conn->close();
