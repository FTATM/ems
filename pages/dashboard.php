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

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="bg-secondary bg-opacity-25 d-flex flex-wrap m-3 gap-3 align-items-center py-2">
                <h4 class="px-2">Filter</h4>

                <select id="select-filter-value" class="form-select bg-secondary border-0 text-white" style="width: 10%;" onchange="filterData()">
                    <option value="60">last 1 hours</option>
                    <option value="1440">last 1 days</option>
                    <option value="4320">last 3 days</option>
                    <option selected value="10080">last 1 week</option>
                    <option value="43200">last 1 month</option>
                    <option value="259200">last 6 month</option>
                    <option value="518400">last 1 year</option>
                </select>
            </div>
            <div class="d-flex p-3">
                <div class="d-flex flex-column gap-3" style="width: 90%;">
                    <div class="w-100 d-flex flex-wrap justify-content-between">
                        <div class="rounded w-25 px-2" id="chart-Pie" style="min-height: 400px;  max-width: 350px;"></div>
                        <div class="rounded w-75 px-2" id="chart-linear" style="min-height: 400px; max-width: 1200px;"></div>
                    </div>
                    <div class="w-100 d-flex flex-wrap justify-content-between">
                        <div class="js-gauge demo gauge bg-secondary bg-opacity-10"></div>
                        <div class="rounded w-75 px-2" id="chart-linear-price" style="min-height: 400px; max-width: 1200px;"></div>
                    </div>
                </div>
                <div class="w-25">
                    <div class="mb-3 pb-1" id="infomation" style="background-color: <?= $bgsec ?>;">
                        <h3 class="text-center pb-2"><?= $lang['information'] ?></h3>
                    </div>
                    <div class="" style="background-color: <?= $bgsec ?>;">
                        <h3 class="text-center pb-2"><?= $lang['typeofvalue'] ?></h3>
                        <div id="list-data" class="w-auto d-flex flex-wrap align-items-center">
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
    <?php include "../scripts/scriptjs-dashboard.html"; ?>
</body>

</html>