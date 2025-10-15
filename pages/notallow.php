<?php
include '../components/session.php';
?>


<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['errortoken'] ?> - EMS</title>
</head>

<body>
    <?php include "../components/header.php"; ?>
    <div class="d-flex justify-content-center align-items-center flex-column bg-black bg-opacity-75" style="width: 100vw; height: 90vh;">
        <div class="bg-white shadow rounded-2 p-4 w-100" style="max-width: 500px;">
            <h2 class="text-center mb-lg-5 mb-3">ไม่ได้รับอนุญาติในการใช้งาน<br>โปรดติดต่อผู้ดูแลระบบ.</h2>
            <a href="../pages/login.php" class="btn bg-black w-100 clickable text-light" role="button">Reload</a>
        </div>
    </div>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-login.html"; ?>

    <script>

    </script>
</body>

</html>