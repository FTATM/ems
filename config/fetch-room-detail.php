<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/no-crash.php";
include "../config/connect.php";

$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Failed variable not found.']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$room) {
    echo json_encode(['success' => false, 'message' => 'Room not found.']);
    exit;
}

$cstmt = $conn->prepare(
    "SELECT c.*, u.full_name, u.phone, u.email
     FROM contract c LEFT JOIN users u ON u.id = c.user_id
     WHERE c.room_id = ? ORDER BY c.id DESC"
);
$cstmt->bind_param('i', $id);
$cstmt->execute();
$contracts = $cstmt->get_result()->fetch_all(MYSQLI_ASSOC);
$cstmt->close();

$bstmt = $conn->prepare("SELECT * FROM bill WHERE room_id = ? ORDER BY id DESC");
$bstmt->bind_param('i', $id);
$bstmt->execute();
$bills = $bstmt->get_result()->fetch_all(MYSQLI_ASSOC);
$bstmt->close();

$pstmt = $conn->prepare(
    "SELECT p.* FROM payments p INNER JOIN bill b ON b.id = p.bill_id
     WHERE b.room_id = ? ORDER BY p.payment_date DESC"
);
$pstmt->bind_param('i', $id);
$pstmt->execute();
$payments = $pstmt->get_result()->fetch_all(MYSQLI_ASSOC);
$pstmt->close();

// ── รวมยอดค่าใช้จ่าย: บิลทั้งหมด, ยอดที่จ่ายแล้ว, ยอดคงเหลือ, และรอบบิลล่าสุด (รวม ค่าไฟ+ค่าน้ำ+ค่าเช่า ของรอบเดียวกัน) ──
$totalBilled = 0.0;
$totalPaid = 0.0;
foreach ($bills as $b) {
    $totalBilled += (float)$b['totalamount'];
}
foreach ($payments as $p) {
    $totalPaid += (float)$p['amount_paid'];
}

$cycles = [];
foreach ($bills as $b) {
    $key = ($b['startbill_date'] ?? '') . '|' . ($b['endbill_date'] ?? '');
    if (!isset($cycles[$key])) {
        $cycles[$key] = [
            'startbill_date' => $b['startbill_date'],
            'endbill_date' => $b['endbill_date'],
            'total' => 0.0,
            'items' => [],
        ];
    }
    $cycles[$key]['total'] += (float)$b['totalamount'];
    $cycles[$key]['items'][] = $b;
}
$cycleList = array_values($cycles);
usort($cycleList, fn($a, $c) => strcmp($c['endbill_date'] ?? '', $a['endbill_date'] ?? ''));

$totals = [
    'total_billed' => round($totalBilled, 2),
    'total_paid' => round($totalPaid, 2),
    'total_outstanding' => round($totalBilled - $totalPaid, 2),
    'latest_cycle' => $cycleList[0] ?? null,
];

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'room' => $room,
    'contracts' => $contracts,
    'bills' => $bills,
    'payments' => $payments,
    'totals' => $totals,
]);

$conn->close();
