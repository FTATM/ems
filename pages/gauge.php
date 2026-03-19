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
    <link rel="stylesheet" href="../styles/gauge.css">
    <style>
    html:not(.dark):not([data-theme="dark"]) #input-refresh {
        color: #111827 !important;
    }
    </style>
</head>

<body>
    <div id="main" class="d-flex" style="height:100svh; overflow:hidden;">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column" style="height:100svh; overflow:hidden;">
            <?php include "../components/header.php"; ?>

            <main class="gauge-main">

                <!-- Controls Card -->
                <div class="gauge-controls">

                    <!-- Select Meter -->
                    <div class="gauge-controls__group gauge-controls__group--meter">
                        <label class="gauge-controls__label" for="select-meters">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="2" />
                                <path
                                    d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                            </svg>
                            Select Meter
                        </label>
                        <select id="select-meters" class="gauge-select" onchange="checkTimeChange()"></select>
                    </div>

                    <!-- Data Last Time -->
                    <div class="gauge-controls__group gauge-controls__group--time">
                        <label class="gauge-controls__label" for="select-time">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            Data Last Time
                        </label>
                        <select id="select-time" class="gauge-select" onchange="checkTimeChange()">
                            <option selected value="1">Now</option>
                            <option value="5">5 mins</option>
                            <option value="10">10 mins</option>
                            <option value="15">15 mins</option>
                            <option value="30">30 mins</option>
                            <option value="60">1 hour</option>
                            <option value="120">2 hours</option>
                            <option value="240">4 hours</option>
                            <option value="1440">Last 1 day</option>
                            <option value="4320">Last 3 days</option>
                            <option value="10080">Last 1 week</option>
                            <option value="43200">Last 1 month</option>
                            <option value="259200">Last 6 months</option>
                            <option value="518400">Last 1 year</option>
                            <option value="0">All time</option>
                        </select>
                    </div>

                    <div class="dash-divider"></div>

                    <!-- Refresh -->
                    <div class="dash-refresh-wrap">
                        <span class="dash-refresh-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="23 4 23 10 17 10" />
                                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                            </svg>
                        </span>
                        <span class="dash-filter-label">Refresh every:</span>
                        <div class="dash-refresh-inner">
                            <input type="number" id="input-refresh" class="dash-refresh-input" value="15" min="1"
                                max="60" onchange="setRefreshTime()">
                        </div>
                        <span class="dash-filter-label--unit">Seconds</span>
                    </div>

                </div><!-- /.gauge-controls -->

                <!-- Content area -->
                <div class="gauge-body">
                    <div id="list-gauge" class="gauge-grid"></div>

                    <!-- Sidebar -->
                    <div id="list-data" class="gauge-sidebar">
                        <div class="gauge-sidebar__header">
                            <span class="gauge-sidebar__dot"></span>
                            <span class="gauge-sidebar__title"><?= $lang['alltype'] ?></span>
                        </div>
                        <div class="gauge-sidebar__body" id="sidebar-body"></div>
                    </div>
                </div>

            </main>
            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-gauge.html"; ?>
</body>

</html>