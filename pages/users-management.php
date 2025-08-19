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
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="bg-secondary bg-opacity-25 d-flex flex-column pt-5 align-items-center" style="min-height: 80svh;">
                <div class="my-5 text-center fs-1 fw-bolder">User list</div>
                <!-- ตาราง -->
                <table id="table-user" class="container table table-bordered table-striped" style="height: 90%;">
                </table>

                <div id="pagination" class="mt-3 d-flex gap-2 justify-content-center" style="height: 10%;"></div>
            </div>
        </div>
    </div>

    <!-- Modal แก้ไขข้อมูลผู้ใช้ -->
    <div class="modal edit" id="editModal" tabindex="-1" aria-labelledby="editModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขชื่อ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-full_name" class="form-label">ชื่อใหม่</label>
                        <input type="text" class="form-control" id="edit-full_name">
                        <label for="edit-phone" class="form-label">เบอร์โทรศัพท์</label>
                        <input type="text" class="form-control" id="edit-phone">
                        <label for="edit-address" class="form-label">ที่อยู่</label>
                        <input type="text" class="form-control" id="edit-address">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="submitEdit()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>


    <!--  Modal ยืนยันการลบ -->
    <div class="modal del" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ยืนยันการลบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <p>คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?</p>
                    <input type="hidden" id="delete-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-danger" onclick="submitDelete()">ยืนยันลบ</button>
                   
                </div>
            </div>
        </div>
    </div>


    <!-- Modal เปลี่ยนรหัสผ่าน  -->
     <!-- Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="changePasswordForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="change-password-id" name="user_id">
                    
                    <div class="mb-3">
                        <label for="change-password-new" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="change-password-new" name="change-password-new" required>
                    </div>
                    <div class="mb-3">
                        <label for="change-password-confirm" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="change-password-confirm" name="change-password-confirm" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Change</button>
                </div>
            </form>
        </div>
    </div>
</div>




    <script id="theme-data" type="application/json">
        <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-users-management.html"; ?>
</body>

</html>