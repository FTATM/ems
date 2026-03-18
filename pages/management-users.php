<?php
include '../components/session.php';
if ($_SESSION['user']['is_admin'] == 0) {
    header("Location: ../pages/permission_denied.php?permission=denied");
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['usermnm'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/management-users.css">
</head>

<body style="height:100svh; overflow:hidden;">
    <div id="main" class="d-flex" style="height:100svh; overflow:hidden;">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column" style="height:100svh; overflow:hidden;">
            <?php include "../components/header.php"; ?>

            <main class="users-main">

                <!-- Hero -->
                <div class="users-hero">
                    <h2><?= $lang['usermnm'] ?></h2>
                    <p>จัดการข้อมูลผู้ใช้งานในระบบ แก้ไข ลบ หรือเปลี่ยนรหัสผ่าน</p>
                </div>

                <!-- Card -->
                <div class="users-card">

                    <!-- Card header -->
                    <div class="users-card__header">
                        <span class="users-card__title">รายชื่อผู้ใช้งาน</span>
                        <span class="users-card__count" id="user-count">...</span>
                    </div>

                    <!-- Table scroll wrapper -->
                    <div class="users-table-wrap">
                        <table id="table-user"></table>
                    </div>

                </div>

                <!-- Pagination -->
                <div id="pagination"></div>

            </main>

            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <!-- ── Modal แก้ไขข้อมูล ── -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขข้อมูลส่วนตัว</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id">
                    <label for="edit-full_name" class="form-label">ชื่อ</label>
                    <input type="text" class="form-control" id="edit-full_name">
                    <label for="edit-phone" class="form-label">เบอร์โทรศัพท์</label>
                    <input type="text" class="form-control" id="edit-phone">
                    <label for="edit-address" class="form-label">ที่อยู่</label>
                    <input type="text" class="form-control" id="edit-address">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary" onclick="submitEdit()"><?= $lang['save'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Modal ยืนยันการลบ ── -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ยืนยันการลบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <p style="color:var(--text-card); margin:0;">คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?</p>
                    <input type="hidden" id="delete-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-danger"
                        onclick="submitDelete()"><?= $lang['confirm'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Modal เปลี่ยนรหัสผ่าน ── -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="changePasswordForm">
                    <div class="modal-header">
                        <h5 class="modal-title">เปลี่ยนรหัสผ่าน</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="change-password-id" name="user_id">
                        <label for="change-password-new" class="form-label">รหัสผ่านใหม่</label>
                        <input type="password" class="form-control" id="change-password-new" name="change-password-new"
                            required>
                        <label for="change-password-confirm" class="form-label">ยืนยันรหัสผ่าน</label>
                        <input type="password" class="form-control" id="change-password-confirm"
                            name="change-password-confirm" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                        <button type="submit" class="btn btn-primary"><?= $lang['change'] ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-management-users.html"; ?>

    <script>
    /* ── Update user count badge after table renders ── */
    window.addEventListener('load', () => {
        const observer = new MutationObserver(() => {
            const rows = document.querySelectorAll('#table-user tbody tr');
            const badge = document.getElementById('user-count');
            if (badge && rows.length > 0) {
                badge.textContent = rows.length + ' รายการ';
            }
        });
        const table = document.getElementById('table-user');
        if (table) observer.observe(table, {
            childList: true,
            subtree: true
        });
    });
    </script>

</body>

</html>