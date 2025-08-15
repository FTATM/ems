<?php
include '../components/session.php';
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
                    <div class="w-20" >
                        <h4 class="text py-2 mb-3 text-center bg-white" style="color:#001B5E;">Meter</h4>
                        <div id="meter-list" class="h-100 w-100">
                            <div class="text-center text-light"><?= $lang['loading'] ?></div>
                        </div>
                    </div>
                    <div class="w-80">
                        <div class="bg-black w-100 d-flex text-center">
                            <div id="menu-config-1" class="menu py-2 w-50 active">Generals</div>
                            <div id="menu-config-2" class="menu py-2 w-50 ">Notify</div>
                            <div id="menu-config-3" class="menu py-2 w-50 ">Connect</div>
                        </div>
                        <div id="config" class="w-100 bg-black bg-opacity-25 py-2" style="min-height: 500px;">
                            <div class="w-100 h-100 text-center align-content-center">
                                <h5><?= $lang['cmisb'] ?></h5>
                            </div>
                        </div>
                        <div class="w-100 text-end my-2">
                            <input class="btn btn-sm btn-primary bg-opacity-50" value="Save">
                            <input class="btn btn-sm btn-danger" value="Cancel">
                        </div>
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
    <?php include "../scripts/scriptjs-configmeter.html"; ?>
</body>

</html>