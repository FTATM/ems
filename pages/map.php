<?php
include '../components/session.php';
#checkLogin();
#checkSession();
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['overview'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/map.css">
</head>

<body>
    <div id="main" class="d-flex" style="height:100svh; overflow:hidden;">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column" style="height:100svh; overflow:hidden;">
            <?php include "../components/header.php"; ?>

            <main class="overview-main">

                <!-- Top bar -->
                <div class="overview-topbar">
                    <div class="overview-topbar__title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" />
                            <rect x="14" y="3" width="7" height="7" />
                            <rect x="14" y="14" width="7" height="7" />
                            <rect x="3" y="14" width="7" height="7" />
                        </svg>
                        Overview
                    </div>
                    <div class="overview-topbar__refresh">
                        <span class="overview-refresh-label">รีเฟรชทุก</span>
                        <input type="number" id="input-refresh" class="overview-refresh-input" value="15" min="1"
                            max="30" onchange="setRefreshTime()">
                        <span class="overview-refresh-label">วินาที</span>
                    </div>
                </div>

                <!-- Content -->
                <div class="overview-body">

                    <!-- Left panel: meter data -->
                    <aside class="overview-panel overview-panel--left">

                        <div class="overview-meter-name" id="name-meter">—</div>

                        <!-- Gauge -->
                        <div class="overview-gauge-wrap" id="gauge-kW"></div>

                        <!-- Voltage row -->
                        <div class="overview-section-label">Voltage</div>
                        <div class="overview-data-row">
                            <div class="overview-data-item">
                                <span class="overview-data-item__label">Phase A</span>
                                <input id="VoltageA" type="text" class="overview-data-input" readonly>
                            </div>
                            <div class="overview-data-item">
                                <span class="overview-data-item__label">Phase B</span>
                                <input id="VoltageB" type="text" class="overview-data-input" readonly>
                            </div>
                            <div class="overview-data-item">
                                <span class="overview-data-item__label">Phase C</span>
                                <input id="VoltageC" type="text" class="overview-data-input" readonly>
                            </div>
                        </div>

                        <!-- Current row -->
                        <div class="overview-section-label">Current</div>
                        <div class="overview-data-row">
                            <div class="overview-data-item">
                                <span class="overview-data-item__label">Phase A</span>
                                <input id="CurrentA" type="text" class="overview-data-input" readonly>
                            </div>
                            <div class="overview-data-item">
                                <span class="overview-data-item__label">Phase B</span>
                                <input id="CurrentB" type="text" class="overview-data-input" readonly>
                            </div>
                            <div class="overview-data-item">
                                <span class="overview-data-item__label">Phase C</span>
                                <input id="CurrentC" type="text" class="overview-data-input" readonly>
                            </div>
                        </div>

                        <!-- Pf & Frequency -->
                        <div class="overview-data-row overview-data-row--half">
                            <div class="overview-data-item">
                                <span class="overview-data-item__label">Power Factor</span>
                                <input id="Pf" type="text" class="overview-data-input" readonly>
                            </div>
                            <div class="overview-data-item">
                                <span class="overview-data-item__label">Frequency</span>
                                <input id="frequency" type="text" class="overview-data-input" readonly>
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

                    <!-- Right panel: group list -->
                    <aside class="overview-panel overview-panel--right" id="listgroup"></aside>

                </div><!-- /.overview-body -->

            </main>
            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-map.html"; ?>
</body>

</html>