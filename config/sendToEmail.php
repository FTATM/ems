<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if (empty($_POST['email'])) {
    http_response_code(400);
    echo "❌ ไม่มีอีเมลปลายทาง";
    exit;
}

$email = $_POST['email'];

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'muensa48@gmail.com';
    $mail->Password = 'qopu vllk yulq vszs';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->CharSet = 'UTF-8';

    $mail->setFrom('muensa48@gmail.com', 'Export System');
    $mail->addAddress($email);

    // แนบไฟล์จาก JavaScript
    if (!empty($_FILES['file']['tmp_name'])) {
        $mail->addAttachment($_FILES['file']['tmp_name'], $_FILES['file']['name']);
    }

    $mail->isHTML(true);
    $mail->Subject = 'รายงานข้อมูลจากระบบ Export';
    $mail->Body = '<p>ระบบได้ส่งไฟล์ Export แนบมาด้วยแล้วครับ</p>';

    $mail->send();
    echo "✅ ส่งอีเมลแนบไฟล์สำเร็จไปยัง $email";
} catch (Exception $e) {
    echo "❌ ไม่สามารถส่งอีเมลได้: {$mail->ErrorInfo}";
}
