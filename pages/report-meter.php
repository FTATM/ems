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
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="container-fluid px-3">
                <div class="bg-secondary bg-opacity-25 d-flex flex-wrap justify-content-end gap-3 align-items-center mt-2">
                    <table class="table table-bordered w-50 m-0">
                        <tr>
                            <td colspan="3" class="text-center">
                                <p class="m-0"><?= $lang['fta'] ?></p>
                            </td>
                            <td class="text-center">
                                <p class="m-0">version</p>
                            </td>
                            <td class="text-center">
                                <p class="m-0">Demo</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center col-2">
                                <label class="ps-2 fw-bold" for="locations">Location :</label>
                            </td>
                            <td class="text-center col-3 bg-light bg-opacity-75 text-black">
                                <p id="location" class="px-2 m-0"></p>
                            </td>
                            <td class="text-center col-2">
                                <label class="ps-2" for="meters"><?= $lang['meter'] ?> : </label>
                            </td>
                            <td colspan="2">
                                <select id="select-meters" class="form-select form-select-sm text-white border-hover" onchange="filterDataInMeters()"> </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center col-2">
                                <label class="ps-2 fw-bold" for="groups">Group : </label>
                            </td>
                            <td class="text-center col-3 bg-light m-1 bg-opacity-75 text-black">
                                <p id="group" class="px-2 m-0"></p>
                            </td>
                            <td class="text-center col-2">
                                <label class="ps-2" for="meters"><?= $lang['time'] ?> : </label>
                            </td>
                            <td colspan="2">
                                <input id="select-filter-value" type="date" class="form-control form-control-sm border-hover" onchange="filterDataInMeters()">
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="d-flex justify-content-between bg-secondary bg-opacity-25">
                    <div class="flex-fill me-1 p-4 rounded w-50">
                        <h5 class="text-center text-white mb-2">รายงานประจำวัน</h5>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">1.ค่าความต้องการ</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"> </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">kW</label>
                                    <input id="kw-avg" type="text" class="form-control form-control-sm" readonly>
                                    <input id="kw-max" type="text" class="form-control form-control-sm d-none" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">บาท/kW</label>
                                    <input id="input-kw" type="text" class="form-control form-control-sm bg-light text-dark">
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">บาท</label>
                                    <input id="result-bath-kw" type="text" value="0.00" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">บาท/kWh</label>
                                    <input id="bath-per-kwhr" type="text" value="0.00" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <!-- ค่าความต้องการไฟฟ้า -->
                            <div class="mb-1 align-content-center">2.ค่าความต้องการไฟฟ้า</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">ชั่วโมง</label>
                                    <input id="hour-diff" type="text" class="form-control form-control-sm" readonly>
                                    <input id="hour-value" type="text" class="form-control form-control-sm d-none">
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">kWh</label>
                                    <input id="kwhr-diff" type="text" class="form-control form-control-sm" readonly>
                                    <input id="kwhr-fv" type="text" class="form-control form-control-sm d-none" readonly>
                                    <input id="kwhr-lv" type="text" class="form-control form-control-sm d-none" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">บาท/kWh</label>
                                    <input id="input-kwhr" type="text" class="form-control form-control-sm bg-light text-dark">
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">บาท</label>
                                    <input id="result-bath-kwhr" type="text" value="0.00" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">LoadFactor</label>
                                    <input id="load-factor" type="text" value="0.00" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">3.ค่าเพาเวอร์แฟคเตอร์</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">KVAR</label>
                                    <input id="kvar-avg" type="text" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">pf &lt; 0.85</label>
                                    <input id="pf-avg" type="text" class="form-control form-control-sm" readonly>
                                    <input id="pf-avg2" type="text" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">บาท/kVar</label>
                                    <input id="input-kvar" type="text" class="form-control form-control-sm bg-light text-dark">
                                </div>
                                <div class="me-2 w-20">
                                    <div class="d-flex">
                                        <label class="me-2 mb-0">บาท</label>
                                        <!-- <i class="bi bi-question-circle"></i> -->
                                    </div>
                                    <input id="result-bath-kvar" type="text" value="0.00" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="me-2 w-20 opacity-0"> </div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">4.ค่าบริการ</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"> </div>
                                <div class="me-2 w-20 opacity-0"> </div>
                                <div class="me-2 w-20 opacity-0"> </div>
                                <div class="me-2 w-20">
                                    <input id="input-service" class="form-control form-control-sm bg-light text-dark" type="text">
                                </div>
                                <div class="me-2 w-20 opacity-0"> </div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">5.ค่า FT</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <input id="input-ft" class="form-control form-control-sm bg-light text-dark" type="text">
                                </div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-ft" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="px-2">บาท/kWh</label>
                                </div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">6.ค่าไฟฟ้ารวม</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-all" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-all-kwhr" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">7.ค่าภาษี</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <input id="input-tax" class="form-control form-control-sm bg-light text-dark" type="text">
                                </div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-tax" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-tax-kwhr" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">8.ค่ารวมทั้งสิ้น</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-total" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-total-kwhr" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                            </div>
                        </div>
                        <button id="btn-export" class="btn btn-primary mt-3 float-end" onclick="createPDF(false)">Export</button>
                    </div>
                    <!-- รายงานประจำเดือน -->
                    <div class="flex-fill ms-1 p-4 rounded w-50">
                        <h5 class="text-center text-white mb-3 ">รายงานประจำเดือน</h5>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">1.ค่าความต้องการ</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">Kw</label>
                                    <input id="kw-avg-m" type="text" class="form-control form-control-sm" readonly>
                                    <input id="kw-max-m" type="text" class="form-control form-control-sm d-none" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">บาท</label>
                                    <input id="result-bath-kw-m" type="text" value="0.00" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">บาท/Kwh</label>
                                    <input id="result-bath-per-kwhr-m" type="text" value="0.00" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">2.ค่าความต้องการไฟฟ้า</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">ชั่วโมง</label>
                                    <input id="hour-diff-m" type="text" class="form-control form-control-sm" readonly>
                                    <input id="hour-value-m" type="text" class="form-control form-control-sm d-none">
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">Kwhr</label>
                                    <!-- <input id="kwhr-avg-m" type="text" class="form-control form-control-sm" readonly> -->
                                    <input id="kwhr-diff-m" type="text" class="form-control form-control-sm" readonly>
                                    <input id="kwhr-fv-m" type="text" class="form-control form-control-sm d-none" readonly>
                                    <input id="kwhr-lv-m" type="text" class="form-control form-control-sm d-none" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">บาท</label>
                                    <input id="result-bath-kwhr-m" type="text" value="0.00" class="form-control form-control-sm ">
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">LoadFactor</label>
                                    <input id="load-factor-m" type="text" value="0.00" class="form-control form-control-sm" readonly>
                                </div>
                            </div>

                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">3.ค่าความต้องการไฟฟ้า</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">KVAR</label>
                                    <input id="kvar-avg-m" type="text" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="me-2 mb-0">pf &lt; 0.85</label>
                                    <input id="pf-avg-m" type="text" class="form-control form-control-sm" readonly>
                                    <input id="pf-avg2-m" type="text" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <div class="d-flex">
                                        <label class="me-2 mb-0">บาท</label>
                                        <!-- <i class="bi bi-question-circle"></i> -->
                                    </div>
                                    <input id="result-bath-kvar-m" type="text" value="0.00" class="form-control form-control-sm ">
                                </div>
                                <div class="me-2 w-20 opacity-0"></div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">4.ค่าบริการ</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <input id="input-service-m" class="form-control form-control-sm bg-light text-dark" type="text">
                                </div>
                                <div class="me-2 w-20 opacity-0"></div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">5.ค่า FT</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-ft-m" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <label class="px-2">บาท/Khwr</label>
                                </div>
                            </div>

                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">6.ค่าไฟฟ้ารวม</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-all-m" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-all-kwhr-m" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">7.ค่าภาษี</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-tax-m" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-tax-kwhr-m" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="boxcell">
                            <div class="mb-1 align-content-center">8.ค่ารวมทั้งสิ้น</div>
                            <div class=" d-flex justify-content-end w-70">
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20 opacity-0"></div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-total-m" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                                <div class="me-2 w-20">
                                    <input id="result-bath-total-kwhr-m" value="0.00" class="form-control form-control-sm" type="text" readonly>
                                </div>
                            </div>
                        </div>
                        <button id="btn-export-monthly" class="btn btn-primary mt-3 float-end" onclick="createPDF(true)">Export</button>
                    </div>
                </div>
                <div class="mt-2 p-2">
                    หมายเหตุ : ไม่มีการกำหนดราคาค่าไฟฟ้าที่เปลี่ยนแปลงในแต่ละช่วงเวลา...
                </div>
            </div>
        </div>


        <?php include "../scripts/scriptjs.html"; ?>
        <?php include '../scripts/scriptjs-report-meter.html'; ?>

</body>

</html>