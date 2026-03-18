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
    <link rel="stylesheet" href="../styles/report-meter.css">
    <link rel="stylesheet" href="../styles/report-meter-detail.css">
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; height: 100svh; overflow: hidden;">
    <div id="main" class="d-flex" style="height: 100svh; overflow: hidden;">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column" style="height: 100%; overflow: hidden;">
            <?php include "../components/header.php"; ?>

            <!-- Filter Bar -->
            <div class="container-fluid px-3 pt-3">
                <div class="filter-bar">
                    <div class="filter-item">
                        <span class="filter-label">Location</span>
                        <div class="filter-value">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span id="location"></span>
                        </div>
                    </div>
                    <div class="filter-item">
                        <span class="filter-label">Group</span>
                        <div class="filter-value">
                            <i class="bi bi-collection-fill"></i>
                            <span id="group"></span>
                        </div>
                    </div>
                    <div class="filter-item">
                        <span class="filter-label"><?= $lang['meter'] ?></span>
                        <select id="select-meters" class="filter-select" onchange="filterDataInMeters()"></select>
                    </div>
                    <div class="filter-item">
                        <span class="filter-label"><?= $lang['time'] ?></span>
                        <div class="filter-date-wrapper"
                            onclick="document.getElementById('select-filter-value').showPicker()">
                            <i class="bi bi-calendar3"></i>
                            <span id="date-display">เลือกวันที่</span>
                            <input id="select-filter-value" type="date" class="filter-date-hidden"
                                onchange="updateDateDisplay(); filterDataInMeters()">
                        </div>
                    </div>
                </div>
            </div>

            <main class="flex-grow-1" style="overflow-y: auto;">
                <div class="container-fluid px-3">
                    <div class="reports-wrapper">

                        <!-- ══════════════════════════════
                             รายงานประจำวัน (Daily Report)
                             ══════════════════════════════ -->
                        <div class="report-card">
                            <div class="report-card-header">
                                <div class="report-card-icon"><i class="bi bi-calendar-day"></i></div>
                                <h5 class="report-card-title">รายงานประจำวัน (Daily Report)</h5>
                            </div>

                            <!-- 1. ค่าความต้องการ -->
                            <div class="section-title">1. ค่าความต้องการ</div>
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
                                        <th class="th-left">ช่วงเวลา</th>
                                        <th>kW</th>
                                        <th>บาท/kW</th>
                                        <th>บาท</th>
                                        <th>บาท/kWh</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="label-cell">On peak</td>
                                        <td><input id="kw-avg-n" type="text" class="form-control form-control-sm"
                                                readonly></td>
                                        <td><input id="input-kw-n" type="text" class="editable"></td>
                                        <td><input id="result-bath-kw-n" type="text" value="0.00" readonly></td>
                                        <td><input id="bath-per-kwhr-n" type="text" value="0.00" readonly></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="label-cell">Off peak</td>
                                        <td><input id="kw-avg-f" type="text" class="form-control form-control-sm"
                                                readonly></td>
                                        <td><input id="input-kw-f" type="text" class="editable"></td>
                                        <td><input id="result-bath-kw-f" type="text" value="0.00" readonly></td>
                                        <td><input id="bath-per-kwhr-f" type="text" value="0.00" readonly></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="label-cell">Holiday</td>
                                        <td><input id="kw-avg-h" type="text" class="form-control form-control-sm"
                                                readonly></td>
                                        <td><input id="input-kw-h" type="text" class="editable"></td>
                                        <td><input id="result-bath-kw-h" type="text" value="0.00" readonly></td>
                                        <td><input id="bath-per-kwhr-h" type="text" value="0.00" readonly></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                            <input id="kw-max-n" type="text" class="d-none" readonly>
                            <input id="kw-max-f" type="text" class="d-none" readonly>
                            <input id="kw-max-h" type="text" class="d-none" readonly>

                            <hr class="section-divider">

                            <!-- 2. ค่าความต้องการไฟฟ้า -->
                            <div class="section-title">2. ค่าความต้องการไฟฟ้า</div>
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
                                        <th class="th-left">ช่วงเวลา</th>
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
                                        <td class="label-cell">Max peak</td>
                                        <td class="sub-label">ชั่วโมงเริ่มต้น</td>
                                        <td><input id="kwhr-diff-ltime" type="text" readonly></td>
                                        <td class="sub-label">ชั่วโมงสิ้นสุด</td>
                                        <td><input id="kwhr-diff-ttime" type="text" readonly></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td colspan="2" class="sub-label">kW(max)</td>
                                        <td><input id="kwhr-diff-kwmax" type="text" readonly></td>
                                        <td></td>
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
                            <div class="section-title">3. ค่าเพาเวอร์แฟคเตอร์</div>
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
                            <div class="section-title">4. ค่าบริการ</div>
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
                            <div class="section-title">5. ค่า FT</div>
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
                            <div class="section-title">6. ค่าไฟฟ้ารวม</div>
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
                            <div class="section-title">7. ค่าภาษี</div>
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
                            <div class="section-title">8. ค่ารวมทั้งสิ้น</div>
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

                            <div style="overflow:hidden;">
                                <button id="btn-export" class="btn-export" onclick="createPDF(false)">
                                    <i class="bi bi-printer"></i> Export
                                </button>
                            </div>
                        </div><!-- /daily card -->


                        <!-- ════════════════════════════════
                             รายงานประจำเดือน (Monthly Report)
                             ════════════════════════════════ -->
                        <div class="report-card">
                            <div class="report-card-header">
                                <div class="report-card-icon"><i class="bi bi-calendar-month"></i></div>
                                <h5 class="report-card-title">รายงานประจำเดือน (Monthly Report)</h5>
                            </div>

                            <!-- 1. ค่าความต้องการ -->
                            <div class="section-title">1. ค่าความต้องการ</div>
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
                                        <th class="th-left">ช่วงเวลา</th>
                                        <th>kW</th>
                                        <th>บาท</th>
                                        <th>บาท/kWh</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="label-cell">On peak</td>
                                        <td><input id="kw-avg-month-n" type="text" readonly></td>
                                        <td><input id="result-bath-kw-month-n" type="text" value="0.00" readonly></td>
                                        <td><input id="result-bath-per-kwhr-month-n" type="text" value="0.00" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="label-cell">Off peak</td>
                                        <td><input id="kw-avg-month-f" type="text" readonly></td>
                                        <td><input id="result-bath-kw-month-f" type="text" value="0.00" readonly></td>
                                        <td><input id="result-bath-per-kwhr-month-f" type="text" value="0.00" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="label-cell">Holiday</td>
                                        <td><input id="kw-avg-month-h" type="text" readonly></td>
                                        <td><input id="result-bath-kw-month-h" type="text" value="0.00" readonly></td>
                                        <td><input id="result-bath-per-kwhr-month-h" type="text" value="0.00" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                            <input id="kw-max-month-n" type="text" class="d-none" readonly>
                            <input id="kw-max-month-f" type="text" class="d-none" readonly>
                            <input id="kw-max-month-h" type="text" class="d-none" readonly>

                            <hr class="section-divider">

                            <!-- 2. ค่าความต้องการไฟฟ้า -->
                            <div class="section-title">2. ค่าความต้องการไฟฟ้า</div>
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
                                        <th class="th-left">ช่วงเวลา</th>
                                        <th>ชั่วโมง</th>
                                        <th>kWhr</th>
                                        <th>บาท</th>
                                        <th>LoadFactor</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="label-cell">On peak</td>
                                        <td><input id="hour-diff-month-n" type="text" readonly></td>
                                        <td><input id="kwhr-diff-month-n" type="text" readonly></td>
                                        <td><input id="result-bath-kwhr-month-n" type="text" value="0.00" readonly></td>
                                        <td><input id="load-factor-month-n" type="text" value="0.00" readonly></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="label-cell">Off peak</td>
                                        <td><input id="hour-diff-month-f" type="text" readonly></td>
                                        <td><input id="kwhr-diff-month-f" type="text" readonly></td>
                                        <td><input id="result-bath-kwhr-month-f" type="text" value="0.00" readonly></td>
                                        <td><input id="load-factor-month-f" type="text" value="0.00" readonly></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="label-cell">Holiday</td>
                                        <td><input id="hour-diff-month-h" type="text" readonly></td>
                                        <td><input id="kwhr-diff-month-h" type="text" readonly></td>
                                        <td><input id="result-bath-kwhr-month-h" type="text" value="0.00" readonly></td>
                                        <td><input id="load-factor-month-h" type="text" value="0.00" readonly></td>
                                        <td></td>
                                    </tr>
                                    <tr class="maxpeak-row">
                                        <td class="label-cell">Max peak</td>
                                        <td class="sub-label">ชั่วโมงเริ่มต้น</td>
                                        <td><input id="kwhr-diff-month-ltime" type="text" readonly></td>
                                        <td class="sub-label">ชั่วโมงสิ้นสุด</td>
                                        <td><input id="kwhr-diff-month-ttime" type="text" readonly></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td colspan="2" class="sub-label">kW(max)</td>
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
                            <div class="section-title">3. ค่าความต้องการไฟฟ้า</div>
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
                                        <td><input id="kvar-diff-month-all" type="text" readonly></td>
                                        <td><input id="pf-avg-month-all" type="text" readonly></td>
                                        <td><input id="result-bath-kvar-month" type="text" value="0.00"></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr class="section-divider">

                            <!-- 4. ค่าบริการ -->
                            <div class="section-title">4. ค่าบริการ</div>
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
                                        <td><input id="input-service-month" type="text" class="editable"></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr class="section-divider">

                            <!-- 5. ค่า FT -->
                            <div class="section-title">5. ค่า FT</div>
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
                                        <td><input id="result-bath-ft-month" type="text" value="0.00" readonly></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr class="section-divider">

                            <!-- 6. ค่าไฟฟ้ารวม -->
                            <div class="section-title">6. ค่าไฟฟ้ารวม</div>
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
                                        <td><input id="result-bath-all-month" type="text" value="0.00" readonly></td>
                                        <td><input id="result-bath-all-kwhr-month" type="text" value="0.00" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr class="section-divider">

                            <!-- 7. ค่าภาษี -->
                            <div class="section-title">7. ค่าภาษี</div>
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
                                        <td><input id="result-bath-tax-month" type="text" value="0.00" readonly></td>
                                        <td><input id="result-bath-tax-kwhr-month" type="text" value="0.00" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr class="section-divider">

                            <!-- 8. ค่ารวมทั้งสิ้น -->
                            <div class="section-title">8. ค่ารวมทั้งสิ้น</div>
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
                                        <td><input id="result-bath-total-month" type="text" value="0.00" readonly
                                                class="total-input"></td>
                                        <td><input id="result-bath-total-kwhr-month" type="text" value="0.00" readonly
                                                class="total-input"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div style="overflow:hidden;">
                                <button id="btn-export-month" class="btn-print" onclick="createPDF(true)">
                                    <i class="bi bi-printer"></i> Export
                                </button>
                            </div>
                        </div><!-- /monthly card -->

                    </div><!-- /reports-wrapper -->

                    <div class="footer-note">
                        หมายเหตุ : ไม่มีการกำหนดราคาค่าไฟฟ้าที่เปลี่ยนแปลงในแต่ละช่วงเวลา...
                    </div>

                </div>
            </main>

            <?php include "../components/footer.php"; ?>

        </div>
    </div>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include '../scripts/scriptjs-report-meter-detail.html'; ?>

    <script>
    function updateDateDisplay() {
        const input = document.getElementById('select-filter-value');
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
        const input = document.getElementById('select-filter-value');
        if (!input.value) {
            const today = new Date().toISOString().split('T')[0];
            input.value = today;
            updateDateDisplay();
        }
    });
    </script>

</body>

</html>