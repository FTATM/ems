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
    <title><?= $lang['report'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
    <link rel="stylesheet" href="../styles/report-electric.css">
</head>

<body>
    <div id="main">
        <?php include "../components/sidemenu.php"; ?>

        <div class="dashboard-wrapper">
            <?php include "../components/header.php"; ?>

            <div class="dashboard-content">

                <!-- ── Filter Bar ── -->
                <div class="dash-filter-bar">

                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['meter'] ?></span>
                        <select id="select-meter-show" class="dash-select" onchange="loadingChart()">
                            <option>No value</option>
                        </select>
                    </div>

                    <div class="dash-divider"></div>

                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['from'] ?></span>
                        <div class="filter-date-wrapper"
                            onclick="document.getElementById('datetime-from').showPicker()">
                            <i class="bi bi-calendar3"></i>
                            <span id="date-from-display" class="filter-date-display">30 กันยายน 2568</span>
                            <input id="datetime-from" type="date" class="filter-date-hidden" value="2026-03-17"
                                onchange="updateFromDisplay(); filterMeters()">
                        </div>
                    </div>

                    <div class="dash-divider"></div>

                    <div class="dash-filter-group">
                        <span class="dash-filter-label"><?= $lang['to'] ?></span>
                        <div class="filter-date-wrapper" onclick="document.getElementById('datetime-to').showPicker()">
                            <i class="bi bi-calendar3"></i>
                            <span id="date-to-display" class="filter-date-display">30 ตุลาคม 2568</span>
                            <input id="datetime-to" type="date" class="filter-date-hidden" value="2026-03-18"
                                onchange="updateToDisplay(); filterMeters()">
                        </div>
                    </div>

                    <div class="dash-divider"></div>

                    <!-- Email + Export -->
                    <div class="dash-filter-group dash-filter-group--export">
                        <span class="dash-filter-label"><?= $lang['export'] ?></span>
                        <div class="report-export-row">
                            <input class="dash-text-input" id="email" type="email"
                                placeholder="<?= $lang['emailaddress'] ?>">
                            <button class="report-btn report-btn--csv" onclick="sendExportToEmail('csv')">
                                <i class="bi bi-filetype-csv"></i> CSV
                            </button>
                            <button id="excel" class="report-btn report-btn--xl" onclick="sendExportToEmail('excel')">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                        </div>
                    </div>

                </div>

                <!-- ── Body ── -->
                <div class="report-body">

                    <!-- Chart Card -->
                    <div class="dash-card report-chart-card">
                        <div class="dash-card-header">
                            <div class="dash-card-title">
                                <i class="bi bi-graph-up-arrow"></i>
                                <?= $lang['preview'] ?>
                            </div>
                        </div>
                        <div class="dash-card-body">
                            <div id="linear-chart" style="width:100%;height:100%;"></div>
                        </div>
                    </div>

                    <!-- Table Card -->
                    <div class="dash-card report-table-card">
                        <div class="dash-card-header report-table-header">
                            <div class="dash-card-title">
                                <i class="bi bi-table"></i><?= $lang['table'] ?>
                            </div>
                            <div class="report-table-controls">
                                <div class="report-check-wrap">
                                    <input id="is-table-all-value" type="checkbox" class="report-checkbox"
                                        onchange="ReloadTable()">
                                    <label for="is-table-all-value"
                                        class="report-check-label"><?= $lang['showall'] ?></label>
                                </div>
                                <div class="dash-filter-group"
                                    style="flex-direction:row;align-items:center;gap:8px;padding:0;flex:unset;">
                                    <span class="dash-filter-label"><?= $lang['rows'] ?></span>
                                    <select id="select-table-show" class="dash-select" style="width:80px;"
                                        onchange="ReloadTable()">
                                        <option selected value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="report-table-scroll">
                            <table class="report-table" id="table-data">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <!-- end report-body -->

            </div>
            <!-- end dashboard-content -->

            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-report.html"; ?>
    <script>
    function updateFromDisplay() {
        const input = document.getElementById('datetime-from');
        const display = document.getElementById('date-from-display');
        if (input.value) {
            const d = new Date(input.value);
            display.textContent = d.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            display.style.color = '#222';
        }
    }

    function updateToDisplay() {
        const input = document.getElementById('datetime-to');
        const display = document.getElementById('date-to-display');
        if (input.value) {
            const d = new Date(input.value);
            display.textContent = d.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            display.style.color = '#222';
        }
    }
    window.addEventListener('DOMContentLoaded', function() {
        updateFromDisplay();
        updateToDisplay();
    });
    </script>

</body>

</html>