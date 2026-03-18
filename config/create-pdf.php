<?php
require_once('../vendor/autoload.php');
require_once('../config/fpdf.php');

$id = $_POST['id'];
$username = $_POST['user'];
$date = $_POST['date'];
$kwh = $_POST['kwh'];
$fv = $_POST['fv'];
$lv = $_POST['lv'];
$rkwh = $_POST['rkwhr'];
$service = $_POST['service'];
$ft = $_POST['ft'];
$rft = $_POST['rft'];
$sum = $_POST['sum'];
$tax = $_POST['tax'];
$rtax = $_POST['rtax'];
$total = $_POST['total'];
// if ($consumption) {
//     echo json_encode(['status' => false, 'message' => 'Failed: Required parameter not found. Please verify your request.']);
// }

$data = calculateBill($kwh);

$thaiMonth = [
    1 => 'ม.ค.',
    2 => 'ก.พ.',
    3 => 'มี.ค.',
    4 => 'เม.ย.',
    5 => 'พ.ค.',
    6 => 'มิ.ย.',
    7 => 'ก.ค.',
    8 => 'ส.ค.',
    9 => 'ก.ย.',
    10 => 'ต.ค.',
    11 => 'พ.ย.',
    12 => 'ธ.ค.'
];
// แปลงวันที่เป็น timestamp
$timestamp = strtotime($date);

// แปลงปี ค.ศ. เป็น พ.ศ.
$yearBE = date('Y', $timestamp) + 543;
$yearShort = substr($yearBE, -2);

$monthThai = $thaiMonth[date('n', $timestamp)];
// สร้าง PDF

$pdf = new tFPDF('P', 'mm', [120, 180]);
$pdf->AddPage();

$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.ttf', true);
$pdf->AddFont('THSarabunNew', 'B', 'THSarabunNew Bold.ttf', true);

$pdf->SetFont('THSarabunNew', 'B', 14);
$pdf->Image('../assets/images/electrical_logo.png', 5, 5, 20, 0);
$pdf->Cell(15, 10, '', 0, 0);
$pdf->SetTextColor(116, 4, 95);
$pdf->Cell(50, 10, "การไฟฟ้าส่วนภูมิภาค", 0, 0);
$pdf->SetFont('THSarabunNew', 'B', 16);
$pdf->SetTextColor(177, 34, 38);
$pdf->Cell(30, 10, "ใบแจ้งค่าไฟฟ้า", 0, 1);
$pdf->Ln(6);

$pdf->SetFont('THSarabunNew', '', 12);
$pdf->SetTextColor(116, 4, 95);
$pdf->Cell(30, 6, "Code", 1, 0);
$pdf->Cell(20, 6, "Invice No.", 1, 0);
$pdf->Cell(30, 6, "Date", 1, 0);
$pdf->Cell(20, 6, "Month", 1, 1);

$pdf->SetFont('THSarabunNew', '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(30, 6, 'G0' . rand(0, 9) . rand(0, 9) . rand(0, 9), 1, 0);
$pdf->Cell(20, 6, 'I00' . rand(0, 9), 1, 0);
$pdf->Cell(30, 6, $date, 1, 0);
$pdf->Cell(20, 6, $monthThai . ' ' . $yearBE, 1, 1);

$pdf->SetFont('THSarabunNew', '', 12);
$pdf->SetTextColor(116, 4, 95);
$pdf->Cell(20, 6, "Meter ID", 1, 0);
$pdf->Cell(20, 6, "Create By", 1, 0);
$pdf->Cell(30, 6, "Due Date", 1, 0);
$pdf->Cell(30, 6, "Last Date", 1, 1);


$idtext = str_pad($id, 3, '0', STR_PAD_LEFT);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 6, 'M' . $yearShort . $idtext, 1, 0);
$pdf->Cell(20, 6, $username, 1, 0);
$pdf->Cell(30, 6, $date, 1, 0);
$pdf->Cell(30, 6, $date, 1, 1);

$pdf->SetFont('THSarabunNew', '', 12);
$pdf->SetTextColor(116, 4, 95);
$pdf->Cell(30, 6, "Previous Reading", 1, 0);
$pdf->Cell(30, 6, "Recent Reading", 1, 0);
$pdf->Cell(30, 6, "Consumption", 1, 0);
$pdf->Cell(10, 6, "Unit", 1, 1);

$pdf->SetFont('THSarabunNew', '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(30, 12, $fv, 1, 0);
$pdf->Cell(30, 12, $lv, 1, 0);
$pdf->Cell(30, 12, $kwh, 1, 0);
$pdf->Cell(10, 12, "unit", 1, 0, 'C');

$pdf->Ln(36);

$pdf->SetFont('THSarabunNew', '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(35, 6, "WM Version 0." . rand(0, 9) . '.' . rand(0, 9) . '.', 1, 0);
$pdf->Cell(25, 6, "จำนวนเงิน (บาท)", 1, 1);

$pdf->Cell(35, 6, "ค่าพลังงานไฟฟ้า", 0, 0);
$pdf->Cell(25, 6, $rkwh, 0, 1, 'R');

$pdf->Cell(35, 6, "ค่าบริการรายเดือน", 0, 0);
$pdf->Cell(25, 6, $service, 0, 1, 'R');


$pdf->Cell(35, 6, "ค่า Ft " . $ft . " บาท/หน่วย", 0, 0);
$pdf->Cell(25, 6, $rft, 0, 1, 'R');

$pdf->Cell(35, 6, "รวมเงินค่าไฟฟ้า", 0, 0);
$pdf->Cell(25, 6, $sum, 0, 1, 'R');

$pdf->Cell(35, 6, "ภาษีมูลค่าเพิ่ม " . $tax . "%", 0, 0);
$pdf->Cell(25, 6, $rtax, 0, 0, 'R');
$pdf->Cell(10, 6, "", 0, 0);
$pdf->Cell(30, 6, "สแกนจ่าย ณ บัญชี ", 0, 1);

$pdf->Cell(35, 6, "รวมเงินค่าไฟฟ้าเดือนปัจจุบัน", 0, 0);
$pdf->Cell(25, 6, $total, 0, 0, 'R');
$pdf->Cell(3, 6, "", 0, 0);
$pdf->Cell(30, 6, "Fieldtech Automation co,ltd ", 0, 1);

$pdf->SetFont('THSarabunNew', '', 14);
$pdf->SetTextColor(177, 34, 38);
$pdf->Cell(35, 10, "รวมเงินที่ต้องชำระ(Amount)", 0, 0);
$pdf->Cell(25, 10, $total, 0, 1, 'R');

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

$text = "Hello, QadggdgwefagqtgasdfadR Coasdafasfgagwgde!asfag";

// ✅ กำหนดค่าตั้งแต่ตอน new
$qrCode = new QrCode(
    data: $text,
    encoding: new Encoding('UTF-8'),
    size: 300,
    margin: 10
);

// เขียนด้วย PNG Writer
$writer = new PngWriter();
$result = $writer->write($qrCode);

$today = date('Y-m-d');
$baseDirQRCode = __DIR__ . '/../assets/images/qrcode/';
if (!is_dir($baseDirQRCode)) {
    mkdir($baseDirQRCode, 0777, true);
}
$result->saveToFile($baseDirQRCode . '/qrcode.png');


$pdf->Image('../assets/images/qrcode/qrcode.png', 77, 90, 30, 0);


$today = date('Y-m-d');
$baseDir = __DIR__ . '/../downloads/' . $today;

if (!is_dir($baseDir)) {
    mkdir($baseDir, 0777, true);
}

$filename = 'bill_' . time() . '.pdf';
$filepath = $baseDir . '/' . $filename;

// $pdf->Output('', 'I'); // I = Inline, แสดง PDF ใน browser
// บันทึกไฟล์ PDF ลงโฟลเดอร์ของวันนั้น
$pdf->Output('F', $filepath);

$webPath = dirname($_SERVER['SCRIPT_NAME']); // จะได้ /ams/config
$webPath = str_replace('/config', '', $webPath); // เอา config ออก เหลือ /ams
// สร้าง URL สำหรับดาวน์โหลด (อิงจากเว็บ root)
$fileUrl = $webPath . '/downloads/' . $today . '/' . $filename;

// ส่ง JSON กลับไป
echo json_encode([
    'status' => true,
    'url' => $fileUrl,
    'message' => 'Export success'
]);
