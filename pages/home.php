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

<body style="background-color: <?= $bg ?>; color: <?=$text ?>;">
    <?php include "../components/header.php"; ?>
    <div class="w-100 h-100">
    </div>

    <?php include "../components/sidemenu.php"; ?>
    <?php include "../scripts/scriptjs.html"; ?>
</body>

</html>