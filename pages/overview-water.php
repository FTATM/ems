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
    <title><?= $lang['water'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/overview-water.css">
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
                        <div style="display:flex; gap:6px; align-items:center; width:100%;">
                            <select id="select-meters" class="dash-select" onchange="changeSelectMeter()"></select>
                            <a id="link-meter-detail" href="meter-detail-water.php" class="wd-detail-link" title="<?= $lang['meter_detail_water'] ?>">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="dash-divider"></div>

                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['datalasttime'] ?></span>
                        <select id="filter-lasttime" class="dash-select" onchange="changeSelectMeter()">
                            <option value="5"><?= $lang['last'] ?> 5 <?= $lang['minutes'] ?></option>
                            <option value="10"><?= $lang['last'] ?> 10 <?= $lang['minutes'] ?></option>
                            <option value="15"><?= $lang['last'] ?> 15 <?= $lang['minutes'] ?></option>
                            <option value="30"><?= $lang['last'] ?> 30 <?= $lang['minutes'] ?></option>
                            <option selected value="60"><?= $lang['last'] ?> 1 <?= $lang['hour'] ?></option>
                            <option value="120"><?= $lang['last'] ?> 2 <?= $lang['hours'] ?></option>
                            <option value="480"><?= $lang['last'] ?> 4 <?= $lang['hours'] ?></option>
                            <option value="1440"><?= $lang['last'] ?> 1 <?= $lang['day'] ?></option>
                            <option value="4320"><?= $lang['last'] ?> 3 <?= $lang['days'] ?></option>
                            <option value="10080"><?= $lang['last'] ?> 1 <?= $lang['week'] ?></option>
                            <option value="43200"><?= $lang['last'] ?> 1 <?= $lang['month'] ?></option>
                        </select>
                    </div>

                    <div class="dash-divider"></div>
                    <div class="dash-refresh-wrap">
                        <span class="dash-filter-label"><?= $lang['refreshevery'] ?></span>
                        <div class="dash-refresh-inner">
                            <input type="number" id="input-refresh" class="dash-refresh-input" value="15" min="1"
                                max="30" onchange="setRefreshTime()">
                            <span class="dash-filter-label"><?= $lang['seconds'] ?></span>
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

                <!-- ── Main Body ── -->
                <div class="dash-body" id="ov-body">

                    <div class="dash-body-left">

                        <!-- ── Flow / Velocity / Cumulative stat cards ── -->
                        <div class="wd-stats" id="wd-stats">
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

                        <div class="dash-card dash-card--line ov-card--full">
                            <div class="dash-card-header">
                                <div class="dash-card-title">
                                    <i class="bi bi-graph-up"></i><?= $lang['energy_usage_graph_kw'] ?>
                                </div>
                            </div>
                            <div class="dash-card-body">
                                <div id="chart-linear"></div>
                            </div>
                        </div>

                        <div class="dash-card dash-card--line wd-card--summary">
                            <div class="dash-card-header">
                                <div class="dash-card-title">
                                    <i class="bi bi-bar-chart-fill"></i><?= $lang['water_summary_chart'] ?>
                                </div>
                            </div>
                            <div class="dash-card-body">
                                <div id="chart-summary"></div>
                            </div>
                        </div>
                    </div>

                    <div class="dash-right-panel">

                        <div class="dash-side-card dash-card--meter">
                            <div class="dash-side-header">
                                <div class="dash-values-title">
                                    <i class="bi bi-info-circle-fill"></i><?= $lang['meterinfo'] ?>
                                </div>
                            </div>
                            <div class="dash-side-scroll dash-side-scroll--info">
                                <div id="infomation">
                                    <div class="dash-skeleton">
                                        <div class="dash-skeleton-line" style="width:60%"></div>
                                        <div class="dash-skeleton-line" style="width:90%"></div>
                                        <div class="dash-skeleton-line" style="width:75%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dash-side-card dash-side-card--types">
                            <div class="dash-side-header">
                                <div class="dash-values-title">
                                    <i class="bi bi-grid-fill"></i> <?= $lang['typeofvalue'] ?>
                                </div>
                            </div>
                            <div class="dash-side-scroll dash-side-scroll--types">
                                <div id="list-data">
                                    <div class="dash-skeleton" style="width:100%">
                                        <div class="dash-skeleton-line" style="width:80%"></div>
                                        <div class="dash-skeleton-line" style="width:65%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    <?php include "../scripts/scriptjs-overview-water.html"; ?>
</body>

</html>
