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
                <label id="location" for="locations"> : </label>
                <label id="group" for="groups">Group : </label>
                <select id="select-meters" class="form-select bg-secondary border-0 text-white" style="width: 15%;" onchange="filterMeters()">

                </select>
                <label for="select-filter-value">Last time : </label>
                <select id="select-filter-value" class="form-select bg-secondary border-0 text-white" style="width: 10%;" onchange="filterMeters()">
                    <option selected value="60">last 1 hour</option>
                    <option value="1440">last 1 days</option>
                    <option value="4320">last 3 days</option>
                    <option value="10080">last 1 week</option>
                    <option value="43200">last 1 month</option>
                    <option value="259200">last 6 month</option>
                    <option value="518400">last 1 year</option>
                </select>
                <label for="input-refresh" class="form-label form-label-sm text-end w-10" style="max-width: 15vw;">Refresh every :</label>
                <input type="number" id="input-refresh" class="form-control form-control-sm ms-2" style="width: 100px;" placeholder="Refresh every (seconds)" value="15" min="1" max="30" onchange="setRefreshTime()">
                <label for="input-refresh" class="form-label form-label-sm " style="max-width: 15vw;">Seconds</label>
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
                    <div class="mb-3 pb-1 py-2" id="infomation" style="background-color: <?= $bgsec ?>;">
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