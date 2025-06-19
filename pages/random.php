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
            <div class="d-flex flex-column gap-3 my-4 container">
                <div class="d-flex flex-column w-25">
                    <label class="text align-middle">Name Device</label>
                    <input id="name" type="text" class="form-control" placeholder="new device name."/>
                </div>
                <div class="d-flex gap-4">
                    <label class="text text-center align-middle">Form</label>
                    <input id="form" type="text" class="form-control" placeholder="form value 0 - 9999999+"/>
                    -
                    <label class="text text-center align-middle">To</label>
                    <input id="to" type="text" class="form-control" placeholder="to value 0 - 9999999+"/>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" name="" id="" class="btn btn-primary" onclick="adddata(1)">
                        + 1
                    </button>
                    <button type="button" name="" id="" class="btn btn-success" onclick="adddata(5)">
                        + 5
                    </button>
                    <button type="button" name="" id="" class="btn btn-warning" onclick="adddata(10)">
                        + 10
                    </button>
                    <button type="button" name="" id="" class="btn btn-danger" onclick="adddata(20)">
                        + 20
                    </button>
                </div>

            </div>
        </div>


    </div>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-random.html"; ?>
</body>

</html>