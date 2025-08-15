<?php
include '../components/session.php';
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['allmeter'] ?> - AMS</title>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="w-100 py-2" style="background-color: #001B5E;">
                <h4 class="text-center text-white">All Meter</h4>
            </div>
            <div class="bg-secondary bg-opacity-25" style="min-height: 80svh;">
                <table id="table-meter" class="table table-bordered table-striped" style="height: 90%;">
                </table>
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