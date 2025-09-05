<?php
include '../components/session.php';
// checkLogin();
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">
<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['allmeter'] ?> - EMS</title>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <!-- side bar 1  -->
            <?php include "../components/header.php"; ?>
            <!-- side bar 2 -->
            <div class="container-fluid p-3">
                <!-- Main Content -->
                <div class="d-flex justify-content-between p-3">
                    <!-- รายงานประจำวัน -->
                    <div class="flex-fill me-2" style="background:#333B4A;
                     border-radius:10px; padding:24px;">
                        <h5 class="text-center text-white mb-2">รายงานประจำวัน</h5>
                        <!-- ...เนื้อหา... -->
                        <div class=" d-flex justify-content-center gap-4 px-4 py-2">
                            <div class="col-2 d-flex justify-content-center">
                                <input id="report-date-day" type="date"
                                    class="form-control form-control-sm m-10"
                                    onchange="showDate()">
                            </div>
                            <!-- <div id="list-input">
                            </div> -->
                        </div>
                        <br>
                         <!-- รายงานประจำวัน -->
                        <div class="mb-3 mt-1">
                            <div class="mb-1">1.ค่าความต้องการ</div>
                            <div class="d-flex justify-content-end">
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">Kw</label>
                                    </div>
                                    <input id="kw-avg" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">Kwr/บาท</label>
                                    </div>
                                    <input id="bath-kW" type="text" class="form-control form-control-sm bg-light text-dark"
                                        style="width:100px;" oninput="bathKw()">
                                </div>
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">บาท</label>
                                    </div>
                                    <input id="result-bath-Kw" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">บาท/Kwhr</label>
                                    </div>
                                    <input id="result-bath-kWhr" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                            </div>

                            <!-- ค่าความต้องการไฟฟ้า -->
                            <div class="mb-1">2.ค่าความต้องการไฟฟ้า</div>
                            <div class="d-flex justify-content-end">
                                <div class="me-2">              
                                    
                                    <div>
                                        <label class="me-2 mb-0">ชั่วโมง</label>
                                    </div>
                                    <input id="hour-diff" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">Kwhr</label>
                                    </div>
                                    <input id="value-kWhr" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly >
                                </div>
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">บาท/Kwhr</label>
                                    </div>
                                    <input id="value-Input-kwhr" type="text" class="form-control form-control-sm bg-light text-dark"
                                        style="width:100px;" oninput="showBathKwhr()">
                                </div>
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">บาท</label>
                                    </div>
                                    <input id="result-bath-kWhr-second" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                                <!-- เหลือยังไม่ได้ทำ -->
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">LoadFactor</label>
                                    </div>
                                    <input id="value-load-factor" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                            </div>


                            <!--   ค่าเพาเวอร์แฟคเตอร์ -->
                            <div class="mb-1">3.ค่าเพาเวอร์แฟคเตอร์</div>
                            <div class="d-flex justify-content-end">
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">KVAR</label>
                                    </div>
                                    <input id="value-kvar" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">pf < 0.85</label>
                                    </div>
                                    <input id="value-pf" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">บาท/kVar</label>
                                    </div>
                                    <input id="bath-kvar" type="text" class="form-control form-control-sm bg-light text-dark"
                                        style="width:100px; " 
                                        >
                                </div>
                                <div class="me-2">
                                    <label class="me-2 mb-0"></label>
                                    <input id="result-bath-KVar" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                            </div>

                            <br>


                            <div class="mb-1">4.ค่าบริการ</div>
                            <div class="d-flex justify-content-end ms-4 ">


                                <div class="ms-2">
                                    <input id="value-input-service" 
                                    class="form-control form-control-sm bg-light text-dark" type="text" 
                                    style="width:100px;" oninput="ServiceCal()" >
                                </div>



                            </div>

                            <div class="mb-1">5.ค่า FT</div>
                            <div class="d-flex justify-content-end">

                                <div  class="ms-2 ms-auto">
                                    <input id="value-input-ft" class="form-control form-control-sm bg-light text-dark"
                                     type="text"  style="width:100px;">
                                </div>



                                <div class="ms-2">
                                    <input id="value-ft-result" class="form-control form-control-sm" 
                                    type="text" style="width:100px;" readonly>
                                </div>

                                <div class="ms-2">
                                    <label class="">บาท/Khwr</label>
                                </div>

                            </div>

                            <div class="mb-1">6.ค่าไฟฟ้ารวม</div>
                            <div class="d-flex justify-content-end">


                                <div class="ms-2">
                                    <input id="value-total-ft-first" class="form-control form-control-sm"
                                     type="text" style="width:100px;" readonly>
                                </div>



                                <div class="ms-2">
                                    <input id="value-total-ft-second" class="form-control form-control-sm" 
                                    type="text" style="width:100px;" readonly>
                                </div>
                            </div>

                            <div class="mb-1">7.ค่าภาษี</div>
                            <div class="d-flex justify-content-end">


                                <div class="ms-2">
                                    <input id="value-input-tax" class="form-control form-control-sm bg-light text-dark" 
                                    type="text"  style="width:100px;">
                                </div>



                                <div class="ms-2">
                                    <input id="value-first-Tax" class="form-control form-control-sm" 
                                    type="text" style="width:100px;" readonly>
                                </div>


                                <div class="ms-2">
                                    <input id="value-second-Tax" class="form-control form-control-sm"
                                     type="text" style="width:100px;" readonly>
                                </div>

                            </div>

                            <div class="mb-1">8.ค่ารวมทั้งสิ้น</div>
                            <div class="d-flex justify-content-end">


                                <div class="ms-2">
                                    <input id="value-total-first" class="form-control form-control-sm"
                                     type="text" style="width:100px;" readonly>
                                </div>

                                <div class="ms-2">
                                    <input id="value-total-second" class="form-control form-control-sm" 
                                    type="text" style="width:100px;" readonly>
                                </div>
                            </div>
                            <button id="btn-export" class="btn btn-primary mt-3 float-end">Export</button>
                        </div>

                    </div>
                    <!-- รายงานประจำเดือน -->
                    <div class="flex-fill ms-2" style="background:#333B4A; border-radius:10px; padding:24px;">
                        <h5 class="text-center text-white mb-3 ">รายงานประจำเดือน</h5>

                        <div class=" d-flex justify-content-center gap-4 px-4  py-2 ">
                            <div class="col-2 d-flex justify-content-center">
                                <input id="report-day-month" type="month"
                                    class="form-control form-control-sm m-10"
                                     onchange="showMonth()">
                            </div>

                        </div>

                         <div class="mb-3 mt-1">
                            <div class="mb-1">1.ค่าความต้องการ</div>
                            <div class="d-flex justify-content-end">
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">Kw</label>
                                    </div>
                                    <input id="value-kw-for-monthly" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">บาท</label>
                                    </div>
                                    <input id ="input-rate-Kw-monthly" type="text" class="form-control form-control-sm bg-light text-dark"
                                        style="width:100px;"   >
                                </div>
                                
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">บาท/Kwhr</label>
                                    </div>
                                    <input id="result-bath-kW-monthly" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                            </div>

                            <!-- ค่าความต้องการไฟฟ้า -->
                            <div class="mb-1">2.ค่าความต้องการไฟฟ้า</div>
                            <div class="d-flex justify-content-end">
                                <div class="me-2">              
                                    
                                    <div>
                                        <label class="me-2 mb-0">ชั่วโมง</label>
                                    </div>
                                    <input id="show-hour-diff-monthly" type="text" 
                                    class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                    </div>

                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">Kwhr</label>
                                    </div>
                                    <input id="show-Kwhr-for-monthly" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>

                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">Kwhr</label>
                                    </div>
                                    <input id="input-Kwhr-for-monthly" type="text" 
                                    class="form-control bg-light text-dark form-control-sm "
                                        style="width:100px;">
                                </div>

                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">บาท</label>
                                    </div>
                                    <input id="show-Kwhr-cal-for-monthly" type="text" class="form-control form-control-sm "
                                        style="width:100px;">
                                </div>

                                
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">LoadFactor</label>
                                    </div>
                                    <input id="show-load-factor-for-monthly" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                            </div>


                            <!--   ค่าเพาเวอร์แฟคเตอร์ -->
                            <div class="mb-1">3.ค่าเพาเวอร์แฟคเตอร์</div>
                            <div class="d-flex justify-content-end">
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">KVAR</label>
                                    </div>
                                    <input id="power-factor-kva-for-monthly" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">pf < 0.85</label>
                                    </div>
                                    <input id="power-factor-pf-monthly" type="text" class="form-control form-control-sm"
                                        style="width:100px;" readonly>
                                </div>

                                <!-- Input Kvar for monthly -->
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">บาท/kVar</label>
                                    </div>
                                    <input id="input-Kvar-for-monthly" type="text" 
                                    class="form-control form-control-sm bg-light text-dark"
                                        style="width:100px;">
                                </div>

                                <!-- Result Kvar for monthly -->
                                <div class="me-2">
                                    <div>
                                        <label class="me-2 mb-0">บาท/kVar</label>
                                    </div>
                                    <input id="result-Kvar-for-monthly" type="text" class="form-control form-control-sm "
                                        style="width:100px;">
                                </div>
                               
                            </div>

                            <br>


                            <div class="mb-1">4.ค่าบริการ</div>
                            <div class="d-flex justify-content-end ms-4 ">


                                <div class="ms-2">
                                    <input id="service-for-monthly" class="form-control form-control-sm bg-light text-dark" type="text" style="width:100px;">
                                </div>


                            </div>

                            <!-- เริ่มมีปัญหา -->

                            <div class="mb-1">5.ค่า FT</div>
                            <div class="d-flex justify-content-end">
                                <!-- input FT  -->
                                
                                <div class="d-flex justify-content-end">

                                <div  class="ms-2 ms-auto">
                                    <input id="input-ft-for-monthly" class="form-control form-control-sm bg-light text-dark"
                                     type="text"  style="width:100px;">
                                </div>

                                <div class="ms-2">
                                    <input id="result-ft-for-monthly" class="form-control
                                    form-control-sm" 
                                    type="text" style="width:100px;" readonly>
                                </div>

                                <div class="ms-2">
                                    <label class="">บาท/Khwr</label>
                                </div>
                            </div>

                            </div>

                            <div class="mb-1">6.ค่าไฟฟ้ารวม</div>
                            <div class="d-flex justify-content-end">

                                <div class="ms-2">
                                    <input id="value-Electricity-total-first-monthly" 
                                    class="form-control form-control-sm" type="text" style="width:100px;" readonly>
                                </div>

                                <div class="ms-2">
                                    <input id="value-Electricity-total-second-monthly" 
                                    class="form-control form-control-sm" type="text" style="width:100px;" readonly>
                                </div>

                            </div>

                            <div class="mb-1">7.ค่าภาษี</div>
                            <div class="d-flex justify-content-end">

                                <div class="ms-2">
                                    <input id="input-value-tax-for-monthly" 
                                    class="form-control form-control-sm bg-light text-dark" 
                                    type="text" style="width:100px;" >
                                </div>

                                <div class="ms-2">
                                    <input id="result-first-value-tax-for-monthly" 
                                    class="form-control form-control-sm" type="text" style="width:100px;" readonly>
                                </div>

                                <div class="ms-2">
                                    <input id="result-second-value-tax-for-monthly" 
                                    class="form-control form-control-sm" type="text" style="width:100px;" readonly>
                                </div>
                            </div>

                            <div class="mb-1">8.ค่ารวมทั้งสิ้น</div>
                            <div class="d-flex justify-content-end">


                                <div class="ms-2">
                                    <input id="value-total-before-sum--for-monthly"
                                     class="form-control form-control-sm" type="text" style="width:100px;" readonly>
                                </div>


                                <div class="ms-2">
                                    <input id="value-total-after-sum-for-monthly" class="form-control form-control-sm" type="text" style="width:100px;" readonly>
                                </div>
                            </div>
                            <br> 

                            <button id="btn-export-monthly" class="btn btn-primary mt-3 float-end">Export</button>
                        </div>
                        <!-- ...เนื้อหา... 
                        
                    </div>


                </div>
                <!-- หมายเหตุ -->
                <div class="mt-2 p-2">
                    หมายเหตุ : ไม่มีการกำหนดราคาค่าไฟฟ้าที่เปลี่ยนแปลงในแต่ละช่วงเวลา...
                </div>
            </div>
        </div>


        <?php include "../scripts/scriptjs.html"; ?>
        <?php include '../scripts/report-meter.html'; ?>

</body>

</html>