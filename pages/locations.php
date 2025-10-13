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
    <title><?= $lang['home'] ?> - EMS</title>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="d-flex justify-content-center mt-3">
                <div class="w-50">
                    <div class="my-5 text-center fs-1 fw-bolder"><?=$lang['chooselocate']?></div>
                    <table id="table-location" class="w-100 table-striped table table-borderless bg-light bg-opacity-10 mb-0 w-25">
                        <thead class="text-center">
                            <tr>
                                <!-- <th class="fs-3">ID</th> -->
                                <th class="fs-3"><?= $lang['name'] ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                        </tbody>
                    </table>
                </div>
                <!-- <div class="w-25 justify-content-center d-flex border" style="min-height: 70svh;">
                    <img id="img-showLocation" src="../assets/images/provinces/Thailand.png" width="100%" height="100%" style="max-height: 750px;">
                </div> -->
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
                        <input type="text" class="form-control  text-black" id="new-name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="submitRename()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-locations.html"; ?>
</body>

</html>