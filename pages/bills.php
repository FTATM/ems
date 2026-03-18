<?php
require '../vendor/autoload.php';  // autoload ของ Composer

$pdf = new tFPDF();
$pdf->AddPage();

// เพิ่มฟอนต์ภาษาไทยจากไฟล์ .ttf (ต้องอยู่ใน font/unifont/)
$pdf->AddFont('THSarabun', '', 'THSarabunNew.ttf', true);
$pdf->AddFont('THSarabun', 'B', 'THSarabunNew Bold.ttf', true);
$pdf->SetFont('THSarabun', '', 16);

// หัวเรื่อง: ใบแจ้งหนี้ พร้อมเส้นขีดใต้
$pdf->Cell(0, 10, 'ใบแจ้งหนี้หอพัก', 0, 1, 'C');
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ข้อมูลผู้เช่า
$pdf->SetFont('THSarabun', '', 14);
$pdf->Cell(0, 8, 'ผู้เช่า: นายสมชาย ใจดี', 0, 1);
$pdf->Cell(0, 8, 'ห้อง: 402', 0, 1);
$pdf->Cell(0, 8, 'บิลของเดือน: มิถุนายน 2025', 0, 1);
$pdf->Cell(0, 8, 'ราคาเช่าห้อง: 3,200 บาท', 0, 1);
$pdf->Cell(0, 8, 'วันที่อ่านหน่วย (ไฟฟ้า/น้ำ): 30 มิถุนายน 2025', 0, 1);
// หัวตาราง
$pdf->SetFont('THSarabun', 'B', 14);
$headers = ['รายการ', 'หน่วยก่อนหน้า', 'หน่วยล่าสุด', 'หน่วยที่ใช้', 'ราคาต่อหน่วย', 'รวมราคา'];
$widths = [40, 30, 30, 30, 30, 30];
for ($i = 0; $i < count($headers); $i++) {
    $pdf->Cell($widths[$i], 8, $headers[$i], 1, 0);
}
$pdf->Ln();

// ข้อมูลรับมาทางตัวแปร $data
$data = [
    ["ค่าไฟฟ้า", 1460, 1490, 30, 7, 210],
    ["ค่าน้ำ", 521, 530, 9, 17, 153],
    ["ค่าห้อง 402", '-', '-', 1, 3200, 3200],
];

$pdf->SetFont('THSarabun', '', 14);
$total = 0;
foreach ($data as $row) {
    list($item, $start, $end, $unit, $price, $amt) = $row;
    $pdf->Cell($widths[0], 8, $item, 1);
    $pdf->Cell($widths[1], 8, $start, 1, 0, 'C');
    $pdf->Cell($widths[2], 8, $end, 1, 0, 'C');
    $pdf->Cell($widths[3], 8, $unit, 1, 0, 'C');
    $pdf->Cell($widths[4], 8, number_format($price), 1, 0, 'R');
    $pdf->Cell($widths[5], 8, number_format($amt), 1, 1, 'R');
    $total += $amt;
}

// สรุปยอดรวม
$pdf->SetFont('THSarabun', 'B', 14);
$pdf->Cell($widths[0], 8, "", 0);
$pdf->Cell($widths[1], 8, "", 0);
$pdf->Cell($widths[2], 8, "", 0);
$pdf->Cell($widths[3], 8, "", 0);
$pdf->Cell($widths[4], 10, 'รวมยอดทั้งหมด', 0, 0);
$pdf->Cell(30, 10, number_format($total) . ' บาท', 1, 1, 'R'); // ln=1
$pdf->Ln(10);
$pdf->SetFont('THSarabun', '', 12);
$pdf->Cell(0, 8, 'หมายเหตุ: กรุณาชำระเงินภายในวันที่ 7 กรกฎาคม 2025 ผ่านบัญชีธนาคารกรุงเทพ สาขาอุบลราชธานี เลขบัญชี 123 4 56789 0');

$pdf->Output('I', 'invoice_ho_pak.pdf');
exit;
