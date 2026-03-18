<?php
include '../components/session.php';
#checkLogin();
#checkSession();
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['group'] ?> - AMS</title>
    <link rel="stylesheet" href="../styles/user.css">
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important;">
    <div id="main">
        <?php include "../components/sidemenu.php"; ?>
        <div class="page-wrapper">
            <?php include "../components/header.php"; ?>

            <div class="page-content">

                <!-- Form Card -->
                <div class="form-card">
                    <form method="POST" action="../config/create-user.php">

                        <!-- Section: Account -->
                        <div class="form-section-title">
                            <i class="bi bi-shield-lock"></i> ข้อมูลบัญชี
                        </div>

                        <div class="form-grid-2">
                            <div class="mb-3">
                                <label class="form-label">Username <span class="req">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-person"></i>
                                    <input type="text" class="form-control" name="username" id="username"
                                        placeholder="กรอก username" required>
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
                        </div>

                        <!-- Section: Personal -->
                        <div class="form-section-title">
                            <i class="bi bi-person-vcard"></i> ข้อมูลส่วนตัว
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ชื่อ-นามสกุล <span class="req">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person-fill"></i>
                                <input type="text" class="form-control" name="full_name" id="full_name"
                                    placeholder="กรอกชื่อ-นามสกุล" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">เลขบัตรประชาชน <span class="req">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-credit-card"></i>
                                <input type="text" class="form-control" name="id_card" id="id_card"
                                    placeholder="13 หลัก" maxlength="13" required>
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="mb-3">
                                <label class="form-label">เบอร์โทรศัพท์ <span class="req">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-telephone"></i>
                                    <input type="tel" class="form-control" name="phone" id="phone"
                                        placeholder="08xxxxxxxx" required>
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
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ที่อยู่ <span class="req">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-geo-alt" style="top:12px;transform:none;"></i>
                                <textarea class="form-control" name="address" id="address" rows="3"
                                    placeholder="กรอกที่อยู่" required></textarea>
                            </div>
                        </div>

                        <!-- Section: Role (admin only) -->
                        <?php if ($_SESSION['user']['is_admin'] == 2): ?>
                        <div class="form-section-title">
                            <i class="bi bi-people"></i> สิทธิ์การใช้งาน
                        </div>
                        <div class="role-selector mb-2">
                            <div class="role-option">
                                <input type="radio" name="role" id="role-admin" value="admin">
                                <label for="role-admin">
                                    <i class="bi bi-shield-fill-check"></i> Admin
                                </label>
                            </div>
                            <div class="role-option">
                                <input type="radio" name="role" id="role-user" value="user" checked>
                                <label for="role-user">
                                    <i class="bi bi-person-fill"></i> User
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div class="form-actions">
                            <a href="javascript:history.back()" class="btn-cancel">
                                <i class="bi bi-x-lg"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn-save">
                                <i class="bi bi-check-lg"></i> บันทึก
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
</body>

</html>