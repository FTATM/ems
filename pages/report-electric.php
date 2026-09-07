<?php
include '../components/session.php';
checkLogin();
checkSession();

$EMS_PAGE_TITLE = $lang['report'] . ' - EMS';
$EMS_SHELL_LOCKED = true;            // monitor page — lock the viewport

date_default_timezone_set('Asia/Bangkok');
$reportDateFrom = date('Y-m-01');    // first day of the current month
$reportDateTo = date('Y-m-d');       // today

include '../components/doc-open.php';
?>
    <link rel="stylesheet" href="../styles/report-electric.css">

<?php include '../components/app-shell-open.php'; ?>

            <div class="report-electric">

                <!-- ── Filter toolbar ── -->
                <div class="ems-toolbar report-toolbar">
                    <div class="ems-toolbar__group">
                        <span class="ems-toolbar__label"><?= $lang['meter'] ?></span>
                        <select id="select-meters" class="ems-select" onchange="changeSelectMeter()">
                            <option>No value</option>
                        </select>
                    </div>

                    <div class="ems-toolbar__divider"></div>

                    <div class="ems-toolbar__group">
                        <span class="ems-toolbar__label"><?= $lang['from'] ?></span>
                        <div class="report-date" onclick="document.getElementById('datetime-from').showPicker()">
                            <i class="bi bi-calendar3"></i>
                            <span id="date-from-display" class="filter-date-display"></span>
                            <input id="datetime-from" type="date" class="filter-date-hidden" value="<?= $reportDateFrom ?>"
                                onchange="updateFromDisplay(); changeSelectMeter()">
                        </div>
                    </div>

                    <div class="ems-toolbar__divider"></div>

                    <div class="ems-toolbar__group">
                        <span class="ems-toolbar__label"><?= $lang['to'] ?></span>
                        <div class="report-date" onclick="document.getElementById('datetime-to').showPicker()">
                            <i class="bi bi-calendar3"></i>
                            <span id="date-to-display" class="filter-date-display"></span>
                            <input id="datetime-to" type="date" class="filter-date-hidden" value="<?= $reportDateTo ?>"
                                onchange="updateToDisplay(); changeSelectMeter()">
                        </div>
                    </div>

                </div>

                <!-- ── Body ── -->
                <div class="report-body">

                    <!-- Chart + Export row (80:20) -->
                    <div class="report-chart-row">

                        <!-- Chart Card -->
                        <div class="ems-card report-chart-card">
                            <div class="report-card-head">
                                <span class="report-card-title">
                                    <i class="bi bi-graph-up-arrow"></i>
                                    <?= $lang['preview'] ?>
                                </span>
                            </div>
                            <div class="report-card-body">
                                <div id="linear-chart" style="width:100%;height:100%;"></div>
                            </div>
                        </div>

                        <!-- Export Card -->
                        <div class="ems-card report-export-card">
                            <div class="report-card-head">
                                <span class="report-card-title">
                                    <i class="bi bi-download"></i>
                                    <?= $lang['export'] ?>
                                </span>
                            </div>
                            <div class="report-card-body report-export-body">
                                <input class="report-email" id="email" type="email"
                                    placeholder="<?= $lang['emailaddress'] ?>">
                                <button class="ems-btn ems-btn--ghost ems-btn--sm" onclick="sendExportToEmail('csv')">
                                    <i class="bi bi-filetype-csv"></i> CSV
                                </button>
                                <button id="excel" class="ems-btn ems-btn--primary ems-btn--sm"
                                    onclick="sendExportToEmail('excel')">
                                    <i class="bi bi-file-earmark-excel"></i> Excel
                                </button>
                            </div>
                        </div>

                    </div>
                    <!-- end report-chart-row -->

                    <!-- Table Card -->
                    <div class="ems-card report-table-card">
                        <div class="report-card-head report-table-head">
                            <span class="report-card-title">
                                <i class="bi bi-table"></i> <?= $lang['table'] ?>
                            </span>
                            <div class="report-table-controls">
                                <label class="report-check-wrap">
                                    <input id="is-table-all-value" type="checkbox" class="report-checkbox"
                                        onchange="ReloadTable()">
                                    <span class="report-check-label"><?= $lang['showall'] ?></span>
                                </label>
                                <div class="report-rows-ctl">
                                    <span class="ems-toolbar__label"><?= $lang['rows'] ?></span>
                                    <select id="select-table-show" class="ems-select ems-select--sm"
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
                            <table class="report-grid" id="table-data">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <!-- end report-body -->

            </div><!-- /.report-electric -->

<?php include '../components/app-shell-close.php'; ?>
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
<?php include '../components/doc-close.php'; ?>
