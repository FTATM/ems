<?php
include '../components/session.php';
checkLogin();

require '../vendor/autoload.php';
require_once '../config/config.php';

$room_id = (int)($_GET['room_id'] ?? 0);
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';

if (!$room_id || !$start || !$end) {
    http_response_code(400);
    echo 'Missing room_id / start / end';
    exit;
}

$conn = new mysqli($db_config['host'] ?? '127.0.0.1', $db_config['user'] ?? 'root', $db_config['pass'] ?? '', $db_config['name'] ?? 'ams', $db_config['port'] ?? 3306);
if ($conn->connect_error) {
    http_response_code(500);
    echo 'DB connection failed';
    exit;
}

$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param('i', $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

$bstmt = $conn->prepare(
    "SELECT b.*, u.full_name FROM bill b LEFT JOIN users u ON u.id = b.user_id
     WHERE b.room_id = ? AND b.startbill_date = ? AND b.endbill_date = ?
     ORDER BY FIELD(b.type, 'Electricity', 'Water', 'Rent')"
);
$bstmt->bind_param('iss', $room_id, $start, $end);
$bstmt->execute();
$bills = $bstmt->get_result()->fetch_all(MYSQLI_ASSOC);
$bstmt->close();

if (!$room || !$bills) {
    http_response_code(404);
    echo 'ไม่พบบิลของห้องนี้ในรอบที่ระบุ';
    exit;
}

$tenantName = $bills[0]['full_name'] ?? '-';

$thaiMonths = [1=>'มกราคม',2=>'กุมภาพันธ์',3=>'มีนาคม',4=>'เมษายน',5=>'พฤษภาคม',6=>'มิถุนายน',7=>'กรกฎาคม',8=>'สิงหาคม',9=>'กันยายน',10=>'ตุลาคม',11=>'พฤศจิกายน',12=>'ธันวาคม'];
$endTs = strtotime($end);
$billMonthLabel = $thaiMonths[(int)date('n', $endTs)] . ' ' . (date('Y', $endTs) + 543);

$typeLabel = ['Electricity' => 'ค่าไฟฟ้า', 'Water' => 'ค่าน้ำ', 'Rent' => 'ค่าห้อง ' . ($room['name'] ?? '')];

$pdf = new tFPDF();
$pdf->AddPage();

$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.ttf', true);
$pdf->AddFont('THSarabunNew', 'B', 'THSarabunNew Bold.ttf', true);
$pdf->SetFont('THSarabunNew', '', 16);

$pdf->Cell(0, 10, 'ใบแจ้งหนี้หอพัก', 0, 1, 'C');
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('THSarabunNew', '', 14);
$pdf->Cell(0, 8, 'ผู้เช่า: ' . $tenantName, 0, 1);
$pdf->Cell(0, 8, 'ห้อง: ' . ($room['name'] ?? '-'), 0, 1);
$pdf->Cell(0, 8, 'บิลของเดือน: ' . $billMonthLabel, 0, 1);
$pdf->Cell(0, 8, 'รอบบิล: ' . $start . ' ถึง ' . $end, 0, 1);

$pdf->SetFont('THSarabunNew', 'B', 14);
$headers = ['รายการ', 'หน่วยก่อนหน้า', 'หน่วยล่าสุด', 'หน่วยที่ใช้', 'ราคาต่อหน่วย', 'รวมราคา'];
$widths = [40, 30, 30, 30, 30, 30];
for ($i = 0; $i < count($headers); $i++) {
    $pdf->Cell($widths[$i], 8, $headers[$i], 1, 0);
}
$pdf->Ln();

$pdf->SetFont('THSarabunNew', '', 14);
$total = 0;
foreach ($bills as $b) {
    $isRent = $b['type'] === 'Rent';
    $label = $typeLabel[$b['type']] ?? $b['type'];
    $start_unit = $isRent ? '-' : number_format((float)$b['unit_start'], 2);
    $end_unit = $isRent ? '-' : number_format((float)$b['unit_end'], 2);
    $usage = (float)$b['usageamount'];
    $price = (float)$b['unitprice'];
    $amt = (float)$b['totalamount'];

    $pdf->Cell($widths[0], 8, $label, 1);
    $pdf->Cell($widths[1], 8, $start_unit, 1, 0, 'C');
    $pdf->Cell($widths[2], 8, $end_unit, 1, 0, 'C');
    $pdf->Cell($widths[3], 8, number_format($usage, 2), 1, 0, 'C');
    $pdf->Cell($widths[4], 8, number_format($price, 2), 1, 0, 'R');
    $pdf->Cell($widths[5], 8, number_format($amt, 2), 1, 1, 'R');
    $total += $amt;
}

$pdf->SetFont('THSarabunNew', 'B', 14);
$pdf->Cell($widths[0], 8, "", 0);
$pdf->Cell($widths[1], 8, "", 0);
$pdf->Cell($widths[2], 8, "", 0);
$pdf->Cell($widths[3], 8, "", 0);
$pdf->Cell($widths[4], 10, 'รวมยอดทั้งหมด', 0, 0);
$pdf->Cell(30, 10, number_format($total, 2) . ' บาท', 1, 1, 'R');
$pdf->Ln(10);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->Cell(0, 8, 'หมายเหตุ: กรุณาติดต่อผู้ดูแลหอพักเพื่อชำระเงินและยืนยันการชำระ');

$conn->close();

$pdf->Output('I', 'invoice_room' . preg_replace('/[^a-zA-Z0-9]/', '', $room['name'] ?? '') . '_' . $end . '.pdf');
exit;
