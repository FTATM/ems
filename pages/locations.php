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
            <div class="d-flex justify-content-center mt-3">
                <table id="table-location" class="table-striped table table-borderless bg-light bg-opacity-10 mb-0 w-25">
                    <thead class="text-center">
                        <tr>
                            <th class="fs-3">ID</th>
                            <th class="fs-3"><?= $lang['location'] ?></th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                    </tbody>
                </table>
                <div class="w-50 bg-white" style="min-height: 50svh;">
                    <img id="img-showLocation" width="40%" height="500px">
                </div>
            </div>
        </div>
    </div>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-locations.html"; ?>
</body>

</html>