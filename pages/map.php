<?php
include '../components/session.php';
checkLogin();
checkSession();

$EMS_PAGE_TITLE = $lang['overview'] . ' - EMS';
$EMS_SHELL_LOCKED = true;            // monitor page — lock the viewport
include '../components/doc-open.php';
?>
    <link rel="stylesheet" href="../styles/map.css">

<?php include '../components/app-shell-open.php'; ?>

            <div class="overview-page">

                <!-- ── Toolbar ── -->
                <div class="ems-toolbar overview-toolbar">
                    <div class="overview-toolbar__title">
                        <span class="overview-toolbar__icon"><i class="bi bi-grid-1x2-fill"></i></span>
                        <?= $lang['overview'] ?>
                    </div>
                    <div class="ems-toolbar__spacer"></div>
                    <div class="overview-refresh">
                        <i class="bi bi-arrow-repeat overview-refresh__spin"></i>
                        <span class="overview-refresh__label"><?= $lang['refreshevery'] ?></span>
                        <input type="number" id="input-refresh" class="overview-refresh__input" value="15" min="1"
                            max="30" onchange="setRefreshTime()">
                        <span class="overview-refresh__label"><?= $lang['seconds'] ?></span>
                    </div>
                </div>

                <!-- ── Body: three columns ── -->
                <div class="overview-body">

                    <!-- Left panel: selected-meter readout -->
                    <aside class="ems-panel overview-panel overview-panel--left">

                        <div class="overview-panel__head">
                            <i class="bi bi-speedometer2"></i>
                            <span class="overview-meter-name" id="name-meter">—</span>
                        </div>

                        <div class="overview-panel__scroll">

                            <!-- Gauge (SVG arc — kW) -->
                            <div class="overview-gauge-wrap" id="gauge-kW">
                                <svg class="gauge-svg" viewBox="0 0 200 128" role="img" aria-label="kW">
                                    <path class="gauge-track" d="M 16 104 A 84 84 0 0 1 184 104" />
                                    <path class="gauge-arc" id="gauge-arc" d="M 16 104 A 84 84 0 0 1 184 104" />
                                    <text class="gauge-value" id="gauge-value" x="100" y="78"
                                        text-anchor="middle">0</text>
                                    <text class="gauge-unit" x="100" y="97" text-anchor="middle">kW</text>
                                    <text class="gauge-end" x="14" y="126" text-anchor="start">0</text>
                                    <text class="gauge-end" id="gauge-max" x="186" y="126"
                                        text-anchor="end">10</text>
                                </svg>
                            </div>

                            <!-- Voltage row -->
                            <div class="overview-section-label"><?= $lang['voltage'] ?></div>
                            <div class="overview-data-row">
                                <div class="overview-data-item">
                                    <span class="overview-data-item__label"><?= $lang['phase'] ?> A</span>
                                    <input id="VoltageA" type="text" class="overview-data-input" readonly>
                                </div>
                                <div class="overview-data-item">
                                    <span class="overview-data-item__label"><?= $lang['phase'] ?> B</span>
                                    <input id="VoltageB" type="text" class="overview-data-input" readonly>
                                </div>
                                <div class="overview-data-item">
                                    <span class="overview-data-item__label"><?= $lang['phase'] ?> C</span>
                                    <input id="VoltageC" type="text" class="overview-data-input" readonly>
                                </div>
                            </div>

                            <!-- Current row -->
                            <div class="overview-section-label"><?= $lang['current'] ?></div>
                            <div class="overview-data-row">
                                <div class="overview-data-item">
                                    <span class="overview-data-item__label"><?= $lang['phase'] ?> A</span>
                                    <input id="CurrentA" type="text" class="overview-data-input" readonly>
                                </div>
                                <div class="overview-data-item">
                                    <span class="overview-data-item__label"><?= $lang['phase'] ?> B</span>
                                    <input id="CurrentB" type="text" class="overview-data-input" readonly>
                                </div>
                                <div class="overview-data-item">
                                    <span class="overview-data-item__label"><?= $lang['phase'] ?> C</span>
                                    <input id="CurrentC" type="text" class="overview-data-input" readonly>
                                </div>
                            </div>

                            <!-- Pf & Frequency -->
                            <div class="overview-data-row overview-data-row--half">
                                <div class="overview-data-item">
                                    <span class="overview-data-item__label"><?= $lang['power_factor'] ?></span>
                                    <input id="Pf" type="text" class="overview-data-input" readonly>
                                </div>
                                <div class="overview-data-item">
                                    <span class="overview-data-item__label"><?= $lang['frequency'] ?></span>
                                    <input id="frequency" type="text" class="overview-data-input" readonly>
                                </div>
                            </div>

                        </div>
                    </aside>

                    <!-- Center: 3D model viewer -->
                    <div class="overview-viewer">
                        <model-viewer id="myModel" alt="3D model" auto-rotate camera-controls ar
                            style="width:100%; height:100%; background-color:transparent;">
                        </model-viewer>
                        <div class="overview-info-popup" id="infomation-meter" style="display:none;"></div>
                    </div>

                    <!-- Right panel: meter list (filled by scriptjs-map.html) -->
                    <aside class="ems-panel overview-panel overview-panel--right" id="listgroup"></aside>

                </div><!-- /.overview-body -->

            </div><!-- /.overview-page -->

<?php include '../components/app-shell-close.php'; ?>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-map.html"; ?>
<?php include '../components/doc-close.php'; ?>
