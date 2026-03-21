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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="../styles/phasor.css">
    <style>
    /* ─── layout only — colours handled by phasor.css variables ─── */
    .gauge-layout {
        padding: 0;
        margin-top: .75rem;
    }
    </style>
</head>

<body>
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column phasor-main-col">
            <?php include "../components/header.php"; ?>
            <div class="w-100 d-flex flex-column phasor-content-wrap">

                <!-- ══ FILTER BAR ══ -->
                <div class="dash-filter-bar"
                    style="border-radius:0; border-left:none; border-right:none; margin-bottom:0;">

                    <!-- Meter -->
                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['meter'] ?? 'Meter' ?></span>
                        <select id="select-meters" class="dash-select" onchange="filterMeters()"></select>
                    </div>

                    <div class="dash-divider"></div>

                    <!-- Time range -->
                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['datalasttime'] ?></span>
                        <select id="select-time" class="dash-select" onchange="filterMeters()">
                            <option selected value="now"><?= $lang['now'] ?> (<?= $lang['live'] ?>)</option>
                            <option value="5"><?= $lang['last'] ?> 5 <?= $lang['minutes'] ?></option>
                            <option value="10"><?= $lang['last'] ?> 10 <?= $lang['minutes'] ?></option>
                            <option value="15"><?= $lang['last'] ?> 15 <?= $lang['minutes'] ?></option>
                            <option value="30"><?= $lang['last'] ?> 30 <?= $lang['minutes'] ?></option>
                            <option value="60"><?= $lang['last'] ?> 1 <?= $lang['hour'] ?></option>
                            <option value="120"><?= $lang['last'] ?> 2 <?= $lang['hours'] ?></option>
                            <option value="240"><?= $lang['last'] ?> 4 <?= $lang['hours'] ?></option>
                            <option value="1440"><?= $lang['last'] ?> 1 <?= $lang['day'] ?></option>
                            <option value="4320"><?= $lang['last'] ?> 3 <?= $lang['days'] ?></option>
                            <option value="10080"><?= $lang['last'] ?> 1 <?= $lang['week'] ?></option>
                            <option value="43200"><?= $lang['last'] ?> 1 <?= $lang['month'] ?></option>
                            <option value="259200"><?= $lang['last'] ?> 6 <?= $lang['months'] ?></option>
                            <option value="518400"><?= $lang['last'] ?> 1 <?= $lang['year'] ?></option>
                            <option value="all"><?= $lang['alltime'] ?></option>
                        </select>
                    </div>

                    <div class="dash-divider"></div>

                    <!-- Refresh -->
                    <div class="dash-refresh-wrap">
                        <div class="phasor-refresh-box">
                            <span class="material-icons-outlined phasor-refresh-spin">sync</span>
                            <span class="phasor-refresh-label"><?= $lang['refreshevery'] ?></span>
                            <input type="number" id="input-refresh" class="phasor-refresh-input" value="3" min="1"
                                max="60" onchange="setRefreshTime()">
                            <span class="phasor-refresh-label"><?= $lang['seconds'] ?></span>
                        </div>
                    </div>
                </div>

                <div class="w-100 px-3 d-flex flex-column phasor-gauge-wrap">

                    <!-- ══ GAUGE LAYOUT (แทน div#list-gauge เดิม) ══ -->
                    <div class="gauge-layout">

                        <!-- LEFT: Single gauge with 6 needles -->
                        <div class="gauge-left-panel">
                            <div class="gauge-panel-header">
                                <div>
                                    <div class="gauge-panel-title"><?= $lang['voltageintegratedgauge'] ?></div>
                                    <div class="gauge-panel-subtitle">
                                        <?= $lang['realtime_phase_to_neutral_and_phase_to_phase'] ?></div>
                                </div>
                                <div class="live-feed-badge"><span class="live-dot"></span><?= $lang['livefeed'] ?>
                                </div>
                            </div>
                            <div class="big-gauge-wrap">
                                <canvas id="big-gauge-canvas" width="440" height="440"></canvas>
                            </div>
                            <div class="avg-display">
                                <span class="avg-value" id="avg-voltage-display">0.0</span>
                                <span class="avg-label">AVG VAC</span>
                            </div>
                            <div class="phase-legend" id="phase-legend"></div>
                        </div>

                        <!-- RIGHT: Metric cards -->
                        <div class="gauge-right-panel" id="metric-cards"></div>

                    </div><!-- /.gauge-layout -->

                </div><!-- /px-3 -->

                <?php include "../components/footer.php"; ?>

            </div><!-- /phasor-content-wrap -->
        </div>
    </div>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-phasor.html"; ?>
</body>


</html>