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
    <title><?= $lang['config'] ?> - EMS</title>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="bg-secondary bg-opacity-25 d-flex justify-content-center" style="min-height: 80svh;">
                <div class="d-flex shadow w-100" style="max-width: 1600px;">
                    <div class="w-20">
                        <h4 class="text py-2 mb-3 text-center bg-white" style="color:#001B5E;"><?= $lang['meter'] ?></h4>
                        <div id="meter-list" class="h-100 w-100">
                            <div class="text-center text-light"><?= $lang['loading'] ?></div>
                        </div>
                    </div>
                    <div class="w-80">
                        <div class="bg-black w-100 d-flex text-center">
                            <div id="menu-config-1" class="menu py-2 w-50 active">Connect</div>
                            <div id="menu-config-2" class="menu py-2 w-50 ">Notify</div>
                        </div>
                        <form id="meter-form">
                            <div id="config" class="w-100 bg-black bg-opacity-25 py-2" style="min-height: 500px;">
                                <div class="w-100 h-100 text-center align-content-center">
                                    <h5><?= $lang['cmisb'] ?></h5>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script id="theme-data" type="application/json">
        <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-management-meters.html"; ?>
</body>

</html>