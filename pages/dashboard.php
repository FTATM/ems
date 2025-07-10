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

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="d-flex">
                <div class="d-flex flex-column" style="width: 90%;">
                    <div class="w-100 d-flex flex-wrap justify-content-center">
                        <div class="rounded w-25 px-2" id="chart-Pie" style="min-height: 400px;  max-width: 350px;"></div>
                        <div class="rounded w-75 px-2" id="chart-linear" style="min-height: 400px; max-width: 1200px;"></div>
                    </div>
                </div>
                <div id="list-data" class="w-auto d-flex flex-column align-items-center">

                </div>
                <!-- <div class="d-flex w-90 flex-wrap justify-content-between ">
                    <div class="summary fs-3 d-flex" style="background-color: <?= $bgsec ?>;">
                        <div class="bg-primary" style="min-width: 30%; min-height: 100%;"></div>
                        <div class="text-center w-100">
                            <h5>summary</h5>
                            54
                        </div>
                    </div>
                    <div class="summary fs-3 d-flex" style="background-color: <?= $bgsec ?>;">
                        <div class="bg-success" style="min-width: 30%; min-height: 100%;"></div>
                        <div class="text-center w-100">
                            <h5>summary</h5>
                            54
                        </div>
                    </div>
                    <div class="summary fs-3 d-flex" style="background-color: <?= $bgsec ?>;">
                        <div class="bg-warning" style="min-width: 30%; min-height: 100%;"></div>
                        <div class="text-center w-100">
                            <h5>summary</h5>
                            54
                        </div>
                    </div>
                </div> -->
            </div>
        </div>



    </div>


    <script id="theme-data" type="application/json">
        <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-dashboard.html"; ?>
</body>

</html>