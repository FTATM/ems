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
                                        <th class="th-left"></th>
                                        <th>kW</th>
                                        <th>บาท/kW</th>
                                        <th>บาท</th>
                                        <th>บาท/kWh</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td><input id="kw-avg" type="text" readonly></td>
                                        <td><input id="input-kw" type="text" class="editable"></td>
                                        <td><input id="result-bath-kw" type="text" value="0.00" readonly></td>
                                        <td><input id="bath-per-kwhr" type="text" value="0.00" readonly></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                            <input id="kw-max" type="text" class="d-none" readonly>

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
                                        <th></th>
                                        <th>KVAR</th>
                                        <th>pf &lt;</th>
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
                                        <td><input id="pf-avg2" type="text" readonly></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><input id="pf-avg2" type="text" readonly></td>
                                        <td></td>
                                        <td></td>
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
                                        <th></th>
                                        <th>kW</th>
                                        <th>บาท</th>
                                        <th>บาท/kWh</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td><input id="kw-avg-m" type="text" readonly></td>
                                        <td><input id="result-bath-kw-m" type="text" value="0.00" readonly></td>
                                        <td><input id="result-bath-per-kwhr-m" type="text" value="0.00" readonly></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                            <input id="kw-max-m" type="text" class="d-none" readonly>

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
                                        <th>pf &lt;</th>
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
                                        <td><input id="pf-avg2-m" type="text" readonly></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><input id="pf-avg2-m" type="text" readonly></td>
                                        <td></td>
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
                                        <td><input id="input-service-m" type="text" class="editable"></td>
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
                                        <td><input id="result-bath-all-m" type="text" value="0.00" readonly></td>
                                        <td><input id="result-bath-all-kwhr-m" type="text" value="0.00" readonly></td>
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
                                        <td><input id="result-bath-tax-m" type="text" value="0.00" readonly></td>
                                        <td><input id="result-bath-tax-kwhr-m" type="text" value="0.00" readonly></td>
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
                                        <td><input id="result-bath-total-m" type="text" value="0.00" readonly
                                                class="total-input"></td>
                                        <td><input id="result-bath-total-kwhr-m" type="text" value="0.00" readonly
                                                class="total-input"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div style="overflow:hidden;">
                                <button id="btn-export-monthly" class="btn-print" onclick="createPDF(true)">
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
    <?php include '../scripts/scriptjs-report-meter.html'; ?>

    <script>
    /* ─── Sync Dark Mode กับ sidemenu (localStorage key: 'theme') ─── */
    window.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    });
    </script>

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