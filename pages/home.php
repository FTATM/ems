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
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="bg-secondary bg-opacity-25 d-flex flex-wrap mx-3 mt-3 gap-3 align-items-center py-2">
                <label id="location" class="px-2" for="locations">Location : </label>
                <label id="group" class="px-2" for="groups">Group : </label>
            </div>
            <div class="bg-secondary bg-opacity-25 d-flex flex-column m-3 pt-5 align-items-center" style="min-height: 80svh;">
                <div id="list-meters" class="d-flex gap-2"></div>
            </div>
        </div>
    </div>

    <script id="theme-data" type="application/json">
        <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-home.html"; ?>
</body>

</html>