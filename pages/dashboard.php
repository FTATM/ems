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
            <div class="d-flex">
                <div class="d-flex flex-column w-75">
                    <div class="w-100 text fs-4 text-center align-items-center py-4">
                        <p><?= $lang['dashboard'] ?></p>
                    </div>
                    <div class="w-100 d-flex flex-wrap gap-4 justify-content-center">
                        <div class="d-flex w-90 flex-wrap justify-content-between ">
                            <div class="summary bg-white fs-3 d-flex">
                                <div class="bg-primary" style="min-width: 30%; min-height: 100%;"></div>
                                <div class="text-center w-100">
                                    <h5>summary</h5>
                                    54
                                </div>
                            </div>
                            <div class="summary bg-white fs-3 d-flex">
                                <div class="bg-success" style="min-width: 30%; min-height: 100%;"></div>
                                <div class="text-center w-100">
                                    <h5>summary</h5>
                                    54
                                </div>
                            </div>
                            <div class="summary bg-white fs-3 d-flex">
                                <div class="bg-warning" style="min-width: 30%; min-height: 100%;"></div>
                                <div class="text-center w-100">
                                    <h5>summary</h5>
                                    54
                                </div>
                            </div>
                            <div class="summary bg-white fs-3 d-flex">
                                <div class="bg-black" style="min-width: 30%; min-height: 100%;"></div>
                                <div class="text-center w-100">
                                    <h5>summary</h5>
                                    54
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between" style="width: 90%;">
                            <div class="" id="chart-bar" style="width: 100%; min-height: 400px;"></div>
                            <!-- <div class="" id="chart-pie" style="width: 49%; min-height: 400px;"></div> -->
                        </div>
                        <!-- <div class="" id="chart-linear" style="width: 90%; min-height: 400px;"></div> -->
                    </div>
                </div>
                <div id="list-data" class="w-25 d-flex flex-column align-items-center">
                    
                </div>
            </div>
        </div>



    </div>



    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-dashboard.html"; ?>
</body>

</html>