<?php
include '../components/session.php';
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">
<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['group'] ?> - AMS</title>
    <link rel="stylesheet" href="../styles/admin.css">
    <!-- path: /Library/WebServer/Documents/ems/styles/admin.css -->
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important;">
    <div id="main">
        <?php include "../components/sidemenu.php"; ?>
        <div class="page-wrapper">
            <?php include "../components/header.php"; ?>

            <div class="page-content">

                <!-- Form Card -->
                <div class="form-card">
                    <form method="POST" action="../config/create-admin.php">

                        <!-- Section: Account -->
                        <div class="form-section-title">
                            <i class="bi bi-person-gear"></i> ข้อมูลบัญชี
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username <span class="req">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person"></i>
                                <input type="text" class="form-control" name="username" id="username"
                                    placeholder="กรอก username" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">อีเมล <span class="req">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-envelope"></i>
                                <input type="email" class="form-control" name="email" id="email"
                                    placeholder="you@example.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">รหัสผ่าน <span class="req">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-lock"></i>
                                <input type="password" class="form-control" name="password" id="password"
                                    placeholder="กรอกรหัสผ่าน" required>
                            </div>
                        </div>

                        <!-- Section: Permission -->
                        <div class="form-section-title">
                            <i class="bi bi-shield-check"></i> สิทธิ์การใช้งาน
                        </div>

                        <div class="admin-toggle-wrap mb-2">
                            <div class="toggle-label">
                                <span>ผู้ดูแลระบบ (Admin)</span>
                                <small>สามารถจัดการข้อมูลผู้ใช้และตั้งค่าระบบได้</small>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_admin" id="is_admin" value="1" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="form-actions">
                            <a href="javascript:history.back()" class="btn-cancel">
                                <i class="bi bi-x-lg"></i> <?= $lang['cancel'] ?>
                            </a>
                            <button type="submit" class="btn-save">
                                <i class="bi bi-shield-plus"></i> สร้างบัญชี
                            </button>
                        </div>

                    </form>
                </div>

            </div>

            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-group.html"; ?>

</body>

</html>