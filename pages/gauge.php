<?php
include '../components/session.php';
checkLogin();
checkSession();

$EMS_PAGE_TITLE   = $lang['gauge'] . ' - EMS';
$EMS_SHELL_LOCKED = true;            // monitor page — lock the viewport, one inner scroll
include '../components/doc-open.php';
?>
    <link rel="stylesheet" href="../styles/gauge.css">
    <script>const LANG = <?= json_encode($lang, JSON_UNESCAPED_UNICODE) ?>;</script>

<?php include '../components/app-shell-open.php'; ?>

            <div class="gauge-page">

                <!-- ── Context hero ── -->
                <section class="ems-card gauge-hero">
                    <div class="gauge-hero__main">
                        <span class="gauge-hero__meter" id="gauge-hero-meter">—</span>
                        <span class="gauge-hero__crumb">
                            <i class="bi bi-geo-alt-fill"></i> <span id="gauge-hero-loc">—</span>
                            <span class="gauge-hero__sep">/</span>
                            <i class="bi bi-collection-fill"></i> <span id="gauge-hero-grp">—</span>
                        </span>
                    </div>
                    <div class="gauge-hero__meta">
                        <span class="gauge-hero__updated">
                            <i class="bi bi-clock-history"></i>
                            <?= $lang['lastupdate'] ?>: <span id="gauge-hero-updated">—</span>
                        </span>
                        <span class="ems-pill gauge-hero__status" id="gauge-hero-status">
                            <span class="gauge-hero__dot"></span>
                            <span id="gauge-hero-status-text"><?= $lang['livefeed'] ?></span>
                        </span>
                    </div>
                </section>

                <!-- ── Filter toolbar ── -->
                <div class="ems-toolbar gauge-toolbar">

                    <div class="ems-toolbar__group">
                        <span class="ems-toolbar__label">
                            <i class="bi bi-speedometer2"></i> <?= $lang['selectmeter'] ?>
                        </span>
                        <select id="select-meters" class="ems-select" onchange="onMeterChange()"></select>
                    </div>

                    <div class="ems-toolbar__divider"></div>

                    <div class="ems-toolbar__group">
                        <span class="ems-toolbar__label">
                            <i class="bi bi-clock-history"></i> <?= $lang['datalasttime'] ?>
                        </span>
                        <select id="select-filter-value" class="ems-select" onchange="onTimeChange()">
                            <option selected value="1"><?= $lang['now'] ?> (<?= $lang['live'] ?>)</option>
                            <option value="5"><?= $lang['last'] ?> 5 <?= $lang['minutes'] ?></option>
                            <option value="10"><?= $lang['last'] ?> 10 <?= $lang['minutes'] ?></option>
                            <option value="15"><?= $lang['last'] ?> 15 <?= $lang['minutes'] ?></option>
                            <option value="30"><?= $lang['last'] ?> 30 <?= $lang['minutes'] ?></option>
                            <option value="60"><?= $lang['last'] ?> 1 <?= $lang['hour'] ?></option>
                            <option value="120"><?= $lang['last'] ?> 2 <?= $lang['hours'] ?></option>
                            <option value="480"><?= $lang['last'] ?> 4 <?= $lang['hours'] ?></option>
                            <option value="1440"><?= $lang['last'] ?> 1 <?= $lang['day'] ?></option>
                            <option value="4320"><?= $lang['last'] ?> 3 <?= $lang['days'] ?></option>
                            <option value="10080"><?= $lang['last'] ?> 1 <?= $lang['week'] ?></option>
                            <option value="43200"><?= $lang['last'] ?> 1 <?= $lang['month'] ?></option>
                            <option value="259200"><?= $lang['last'] ?> 6 <?= $lang['months'] ?></option>
                            <option value="518400"><?= $lang['last'] ?> 1 <?= $lang['year'] ?></option>
                            <option value="0"><?= $lang['alltime'] ?></option>
                        </select>
                    </div>

                    <div class="ems-toolbar__spacer"></div>

                    <div class="ems-refresh gauge-refresh">
                        <span class="ems-refresh__icon"><i class="bi bi-arrow-repeat"></i></span>
                        <span class="gauge-refresh__label"><?= $lang['refreshevery'] ?></span>
                        <input type="number" id="input-refresh" class="ems-refresh__input" value="15" min="1"
                            max="60" onchange="setRefreshTime()">
                        <span class="gauge-refresh__label"><?= $lang['seconds'] ?></span>
                    </div>
                </div>

                <!-- ── Body: gauge grid + value panel ── -->
                <div class="gauge-body">

                    <section class="ems-card gauge-grid-card">
                        <div class="ems-card__header gauge-grid-card__head">
                            <span class="ems-card__title">
                                <i class="bi bi-grid-3x3-gap-fill"></i> <?= $lang['gauge'] ?>
                            </span>
                            <span class="ems-pill ems-pill--muted" id="gauge-count">0</span>
                        </div>
                        <div id="list-gauge" class="gauge-grid"></div>
                    </section>

                    <aside class="ems-panel ems-panel--sm gauge-side" id="list-data">
                        <div class="ems-panel__header">
                            <span class="gauge-side__dot"></span>
                            <span class="ems-panel__title"><?= $lang['alltype'] ?></span>
                        </div>
                        <div class="ems-panel__body" id="sidebar-body"></div>
                    </aside>

                </div><!-- /.gauge-body -->

            </div><!-- /.gauge-page -->

<?php include '../components/app-shell-close.php'; ?>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-gauge.html"; ?>
<?php include '../components/doc-close.php'; ?>
