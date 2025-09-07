<?php
include '../components/session.php';
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['allmeter'] ?> - EMS</title>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="py-2 justify-content-center d-flex" style="min-height: 600px;">
                <div class="d-flex flex-column">
                    <div class="px-4 d-flex justify-content-start my-3 gap-2 bg-white bg-opacity-10 py-3">
                        <div class="d-flex align-items-center gap-2" style="width: 35svw;">
                            <label for="select-meters" class="form-label form-label-sm text-end w-50" style="max-width: 15vw;">Select meter :</label>
                            <select id="select-meters" class="form-select form-select-sm" onchange="checkTimeChange()">

                            </select>
                            <label for="select-time" class="form-label form-label-sm text-end w-50" style="max-width: 15vw;">Data last time :</label>
                            <select id="select-time" class="form-select form-select-sm w-50" onchange="checkTimeChange()">
                                <!-- <option value="-">-- please select --</option> -->
                                <option selected value="1">Now</option>
                                <option value="5">5 mins</option>
                                <option value="10">10 mins</option>
                                <option value="15">15 mins</option>
                                <option value="30">30 mins</option>
                                <option value="60">1 hour</option>
                                <option value="120">2 hours</option>
                                <option value="240">4 hours</option>
                                <option value="0">All Time</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="width: 15svw;">
                            <label for="input-refresh" class="form-label form-label-sm text-end w-50" style="max-width: 15vw;">Refresh every :</label>
                            <input type="number" id="input-refresh" class="form-control form-control-sm ms-2 w-25" style="max-width: 15vw;" placeholder="Refresh every (seconds)" value="3" min="1" max="30" onchange="setRefreshTime()">
                            <label for="input-refresh" class="form-label form-label-sm " style="max-width: 15vw;">Secondary</label>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap justify-content-center">
                        <div id="list-gauge" class="w-80 d-flex flex-wrap justify-content-center gap-2"> </div>
                        <div id="list-data" class="w-15 d-flex flex-wrap gap-1 align-items-center"> </div>
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
    <?php include "../scripts/scriptjs-gauge.html"; ?>
</body>

</html>