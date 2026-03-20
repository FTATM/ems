<?php
include '../components/session.php';
// checkLogin();
// checkSession();
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['home'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>">
    <div id="main">
        <?php include "../components/sidemenu.php"; ?>

        <div class="dashboard-wrapper">
            <?php include "../components/header.php"; ?>

            <div class="dashboard-content">

                <!-- ── Filter Bar ── -->
                <div class="dash-filter-bar">

                    <!-- Location -->
                    <div class="dash-filter-group">
                        <span class="dash-filter-label">Location</span>
                        <div class="dash-location-badge">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span id="location"></span>
                        </div>
                    </div>

                    <div class="dash-divider"></div>

                    <!-- Group -->
                    <div class="dash-filter-group">
                        <span class="dash-filter-label">Group</span>
                        <div class="dash-group-badge">
                            <i class="bi bi-collection-fill"></i>
                            <span id="group"></span>
                        </div>
                    </div>

                    <div class="dash-divider"></div>

                    <!-- Meter -->
                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['meter'] ?></span>
                        <select id="select-meters" class="dash-select" onchange=" setMeterID()"></select>
                    </div>

                    <div class="dash-divider"></div>

                    <!-- Time range -->
                    <div class="dash-filter-group">
                        <span class="dash-filter-label">ช่วงเวลา</span>
                        <select id="select-filter-value" class="dash-select" onchange="setMeterID()">
                            <option value="5">5 นาทีล่าสุด</option>
                            <option value="10">10 นาทีล่าสุด</option>
                            <option value="15">15 นาทีล่าสุด</option>
                            <option value="30">30 นาทีล่าสุด</option>
                            <option selected value="60">1 ชั่วโมงล่าสุด</option>
                            <option value="120">2 ชั่วโมงล่าสุด</option>
                            <option value="480">4 ชั่วโมงล่าสุด</option>
                            <option value="1440">1 วันล่าสุด</option>
                            <option value="4320">3 วันล่าสุด</option>
                            <option value="10080">1 สัปดาห์ล่าสุด</option>
                            <option value="43200">1 เดือนล่าสุด</option>
                            <option value="259200">6 เดือนล่าสุด</option>
                            <option value="518400">1 ปีล่าสุด</option>
                        </select>
                    </div>

                    <!-- Refresh -->
                    <div class="dash-divider"></div>
                    <div class="dash-refresh-wrap">
                        <span class="dash-filter-label">รีเฟรชทุก</span>
                        <div class="dash-refresh-inner">
                            <input type="number" id="input-refresh" class="dash-refresh-input" value="15" min="1"
                                max="30" onchange="setRefreshTime()">
                            <span class="dash-filter-label">วินาที</span>
                        </div>
                    </div>
                </div>

                <!-- ── Main Body ── -->
                <div class="dash-body">

                    <!-- Left: Charts Grid -->
                    <div class="dash-body-left">
                        <!-- dash-charts-grid: มีแค่ 3 card -->
                        <div class="dash-charts-grid">

                            <!-- Meter Info แทน Pie (row 1, col 1) -->
                            <div class="dash-side-card dash-card--meter">
                                <div class="dash-side-header">
                                    <div class="dash-values-title">
                                        <i class="bi bi-info-circle-fill"></i> ข้อมูลมิเตอร์
                                    </div>
                                </div>
                                <div class="dash-side-scroll dash-side-scroll--info">
                                    <div id="infomation">
                                        <div class="dash-skeleton">
                                            <div class="dash-skeleton-line" style="width:60%"></div>
                                            <div class="dash-skeleton-line" style="width:90%"></div>
                                            <div class="dash-skeleton-line" style="width:75%"></div>
                                            <div class="dash-skeleton-line" style="width:85%"></div>
                                            <div class="dash-skeleton-line" style="width:50%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Line Chart -->
                            <div class="dash-card dash-card--line"> ... </div>

                            <!-- Pie Chart (row 2, col 1) -->
                            <div class="dash-card dash-card--pie">
                                <div class="dash-card-header">
                                    <div class="dash-card-title">
                                        <i class="bi bi-pie-chart-fill"></i> สัดส่วนการใช้พลังงาน
                                    </div>
                                </div>
                                <div class="dash-card-body">
                                    <div id="chart-Pie"></div>
                                </div>
                            </div>


                            <!-- Line Chart -->
                            <div class="dash-card dash-card--line">
                                <div class="dash-card-header">
                                    <div class="dash-card-title">
                                        <i class="bi bi-graph-up"></i> กราฟการใช้พลังงาน (kW)
                                    </div>
                                </div>
                                <div class="dash-card-body">
                                    <div id="chart-linear"></div>
                                </div>
                            </div>

                            <!-- Price Chart -->
                            <div class="dash-card dash-card--price">
                                <div class="dash-card-header">
                                    <div class="dash-card-title">
                                        <i class="bi bi-currency-exchange"></i> ค่าไฟฟ้า (บาท)
                                    </div>
                                </div>
                                <div class="dash-card-body">
                                    <div id="chart-linear-price"></div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Right: Info Panel -->
                    <div class="dash-right-panel">

                        <!-- Card 2: Type of Value -->
                        <div class="dash-side-card dash-side-card--types">
                            <div class="dash-side-header">
                                <div class="dash-values-title">
                                    <i class="bi bi-grid-fill"></i> <?= $lang['typeofvalue'] ?>
                                </div>
                            </div>
                            <div class="dash-side-scroll dash-side-scroll--types">
                                <div id="list-data">
                                    <!-- skeleton -->
                                    <div class="dash-skeleton" style="width:100%">
                                        <div class="dash-skeleton-line" style="width:80%"></div>
                                        <div class="dash-skeleton-line" style="width:65%"></div>
                                        <div class="dash-skeleton-line" style="width:90%"></div>
                                        <div class="dash-skeleton-line" style="width:70%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end dash-body -->

            </div>
            <!-- end dashboard-content -->

            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-dashboard.html"; ?>
</body>

</html>