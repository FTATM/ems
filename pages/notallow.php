<?php
include '../components/session.php';
?>
<!DOCTYPE html>
<html lang="<?= $langCode ?>">
<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['errortoken'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/notallow.css">
</head>

<body>

    <div class="denied-bg">
        <!-- Decorative circles -->
        <div class="denied-circle denied-circle--1"></div>
        <div class="denied-circle denied-circle--2"></div>
        <div class="denied-circle denied-circle--3"></div>

        <div class="denied-card">

            <!-- Icon -->
            <div class="denied-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
            </div>

            <!-- Text -->
            <div class="denied-code">403</div>
            <h2 class="denied-title">ไม่ได้รับอนุญาต</h2>
            <p class="denied-desc">คุณไม่มีสิทธิ์เข้าถึงหน้านี้<br>โปรดติดต่อผู้ดูแลระบบ</p>

            <!-- Action -->
            <a href="../pages/login.php" class="denied-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="1 4 1 10 7 10" />
                    <path d="M3.51 15a9 9 0 1 0 .49-4.5" />
                </svg>
                กลับไปหน้าเข้าสู่ระบบ
            </a>

        </div>
    </div>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-login.html"; ?>
</body>

</html>