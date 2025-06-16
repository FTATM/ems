<?php
include '../components/session.php';
?>


<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['home'] ?> - AMS</title>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="d-flex flex-wrap justify-content-center gap-3 m-4">
                <div id="card" onclick="" style="background-color: <?= $bgsec ?>;">
                    <img src="../assets/images/dashboard.png" class="w-75">
                    <div class="text"><?= $lang['dashboard'] ?></div>
                </div>
                <div id="card" onclick="" style="background-color: <?= $bgsec ?>;">
                    <img src="../assets/images/bills.png" class="w-75">
                    <div class="text"><?= $lang['bill'] ?></div>
                </div>
                <div id="card" onclick="" style="background-color: <?= $bgsec ?>;">
                    <img src="../assets/images/payment.png" class="w-75">
                    <div class="text"><?= $lang['payment'] ?></div>
                </div>
                <div id="card" onclick="" style="background-color: <?= $bgsec ?>;">
                    <img src="../assets/images/meter.png" class="w-75">
                    <div class="text"><?= $lang['meter'] ?></div>
                </div>
                <div id="card" onclick="" style="background-color: <?= $bgsec ?>;">
                    <img src="../assets/images/member.png" class="w-75">
                    <div class="text"><?= $lang['member'] ?></div>
                </div>
                <div id="card" onclick="" style="background-color: <?= $bgsec ?>;">
                    <img src="../assets/images/system.png" class="w-75">
                    <div class="text"><?= $lang['system'] ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php include "../scripts/scriptjs.html"; ?>
</body>

</html>