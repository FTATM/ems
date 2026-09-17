<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include "../config/no-crash.php";
include "../config/connect.php";

$bill_id = (int)($_POST['bill_id'] ?? 0);
$amount_paid = (float)($_POST['amount_paid'] ?? 0);
$payment_date = $_POST['payment_date'] ?? '';
$payment_method = trim($_POST['payment_method'] ?? '');
$note = trim($_POST['note'] ?? '');

if (!$bill_id || $amount_paid <= 0 || !$payment_date) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$stmt = $conn->prepare("SELECT totalamount FROM bill WHERE id = ?");
$stmt->bind_param('i', $bill_id);
$stmt->execute();
$bill = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$bill) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบบิลนี้']);
    exit;
}

$pstmt = $conn->prepare(
    "INSERT INTO payments (bill_id, payment_date, amount_paid, payment_method, note) VALUES (?, ?, ?, ?, ?)"
);
$pstmt->bind_param('isdss', $bill_id, $payment_date, $amount_paid, $payment_method, $note);
if (!$pstmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกการชำระเงินได้']);
    exit;
}
$pstmt->close();

// รวมยอดที่จ่ายแล้วทั้งหมดของบิลนี้ เพื่ออัปเดตสถานะบิลให้ถูกต้อง
$sumStmt = $conn->prepare("SELECT COALESCE(SUM(amount_paid), 0) AS total_paid FROM payments WHERE bill_id = ?");
$sumStmt->bind_param('i', $bill_id);
$sumStmt->execute();
$totalPaid = (float)$sumStmt->get_result()->fetch_assoc()['total_paid'];
$sumStmt->close();

$totalAmount = (float)$bill['totalamount'];
if ($totalPaid >= $totalAmount) {
    $status = 'Paid';
} elseif ($totalPaid > 0) {
    $status = 'Partially Paid';
} else {
    $status = 'Unpaid';
}

$ustmt = $conn->prepare("UPDATE bill SET paymentstatus = ?, PaymentDate = ? WHERE id = ?");
$ustmt->bind_param('ssi', $status, $payment_date, $bill_id);
$ustmt->execute();
$ustmt->close();

echo json_encode(['success' => true, 'message' => 'บันทึกการชำระเงินสำเร็จ', 'status' => $status, 'total_paid' => $totalPaid]);

$conn->close();
