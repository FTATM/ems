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
    <title><?= $lang['group'] ?> - EMS</title>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="bg-secondary bg-opacity-25 d-flex flex-column pt-5 align-items-center" style="min-height: 80svh;">
                <div class="my-5 text-center fs-1 fw-bolder"><?= $lang['group'] ?>/<?= $lang['project'] ?></div>
                <table id="table-group" class="container table table-bordered table-striped" style="height: 90%;">
                    <thead class="text-center">
                        <tr>
                            <th class="text-white w-10" style="background-color:#001B5E;"><?= $lang['id']; ?></th>
                            <th class="text-white w-15" style="background-color:#001B5E;"><?= $lang['location']; ?></th>
                            <th class="text-white w-25" style="background-color:#001B5E;"><?= $lang['name']; ?></th>
                            <th class="text-white w-25" style="width:10%; background-color:#001B5E;"><?= $lang['action']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div id="pagination" class="mt-3 d-flex gap-2 justify-content-center" style="height: 10%;"></div>
            </div>
        </div>
    </div>

    <!-- 🔧 Modal เพิ่ม -->
    <div class="modal fade" id="newGroupModal" tabindex="-1" aria-labelledby="newGroupModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">สร้างกลุ่มใหม่</h5>
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
                    <button type="button" class="btn btn-primary" onclick="submitnewGroup()">บันทึก</button>
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
    <?php include "../scripts/scriptjs-management-groups.html"; ?>
</body>

</html>