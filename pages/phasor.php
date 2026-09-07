<?php
include '../components/session.php';
checkLogin();
checkSession();

$EMS_PAGE_TITLE = $lang['gvoltage'] . ' - EMS';
$EMS_SHELL_LOCKED = true;            // monitor page — lock the viewport
include '../components/doc-open.php';
?>
    <link rel="stylesheet" href="../styles/phasor.css">
    <script>const LANG = <?= json_encode($lang, JSON_UNESCAPED_UNICODE) ?>;</script>

<?php include '../components/app-shell-open.php'; ?>

            <div class="phasor-page">

                <!-- ── Filter toolbar ── -->
                <div class="ems-toolbar phasor-toolbar">

                    <div class="ems-toolbar__group">
                        <span class="ems-toolbar__label"><?= $lang['meter'] ?? 'Meter' ?></span>
                        <select id="select-meters" class="ems-select" onchange="filterMeters()"></select>
                    </div>

                    <div class="ems-toolbar__divider"></div>

                    <div class="ems-toolbar__group">
                        <span class="ems-toolbar__label"><?= $lang['datalasttime'] ?></span>
                        <select id="select-time" class="ems-select" onchange="filterMeters()">
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

                    <div class="ems-toolbar__spacer"></div>

                    <div class="ems-refresh phasor-refresh">
                        <span class="ems-refresh__icon"><i class="bi bi-arrow-repeat"></i></span>
                        <span class="phasor-refresh__label"><?= $lang['refreshevery'] ?></span>
                        <input type="number" id="input-refresh" class="ems-refresh__input" value="30" min="1"
                            max="60" onchange="setRefreshTime()">
                        <span class="phasor-refresh__label"><?= $lang['seconds'] ?></span>
                    </div>
                </div>

                <!-- ── Gauge hero ── -->
                <section class="ems-card phasor-hero">
                    <div class="ems-card__header phasor-hero__head">
                        <div class="phasor-hero__titles">
                            <span class="ems-card__title phasor-hero__title">
                                <i class="bi bi-speedometer2"></i>
                                <?= $lang['voltageintegratedgauge'] ?>
                            </span>
                            <span class="phasor-hero__subtitle">
                                <?= $lang['realtime_phase_to_neutral_and_phase_to_phase'] ?>
                            </span>
                        </div>
                        <span class="ems-pill phasor-live">
                            <span class="phasor-live__dot"></span><?= $lang['livefeed'] ?>
                        </span>
                    </div>

                    <div class="phasor-hero__gauge">
                        <canvas id="big-gauge-canvas"></canvas>
                    </div>

                    <div class="phasor-avg">
                        <span class="phasor-avg__value" id="avg-voltage-display">0.0</span>
                        <span class="phasor-avg__label"><?= $lang['avg'] ?> VAC</span>
                    </div>

                    <div class="phase-legend" id="phase-legend"></div>
                </section>

                <!-- ── Phase metric cards ── -->
                <div class="phasor-metrics" id="metric-cards"></div>

            </div><!-- /.phasor-page -->

<?php include '../components/app-shell-close.php'; ?>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-phasor.html"; ?>
<?php include '../components/doc-close.php'; ?>
