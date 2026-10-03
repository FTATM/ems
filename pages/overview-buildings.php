<?php
include '../components/session.php';
checkLogin();
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['building_overview'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/overview-room.css">
    <link rel="stylesheet" href="../styles/overview-buildings.css">
    <script>
    const LANG = <?= json_encode($lang) ?>;
    </script>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>">
    <div id="main">
        <?php include "../components/sidemenu.php"; ?>

        <div class="ov-wrapper">
            <?php include "../components/header.php"; ?>

            <div class="ov-content">

                <div class="rd-breadcrumb">
                    <span class="rd-breadcrumb__current">
                        <i class="bi bi-buildings-fill"></i> <?= $lang['building_overview'] ?>
                    </span>
                </div>

                <!-- ── Portfolio-wide totals ── -->
                <div class="rd-stats" id="ob-totals"></div>

                <!-- ── Empty state ── -->
                <div id="ov-empty" class="ov-empty" hidden>
                    <i class="bi bi-buildings"></i>
                    <h3><?= $lang['no_building_found'] ?></h3>
                    <p><?= $lang['no_building_found_hint'] ?></p>
                </div>

                <div class="room-grid-wrap">
                    <div class="room-grid" id="building-grid">
                        <div class="room-card room-card--skeleton"></div>
                        <div class="room-card room-card--skeleton"></div>
                        <div class="room-card room-card--skeleton"></div>
                    </div>
                </div>

            </div>

            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-overview-buildings.html"; ?>
</body>

</html>
