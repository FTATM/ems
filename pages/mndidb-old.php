<?php
include '../components/session.php';
?>


<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['home'] ?> - EMS</title>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="d-flex my-4 container bg-light bg-opacity-10 gap-3">
                <div class="w-80 py-2" style="min-height: 500px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>SQL :</div>
                        <div class="d-flex justify-content-end gap-2 my-2">
                            <button class="btn btn-danger btn-sm" onclick="cleartext()">clear all</button>
                            <button id="btn-run" class="btn btn-success btn-sm" onclick="excuteStringSQL()">excute SQL</button>
                        </div>
                    </div>
                    <div id="sqltext" contenteditable="true" class="rounded" oninput="highlightSQL()"
                        style="width:100%; min-height:200px; background:#f8f9fa; color:black; padding:8px; font-family:monospace; border:1px solid #ccc;">
                    </div>
                    <div>Result :</div>
                    <div id="output" style="min-height: 100px;" class="form-control form-control-sm rounded bg-light bg-opacity-75"></div>
                </div>
                <div class="w-20 d-flex flex-column">
                    <div class="text-center fs-4 mb-2 fw-bolder">Menu</div>
                    <div class="my-2 bg-black bg-opacity-10 p-2 rounded border">
                        <p>Update datetime in database for stabilizing all</p>
                        <p id="status-update-date"></p>
                        <button id="btn-update-all" class="btn btn-primary" onclick="updatedatetime()">Update</button>
                    </div>
                </div>
            </div>

        </div>

        <script id="theme-data" type="application/json">
            <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
        </script>
    </div>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-mndidb.html"; ?>
</body>

</html>