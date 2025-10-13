<?php
include '../components/session.php';
checkLogin();
checkSession();
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
            <div class="w-100 py-2 d-flex justify-content-center position-relative" style="background-color: #001B5E;">
                <h4 class="text-center text-white"><?= $lang['allmeter'] ?></h4>
                <div class="d-flex gap-2 position-absolute end-0 px-3" style="top:0.5rem;">
                    <label class="form-label form-label-sm text-end text-nowrap " style="max-width: 15vw;">Refresh every :</label>
                    <input type="number" id="input-refresh" class="form-control form-control-sm ms-2" style="width: 100px;" placeholder="Refresh every (seconds)" value="15" min="1" max="30" onchange="setRefreshTime()">
                    <label class="form-label form-label-sm " style="max-width: 15vw;">Seconds</label>
                </div>
            </div>
            <div class="bg-secondary bg-opacity-25" style="min-height: 80svh;">
                <div class="table-responsive w-100">
                    <table id="table-meter" class="w-100 table table-bordered table-striped" style="height: 90%;">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div id="pagination" class="mt-3 d-flex gap-2 justify-content-center" style="height: 10%;"></div>
            </div>
        </div>
    </div>


    <script id="theme-data" type="application/json">
        <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-allmeter.html"; ?>
</body>

</html>