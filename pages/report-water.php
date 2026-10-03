<?php
include '../components/session.php';
checkLogin();

if (!isset($_SESSION['gid'])) {
    header("Location: ../pages/locations.php?step=2");
    exit;
}
$_SESSION['tid'] = 2;
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['report_water'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/overview-water.css">
    <link rel="stylesheet" href="../styles/meter-detail-water.css">
    <link rel="stylesheet" href="../styles/report-water.css">
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

                <!-- ── Filter Bar ── -->
                <div class="dash-filter-bar">

                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['location'] ?></span>
                        <div class="dash-location-badge">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span id="location"></span>
                        </div>
                    </div>

                    <div class="dash-divider"></div>

                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['group'] ?></span>
                        <div class="dash-group-badge">
                            <i class="bi bi-collection-fill"></i>
                            <span id="group"></span>
                        </div>
                    </div>

                    <div class="dash-divider"></div>

                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['meter'] ?></span>
                        <select id="select-meters" class="dash-select" onchange="loadReport()"></select>
                    </div>

                    <div class="dash-divider"></div>

                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['from'] ?></span>
                        <input type="date" id="date-start" class="dash-select" onchange="loadReport()">
                    </div>

                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['to'] ?></span>
                        <input type="date" id="date-end" class="dash-select" onchange="loadReport()">
                    </div>

                    <div class="dash-divider"></div>

                    <div class="dash-filter-group">
                        <span class="dash-filter-label">&nbsp;</span>
                        <div class="rw-export-group">
                            <button type="button" class="rw-btn rw-btn--primary" onclick="exportExcel()">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                            <button type="button" class="rw-btn" onclick="exportCsv()">
                                <i class="bi bi-file-earmark-text"></i> CSV
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ── No meters state ── -->
                <div id="ov-empty" class="ov-empty" hidden>
                    <i class="bi bi-droplet"></i>
                    <h3><?= $lang['no_water_meter_found'] ?></h3>
                    <p><?= $lang['no_water_meter_found_hint'] ?></p>
                    <a href="locations.php?step=2" class="ov-empty__back">
                        <i class="bi bi-chevron-left"></i> <?= $lang['back'] ?>
                    </a>
                </div>

                <div id="ov-body">
                    <div class="rw-summary" id="rw-summary"></div>

                    <div class="ov-table-wrap">
                        <table class="ov-table">
                            <thead>
                                <tr>
                                    <th><?= $lang['nmeters'] ?></th>
                                    <th><?= $lang['datalasttime'] ?></th>
                                    <th><?= $lang['group_flow'] ?></th>
                                    <th><?= $lang['group_velocity'] ?></th>
                                    <th><?= $lang['positive_cumulative'] ?></th>
                                    <th><?= $lang['negative_cumulative'] ?></th>
                                </tr>
                            </thead>
                            <tbody id="rw-table-body"></tbody>
                        </table>
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
    <?php include "../scripts/scriptjs-report-water.html"; ?>
</body>

</html>
