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
                        <span class="hero-icon"><i class="bi bi-file-earmark-text"></i></span>
                        <?= $lang['reportdetail'] ?? $lang['report'] ?>
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
                        <select id="select-meters" class="ems-select" onchange="filterDataInMeters()"></select>
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
                                    <th class="th-left"><?= $lang['timerange'] ?></th>
                                    <th></th>
                                    <th>kW</th>
                                    <th>บาท/kW</th>
                                    <th>บาท</th>
                                    <th>บาท/kWh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="label-cell">On peak</td>
                                    <td></td>
                                    <td><input id="kw-avg-n" type="text" class="form-control form-control-sm" readonly></td>
                                    <td><input id="input-kw-n" type="text" class="editable"></td>
                                    <td><input id="result-bath-kw-n" type="text" value="0.00" readonly></td>
                                    <td><input id="bath-per-kwhr-n" type="text" value="0.00" readonly></td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Off peak</td>
                                    <td></td>
                                    <td><input id="kw-avg-f" type="text" class="form-control form-control-sm" readonly></td>
                                    <td><input id="input-kw-f" type="text" class="editable"></td>
                                    <td><input id="result-bath-kw-f" type="text" value="0.00" readonly></td>
                                    <td><input id="bath-per-kwhr-f" type="text" value="0.00" readonly></td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Holiday</td>
                                    <td></td>
                                    <td><input id="kw-avg-h" type="text" class="form-control form-control-sm" readonly></td>
                                    <td><input id="input-kw-h" type="text" class="editable"></td>
                                    <td><input id="result-bath-kw-h" type="text" value="0.00" readonly></td>
                                    <td><input id="bath-per-kwhr-h" type="text" value="0.00" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                        <input id="kw-max-n" type="text" class="d-none" readonly>
                        <input id="kw-max-f" type="text" class="d-none" readonly>
                        <input id="kw-max-h" type="text" class="d-none" readonly>

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
                                    <th class="th-left"><?= $lang['timerange'] ?></th>
                                    <th>ชั่วโมง</th>
                                    <th>kWh</th>
                                    <th>บาท/kWh</th>
                                    <th>บาท</th>
                                    <th>LoadFactor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="label-cell">On peak</td>
                                    <td><input id="hour-diff-n" type="text" readonly></td>
                                    <td><input id="kwhr-diff-n" type="text" readonly></td>
                                    <td><input id="input-kwhr-n" type="text" class="editable"></td>
                                    <td><input id="result-bath-kwhr-n" type="text" value="0.00" readonly></td>
                                    <td><input id="load-factor-n" type="text" value="0.00" readonly></td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Off peak</td>
                                    <td><input id="hour-diff-f" type="text" readonly></td>
                                    <td><input id="kwhr-diff-f" type="text" readonly></td>
                                    <td><input id="input-kwhr-f" type="text" class="editable"></td>
                                    <td><input id="result-bath-kwhr-f" type="text" value="0.00" readonly></td>
                                    <td><input id="load-factor-f" type="text" value="0.00" readonly></td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Holiday</td>
                                    <td><input id="hour-diff-h" type="text" readonly></td>
                                    <td><input id="kwhr-diff-h" type="text" readonly></td>
                                    <td><input id="input-kwhr-h" type="text" class="editable"></td>
                                    <td><input id="result-bath-kwhr-h" type="text" value="0.00" readonly></td>
                                    <td><input id="load-factor-h" type="text" value="0.00" readonly></td>
                                </tr>
                                <tr class="maxpeak-row">
                                    <td></td>
                                    <td class="sub-label">ชั่วโมงเริ่มต้น</td>
                                    <td class="sub-label">ชั่วโมงสิ้นสุด</td>
                                    <td class="sub-label">kW(max)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Max peak</td>
                                    <td><input id="kwhr-diff-ltime" type="text" readonly></td>
                                    <td><input id="kwhr-diff-ttime" type="text" readonly></td>
                                    <td><input id="kwhr-diff-kwmax" type="text" readonly></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <input id="hour-value-n" type="text" class="d-none">
                        <input id="hour-value-f" type="text" class="d-none">
                        <input id="hour-value-h" type="text" class="d-none">
                        <input id="kwhr-diff-all" type="text" class="d-none" readonly>
                        <input id="kwhr-fv-all" type="text" class="d-none" readonly>
                        <input id="kwhr-lv-all" type="text" class="d-none" readonly>
                        <input id="result-bath-kwhr-all" type="text" value="0.00" class="d-none" readonly>

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
                                    <th class="th-left"></th>
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
                                    <td><input id="kvar-diff-all" type="text" readonly></td>
                                    <td><input id="pf-avg-all" type="text" readonly></td>
                                    <td><input id="input-kvar" type="text" class="editable"></td>
                                    <td><input id="result-bath-kvar" type="text" value="0.00" readonly></td>
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
                                    <th class="th-left"><?= $lang['timerange'] ?></th>
                                    <th></th>
                                    <th></th>
                                    <th>kW</th>
                                    <th>บาท</th>
                                    <th>บาท/kWh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="label-cell">On peak</td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="kw-avg-month-n" type="text" readonly></td>
                                    <td><input id="result-bath-kw-month-n" type="text" value="0.00" readonly></td>
                                    <td><input id="result-bath-per-kwhr-month-n" type="text" value="0.00" readonly></td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Off peak</td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="kw-avg-month-f" type="text" readonly></td>
                                    <td><input id="result-bath-kw-month-f" type="text" value="0.00" readonly></td>
                                    <td><input id="result-bath-per-kwhr-month-f" type="text" value="0.00" readonly></td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Holiday</td>
                                    <td></td>
                                    <td></td>
                                    <td><input id="kw-avg-month-h" type="text" readonly></td>
                                    <td><input id="result-bath-kw-month-h" type="text" value="0.00" readonly></td>
                                    <td><input id="result-bath-per-kwhr-month-h" type="text" value="0.00" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                        <input id="kw-max-month-n" type="text" class="d-none" readonly>
                        <input id="kw-max-month-f" type="text" class="d-none" readonly>
                        <input id="kw-max-month-h" type="text" class="d-none" readonly>

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
                                    <th class="th-left"><?= $lang['timerange'] ?></th>
                                    <th></th>
                                    <th>ชั่วโมง</th>
                                    <th>kWhr</th>
                                    <th>บาท</th>
                                    <th>LoadFactor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="label-cell">On peak</td>
                                    <td></td>
                                    <td><input id="hour-diff-month-n" type="text" readonly></td>
                                    <td><input id="kwhr-diff-month-n" type="text" readonly></td>
                                    <td><input id="result-bath-kwhr-month-n" type="text" value="0.00" readonly></td>
                                    <td><input id="load-factor-month-n" type="text" value="0.00" readonly></td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Off peak</td>
                                    <td></td>
                                    <td><input id="hour-diff-month-f" type="text" readonly></td>
                                    <td><input id="kwhr-diff-month-f" type="text" readonly></td>
                                    <td><input id="result-bath-kwhr-month-f" type="text" value="0.00" readonly></td>
                                    <td><input id="load-factor-month-f" type="text" value="0.00" readonly></td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Holiday</td>
                                    <td></td>
                                    <td><input id="hour-diff-month-h" type="text" readonly></td>
                                    <td><input id="kwhr-diff-month-h" type="text" readonly></td>
                                    <td><input id="result-bath-kwhr-month-h" type="text" value="0.00" readonly></td>
                                    <td><input id="load-factor-month-h" type="text" value="0.00" readonly></td>
                                </tr>
                                <tr class="maxpeak-row">
                                    <td class="label-cell">Max peak</td>
                                    <td class="sub-label">ชั่วโมงเริ่มต้น</td>
                                    <td class="sub-label">ชั่วโมงสิ้นสุด</td>
                                    <td class="sub-label">kW(max)</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><input id="kwhr-diff-month-ltime" type="text" readonly></td>
                                    <td><input id="kwhr-diff-month-ttime" type="text" readonly></td>
                                    <td><input id="kwhr-diff-month-kwmax" type="text" readonly></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <input id="hour-value-month-n" type="text" class="d-none">
                        <input id="hour-value-month-f" type="text" class="d-none">
                        <input id="hour-value-month-h" type="text" class="d-none">
                        <input id="kwhr-diff-month-all" type="text" class="d-none" readonly>
                        <input id="kwhr-fv-month-all" type="text" class="d-none" readonly>
                        <input id="kwhr-lv-month-all" type="text" class="d-none" readonly>
                        <input id="result-bath-kwhr-month-all" type="text" value="0.00" class="d-none" readonly>

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
                                <col class="col-data">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th></th>
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
                                    <td></td>
                                    <td><input id="kvar-diff-month-all" type="text" readonly></td>
                                    <td><input id="pf-avg-month-all" type="text" readonly></td>
                                    <td><input id="result-bath-kvar-month" type="text" value="0.00"></td>
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
                                    <td><input id="input-service-month" type="text" class="editable"></td>
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
                                    <td><input id="result-bath-ft-month" type="text" value="0.00" readonly></td>
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
                                    <td><input id="result-bath-all-month" type="text" value="0.00" readonly></td>
                                    <td><input id="result-bath-all-kwhr-month" type="text" value="0.00" readonly></td>
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
                                    <td><input id="result-bath-tax-month" type="text" value="0.00" readonly></td>
                                    <td><input id="result-bath-tax-kwhr-month" type="text" value="0.00" readonly></td>
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
                                    <td><input id="result-bath-total-month" type="text" value="0.00" readonly
                                            class="total-input"></td>
                                    <td><input id="result-bath-total-kwhr-month" type="text" value="0.00" readonly
                                            class="total-input"></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="report-card-actions">
                            <button id="btn-export-month" class="ems-btn ems-btn--primary" onclick="createPDF(true)">
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
    <?php include '../scripts/scriptjs-report-meter-detail.html'; ?>

    <script>
        function updateDateDisplay() {
            const input = document.getElementById('select-date-filter');
            const display = document.getElementById('date-display');
            if (input.value) {
                const d = new Date(input.value);
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                display.textContent = d.toLocaleDateString('th-TH', options);
                display.style.color = '#222';
            } else {
                display.textContent = 'เลือกวันที่';
                display.style.color = '#aaa';
            }
        }
        window.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('select-date-filter');
            if (!input.value) {
                const today = new Date().toISOString().split('T')[0];
                input.value = today;
                updateDateDisplay();
            }
        });
    </script>
<?php include '../components/doc-close.php'; ?>
