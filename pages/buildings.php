<?php
include '../components/session.php';
checkLogin();
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['building'] ?> - EMS</title>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="bg-secondary bg-opacity-25 d-flex flex-column pt-5 align-items-center" style="min-height: 80svh;">
                <div class="my-5 text-center fs-1 fw-bolder">Building Management</div>
                <div class="container mb-2 text-end">
                    <input class=" btn btn-primary bg-primary w-10" value="create" onclick="openBuildingModal()">
                </div>
                <table id="table-building" class="container table table-bordered table-striped" style="height: 90%;">
                </table>
                <div id="pagination" class="mt-3 d-flex gap-2 justify-content-center" style="height: 10%;"></div>
            </div>
        </div>
    </div>

    <!-- 🔧 Modal เพิ่ม -->
    <div class="modal fade" id="newBuildingModal" tabindex="-1" aria-labelledby="newBuildingModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">สร้างตึกใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="select-Location" class="form-label">เลือกโลเคชั่น</label>
                        <select type="text" class="form-select text-black" required id="select-Location">

                        </select>
                        <label for="newName" class="form-label">ชื่อใหม่</label>
                        <input type="text" class="form-control text-black" id="newName">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="submitnewBuilding()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔧 Modal แก้ไขชื่อ -->
    <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขชื่อ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="rename-id">
                    <div class="mb-3">
                        <label for="new-name" class="form-label">ชื่อใหม่</label>
                        <input type="text" class="form-control text-black" id="new-name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="submitRename()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ❌ Modal ยืนยันการลบ -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ยืนยันการลบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <p class="text-black">คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?</p>
                    <input type="hidden" id="delete-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-danger" onclick="submitDelete()">ยืนยันลบ</button>
                </div>
            </div>
        </div>
    </div>

    <script id="theme-data" type="application/json">
        <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-building.html"; ?>
</body>

</html>