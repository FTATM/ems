<?php
include '../components/session.php';
checkLogin();
checkSession();
?>
<!DOCTYPE html>
<html lang="<?= $langCode ?>">
<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['home'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/diagram.css">
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex" style="min-height: 100svh;">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column" style="min-height: 100svh;">
            <?php include "../components/header.php"; ?>
            <div class="flex-grow-1">
                <div
                    class="bg-secondary bg-opacity-25 d-flex flex-wrap mx-3 mt-3 gap-3 align-items-center py-2 diag-info-bar">
                    <label id="location" class="px-2" for="locations">Location : </label>
                    <label id="group" class="px-2" for="groups">Group : </label>
                </div>
                <div class="mx-3 bg-diagram-head d-flex justify-content-center align-items-end" style="height: 480px;">
                    <div id="main-meter" class="d-flex justify-content-center" style="margin-left: 240px;"></div>
                </div>
                <div id="list-meters"></div>
            </div>
            <?php include "../components/footer.php"; ?>
        </div>
    </div>
    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-diagram.html"; ?>
</body>

</html>