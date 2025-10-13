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
    <title><?= $lang['overview'] ?> - EMS</title>

</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="d-flex">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
            <div class="w-100 justify-content-center align-items-center d-flex">
                <div class="justify-content-center align-items-center d-flex flex-column" style="width: 90%;">
                    <div class="w-100 text-center py-4 px-5 m-0 my-2 bg-secondary bg-opacity-25 d-flex justify-content-center position-relative">
                        <h2 class="m-0">Overview</h2>
                        <div class="d-flex gap-2 align-items-center position-absolute end-0 me-5">
                            <label for="input-refresh" class="form-label form-label-sm text-end text-nowrap">Refresh every :</label>
                            <input type="number" id="input-refresh" class="form-control form-control-sm ms-2" style="width: 80px;" value="15" min="1" max="30" onchange="setRefreshTime()">
                            <label for="input-refresh" class="form-label form-label-sm">Seconds</label>
                        </div>
                    </div>
                    <div class="d-flex w-100 justify-content-center" style="height: 80svh;">
                        <div class="w-25 px-5 bg-light bg-opacity-10 shadow-sm d-flex flex-column gap-2">
                            <h3 class="w-100 text-center my-4" id="name-meter">-</h3>
                            <div class="w-100 border mb-3" id="gauge-kW" style="min-height: 20%;"></div>
                            <div class="w-100 d-flex gap-2">
                                <div>
                                    <h5>Voltage A</h5>
                                    <input id="VoltageA" type="text" class="form-control bg-secondary bg-opacity-25" readonly>
                                </div>
                                <div>
                                    <h5>Voltage B</h5>
                                    <input id="VoltageB" type="text" class="form-control bg-secondary bg-opacity-25" readonly>
                                </div>
                                <div>
                                    <h5>Voltage C</h5>
                                    <input id="VoltageC" type="text" class="form-control bg-secondary bg-opacity-25" readonly>
                                </div>
                            </div>
                            <div class="w-100 d-flex gap-2">
                                <div>
                                    <h5>Current A</h5>
                                    <input id="CurrentA" type="text" class="form-control bg-secondary bg-opacity-25" readonly>
                                </div>
                                <div>
                                    <h5>Current B</h5>
                                    <input id="CurrentB" type="text" class="form-control bg-secondary bg-opacity-25" readonly>
                                </div>
                                <div>
                                    <h5>Current C</h5>
                                    <input id="CurrentC" type="text" class="form-control bg-secondary bg-opacity-25" readonly>
                                </div>
                            </div>
                            <div class="w-100">
                                <h5 class="ps-2">Pf</h5>
                                <input id="Pf" type="text" class="form-control bg-secondary bg-opacity-25" readonly>
                            </div>
                            <div class="w-100">
                                <h5 class="ps-2">Frequency</h5>
                                <input id="frequency" type="text" class="form-control bg-secondary bg-opacity-25" readonly>
                            </div>
                        </div>
                        <div class="w-50 position-relative">
                            <model-viewer id="myModel" alt="3D model" auto-rotate camera-controls ar
                                style="width: 100%; height: 100%; background-color: transparent;">
                            </model-viewer>
                            <!-- <div id="3d-viewer" style="position: relative; display: inline-block;"> -->
                                <!-- รูปภาพ -->
                                <!-- <img src="../assets/images/map.png" alt="My PNG" style="width: 100%; height: auto;"> -->
                            <!-- </div> -->
                            <div class="position-absolute w-25 bg-light bg-opacity-10 px-3 py-2 rounded flex-column gap-2" id="infomation-meter" style="top: 10px; right: 10px; display: none;">sadagsd</div>
                        </div>
                        <div class="w-25 px-3 bg-white bg-opacity-10 shadow-sm d-flex flex-column gap-1" id="listgroup">
                        </div>
                    </div>
                </div>
                <!-- <model-viewer id="myModel" alt="3D model" auto-rotate camera-controls ar
                    style="width: 600px; height: 400px; background-color: #eee;">
                </model-viewer> -->

            </div>
        </div>
    </div>


    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-map.html"; ?>
</body>

</html>