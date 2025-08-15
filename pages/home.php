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

<body class="body" style=" color: <?= $text ?>; min-height: 100svh;">
    <div class="background-blur"></div>
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="bg-opacity-100" style="min-height: 60vh;"></div>
            <div class="px-4 text-end align-content-lg-center" style="width: 95%;" >
                <input class="btn btn-outline-light text-white" value="All" onclick="window.location.href='../pages/allmeter.php';">
            </div>
            <div id="body" class="w-100 h-100 d-flex flex-wrap justify-content-center align-items-end p-3 gap-3 row-gap-2"></div>
        </div>
    </div>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-home.html"; ?>
</body>

</html>