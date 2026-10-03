<?php
include '../components/session.php';
checkLogin();

if (!isset($_SESSION['gid'])) {
    header("Location: ../pages/locations.php?step=2");
    exit;
}
$_SESSION['tid'] = 2;

$meterId = (int)($_GET['id'] ?? 0);
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['meter_detail_water'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/overview-water.css">
    <link rel="stylesheet" href="../styles/meter-detail-water.css">
    <script>
    const LANG = <?= json_encode($lang) ?>;
    const INITIAL_METER_ID = <?= (int)$meterId ?>;
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
                        <select id="select-meters" class="dash-select" onchange="changeSelectMeter()"></select>
                    </div>

                    <div class="dash-divider"></div>

                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['from'] ?></span>
                        <input type="date" id="date-start" class="dash-select" onchange="changeSelectMeter()">
                    </div>

                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['to'] ?></span>
                        <input type="date" id="date-end" class="dash-select" onchange="changeSelectMeter()">
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

                    <!-- ── Meter info + latest values ── -->
                    <div class="rd-grid">
                        <div class="rd-card">
                            <div class="rd-card__title"><i class="bi bi-info-circle-fill"></i> <?= $lang['meterinfo'] ?></div>
                            <div class="rd-info-grid" id="md-meter-info"></div>
                        </div>
                        <div class="rd-card">
                            <div class="rd-card__title"><i class="bi bi-water"></i> <?= $lang['group_flow'] ?> / <?= $lang['group_velocity'] ?></div>
                            <div class="wd-stats wd-stats--2col">
                                <div class="wd-stat" id="wd-stat-flow">
                                    <span class="wd-stat__label"><i class="bi bi-water"></i> <?= $lang['group_flow'] ?></span>
                                    <span class="wd-stat__value">—</span>
                                </div>
                                <div class="wd-stat" id="wd-stat-velocity">
                                    <span class="wd-stat__label"><i class="bi bi-speedometer"></i> <?= $lang['group_velocity'] ?></span>
                                    <span class="wd-stat__value">—</span>
                                </div>
                                <div class="wd-stat" id="wd-stat-poscum">
                                    <span class="wd-stat__label"><i class="bi bi-arrow-up-circle"></i> <?= $lang['positive_cumulative'] ?></span>
                                    <span class="wd-stat__value">—</span>
                                </div>
                                <div class="wd-stat" id="wd-stat-negcum">
                                    <span class="wd-stat__label"><i class="bi bi-arrow-down-circle"></i> <?= $lang['negative_cumulative'] ?></span>
                                    <span class="wd-stat__value">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Charts: one per metric ── -->
                    <div class="md-chart-grid">
                        <div class="dash-card dash-card--line md-chart-card">
                            <div class="dash-card-header"><div class="dash-card-title"><i class="bi bi-water"></i> <?= $lang['group_flow'] ?></div></div>
                            <div class="dash-card-body"><div id="chart-flow"></div></div>
                        </div>
                        <div class="dash-card dash-card--line md-chart-card">
                            <div class="dash-card-header"><div class="dash-card-title"><i class="bi bi-speedometer"></i> <?= $lang['group_velocity'] ?></div></div>
                            <div class="dash-card-body"><div id="chart-velocity"></div></div>
                        </div>
                        <div class="dash-card dash-card--line md-chart-card">
                            <div class="dash-card-header"><div class="dash-card-title"><i class="bi bi-arrow-up-circle"></i> <?= $lang['positive_cumulative'] ?></div></div>
                            <div class="dash-card-body"><div id="chart-poscum"></div></div>
                        </div>
                        <div class="dash-card dash-card--line md-chart-card">
                            <div class="dash-card-header"><div class="dash-card-title"><i class="bi bi-arrow-down-circle"></i> <?= $lang['negative_cumulative'] ?></div></div>
                            <div class="dash-card-body"><div id="chart-negcum"></div></div>
                        </div>
                    </div>

                    <!-- ── Data table ── -->
                    <h6 class="ov-subheading"><i class="bi bi-table"></i> <?= $lang['table'] ?></h6>
                    <div class="ov-table-wrap">
                        <table class="ov-table">
                            <thead>
                                <tr>
                                    <th><?= $lang['datalasttime'] ?></th>
                                    <th><?= $lang['group_flow'] ?></th>
                                    <th><?= $lang['group_velocity'] ?></th>
                                    <th><?= $lang['positive_cumulative'] ?></th>
                                    <th><?= $lang['negative_cumulative'] ?></th>
                                </tr>
                            </thead>
                            <tbody id="md-table-body"></tbody>
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
    <?php include "../scripts/scriptjs-meter-detail-water.html"; ?>
</body>

</html>
