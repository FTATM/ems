<?php
include '../components/session.php';
checkLogin();
checkSession();

$EMS_PAGE_TITLE = $lang['report'] . ' - EMS';
// scroll page — no $EMS_SHELL_LOCKED
include '../components/doc-open.php';
?>
    <link rel="stylesheet" href="../styles/report-meter.css">

<?php include '../components/app-shell-open.php'; ?>

            <div class="page-content report-page">

                <!-- ── Page hero ── -->
                <div class="report-hero">
                    <h2>
                        <span class="hero-icon"><i class="bi bi-receipt"></i></span>
                        <?= $lang['report'] ?>
                    </h2>
                </div>

                <!-- ── Filter toolbar ── -->
                <div class="ems-toolbar report-toolbar">
                    <div class="ems-toolbar__group">
                        <span class="ems-toolbar__label"><?= $lang['location'] ?></span>
                        <span class="report-toolbar__value">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span id="location"></span>
                        </span>
                    </div>
                    <div class="ems-toolbar__divider"></div>
                    <div class="ems-toolbar__group">
                        <span class="ems-toolbar__label"><?= $lang['group'] ?></span>
                        <span class="report-toolbar__value">
                            <i class="bi bi-collection-fill"></i>
                            <span id="group"></span>
                        </span>
                    </div>
                    <div class="ems-toolbar__divider"></div>
                    <div class="ems-toolbar__group">
                        <span class="ems-toolbar__label"><?= $lang['meter'] ?></span>
                        <select id="select-meters" class="ems-select" onchange="changeSelectMeter()"></select>
                    </div>
                    <div class="ems-toolbar__group">
                        <span class="ems-toolbar__label"><?= $lang['select_date'] ?></span>
                        <div class="report-date"
                            onclick="document.getElementById('select-date-filter').showPicker()">
                            <i class="bi bi-calendar3"></i>
                            <span id="date-display">เลือกวันที่</span>
                            <input id="select-date-filter" type="date" class="filter-date-hidden"
                                onchange="updateDateDisplay(); changeSelectMeter()">
                            <input hidden id="value-date-filter" />
                        </div>
                    </div>
                </div>

                <!-- ── Tabs: Daily / Monthly ── -->
                <div class="report-tabs" role="tablist">
                    <button type="button" class="report-tab is-active" data-tab="daily"
                        onclick="switchReportTab('daily')">
                        <i class="bi bi-calendar-day"></i> <?= $lang['dailyreport'] ?>
                    </button>
                    <button type="button" class="report-tab" data-tab="monthly"
                        onclick="switchReportTab('monthly')">
                        <i class="bi bi-calendar-month"></i> <?= $lang['monthlyreport'] ?>
                    </button>
                </div>

                <!-- ══════════════════════════════
                     รายงานประจำวัน (Daily Report)
                     ══════════════════════════════ -->
                <section class="report-panel" id="tab-daily">
                    <div class="ems-card ems-card--accent report-card">

                        <!-- 1. ค่าความต้องการ -->
                        <div class="section-title"><span class="section-no">1</span><?= $lang['demand'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="th-left"></th>
                                    <th></th>
                                    <th>kW</th>
                                    <th>บาท/kW</th>
                                    <th>บาท</th>
                                    <th>บาท/kWh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><input id="kw-avg" type="text" readonly></td>
                                    <td><input id="input-kw" type="text" class="editable"></td>
                                    <td><input id="result-bath-kw" type="text" value="0.00" readonly></td>
                                    <td><input id="bath-per-kwhr" type="text" value="0.00" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                        <input id="kw-max" type="text" class="d-none" readonly>

                        <hr class="section-divider">

                        <!-- 2. ค่าความต้องการไฟฟ้า -->
                        <div class="section-title"><span class="section-no">2</span><?= $lang['power_demand'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="th-left"></th>
                                    <th>ชั่วโมง</th>
                                    <th>kWh</th>
                                    <th>บาท/kWh</th>
                                    <th>บาท</th>
                                    <th>LoadFactor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td><input id="hour-diff" type="text" readonly></td>
                                    <td><input id="kwhr-diff" type="text" readonly></td>
                                    <td><input id="input-kwhr" type="text" class="editable"></td>
                                    <td><input id="result-bath-kwhr" type="text" value="0.00" readonly></td>
                                    <td><input id="load-factor" type="text" value="0.00" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                        <input id="hour-value" type="text" class="d-none">
                        <input id="kwhr-fv" type="text" class="d-none" readonly>
                        <input id="kwhr-lv" type="text" class="d-none" readonly>

                        <hr class="section-divider">

                        <!-- 3. ค่าเพาเวอร์แฟคเตอร์ -->
                        <div class="section-title"><span class="section-no">3</span><?= $lang['powerfactor'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>KVAR</th>
                                    <th>pf &lt; 0.85</th>
                                    <th>บาท/kVar</th>
                                    <th>บาท</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td><input id="kvar-avg" type="text" readonly></td>
                                    <td><input id="pf-avg" type="text" value="0.85"></td>
                                    <td><input id="input-kvar" type="text" class="editable"></td>
                                    <td><input id="result-bath-kvar" type="text" value="0.00" readonly></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><input id="pf-avg2" type="text" class="d-none" readonly></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="section-divider">

                        <!-- 4. ค่าบริการ -->
                        <div class="section-title"><span class="section-no">4</span><?= $lang['servicecharge'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>บาท</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="input-service" type="text" class="editable"></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="section-divider">

                        <!-- 5. ค่า FT -->
                        <div class="section-title"><span class="section-no">5</span><?= $lang['fuel_adjustment_charge'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>บาท/kWh</th>
                                    <th>บาท</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="input-ft" type="text" class="editable"></td>
                                    <td><input id="result-bath-ft" type="text" value="0.00" readonly></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="section-divider">

                        <!-- 6. ค่าไฟฟ้ารวม -->
                        <div class="section-title"><span class="section-no">6</span><?= $lang['total_electricity_charge'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>บาท</th>
                                    <th>บาท/kWh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="result-bath-all" type="text" value="0.00" readonly></td>
                                    <td><input id="result-bath-all-kwhr" type="text" value="0.00" readonly></td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="section-divider">

                        <!-- 7. ค่าภาษี -->
                        <div class="section-title"><span class="section-no">7</span><?= $lang['tax'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>% ภาษี</th>
                                    <th>บาท</th>
                                    <th>บาท/kWh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="input-tax" type="text" class="editable"></td>
                                    <td><input id="result-bath-tax" type="text" value="0.00" readonly></td>
                                    <td><input id="result-bath-tax-kwhr" type="text" value="0.00" readonly></td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="section-divider">

                        <!-- 8. ค่ารวมทั้งสิ้น -->
                        <div class="section-title"><span class="section-no">8</span><?= $lang['total_amount'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>บาท</th>
                                    <th>บาท/kWh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="result-bath-total" type="text" value="0.00" readonly
                                            class="total-input"></td>
                                    <td><input id="result-bath-total-kwhr" type="text" value="0.00" readonly
                                            class="total-input"></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="report-card-actions">
                            <button id="btn-export" class="ems-btn ems-btn--primary" onclick="createPDF(false)">
                                <i class="bi bi-printer"></i> <?= $lang['exportpdf'] ?>
                            </button>
                        </div>
                    </div>
                </section><!-- /#tab-daily -->

                <!-- ════════════════════════════════
                     รายงานประจำเดือน (Monthly Report)
                     ════════════════════════════════ -->
                <section class="report-panel" id="tab-monthly" hidden>
                    <div class="ems-card ems-card--accent report-card">

                        <!-- 1. ค่าความต้องการ -->
                        <div class="section-title"><span class="section-no">1</span><?= $lang['demand'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>kW</th>
                                    <th>บาท</th>
                                    <th>บาท/kWh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><input id="kw-avg-m" type="text" readonly></td>
                                    <td><input id="result-bath-kw-m" type="text" value="0.00" readonly></td>
                                    <td><input id="result-bath-per-kwhr-m" type="text" value="0.00" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                        <input id="kw-max-m" type="text" class="d-none" readonly>

                        <hr class="section-divider">

                        <!-- 2. ค่าความต้องการไฟฟ้า -->
                        <div class="section-title"><span class="section-no">2</span><?= $lang['power_demand'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ชั่วโมง</th>
                                    <th>kWhr</th>
                                    <th>บาท</th>
                                    <th>LoadFactor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td><input id="hour-diff-m" type="text" readonly></td>
                                    <td><input id="kwhr-diff-m" type="text" readonly></td>
                                    <td><input id="result-bath-kwhr-m" type="text" value="0.00"></td>
                                    <td><input id="load-factor-m" type="text" value="0.00" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                        <input id="hour-value-m" type="text" class="d-none">
                        <input id="kwhr-fv-m" type="text" class="d-none" readonly>
                        <input id="kwhr-lv-m" type="text" class="d-none" readonly>

                        <hr class="section-divider">

                        <!-- 3. ค่าความต้องการไฟฟ้า (KVAR) -->
                        <div class="section-title"><span class="section-no">3</span><?= $lang['power_demand'] ?> (KVAR)</div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>KVAR</th>
                                    <th>pf &lt; 0.85</th>
                                    <th>บาท</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td><input id="kvar-avg-m" type="text" readonly></td>
                                    <td><input id="pf-avg-m" type="text" value="0.85"></td>
                                    <td><input id="result-bath-kvar-m" type="text" value="0.00"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><input id="pf-avg2-m" type="text" class="d-none" readonly></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="section-divider">

                        <!-- 4. ค่าบริการ -->
                        <div class="section-title"><span class="section-no">4</span><?= $lang['servicecharge'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>บาท</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="input-service-m" type="text" class="editable"></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="section-divider">

                        <!-- 5. ค่า FT -->
                        <div class="section-title"><span class="section-no">5</span><?= $lang['fuel_adjustment_charge'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>บาท</th>
                                    <th>บาท/kWhr</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="result-bath-ft-m" type="text" value="0.00" readonly></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="section-divider">

                        <!-- 6. ค่าไฟฟ้ารวม -->
                        <div class="section-title"><span class="section-no">6</span><?= $lang['total_electricity_charge'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>บาท</th>
                                    <th>บาท/kWh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="result-bath-all-m" type="text" value="0.00" readonly></td>
                                    <td><input id="result-bath-all-kwhr-m" type="text" value="0.00" readonly>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="section-divider">

                        <!-- 7. ค่าภาษี -->
                        <div class="section-title"><span class="section-no">7</span><?= $lang['tax'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>บาท</th>
                                    <th>บาท/kWh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="result-bath-tax-m" type="text" value="0.00" readonly></td>
                                    <td><input id="result-bath-tax-kwhr-m" type="text" value="0.00" readonly>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="section-divider">

                        <!-- 8. ค่ารวมทั้งสิ้น -->
                        <div class="section-title"><span class="section-no">8</span><?= $lang['total_amount'] ?></div>
                        <table class="report-table">
                            <colgroup>
                                <col class="col-label">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>บาท</th>
                                    <th>บาท/kWh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="result-bath-total-m" type="text" value="0.00" readonly
                                            class="total-input"></td>
                                    <td><input id="result-bath-total-kwhr-m" type="text" value="0.00" readonly
                                            class="total-input"></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="report-card-actions">
                            <button id="btn-export-monthly" class="ems-btn ems-btn--primary" onclick="createPDF(true)">
                                <i class="bi bi-printer"></i> <?= $lang['exportpdf'] ?>
                            </button>
                        </div>
                    </div>
                </section><!-- /#tab-monthly -->

                <div class="footer-note">
                    <?= $lang['notee'] ?>
                </div>

            </div><!-- /.page-content -->

<?php include '../components/app-shell-close.php'; ?>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include '../scripts/scriptjs-report-meter.html'; ?>
<?php include '../components/doc-close.php'; ?>
