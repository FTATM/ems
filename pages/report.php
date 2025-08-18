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
            <?php include "../components/header.php"; ?>
            <div class="w-100 py-2 justify-content-center align-items-center d-flex">
                <div class="justify-content-center align-items-center d-flex flex-column gap-2" style="width: 90%;">
                    <div class="d-flex bg-white bg-opacity-10 p-3 w-100">
                        <div class="w-25">
                            <h4>Choose Data</h4>

                            <label for="select-location">Location</label>
                            <select id="select-location" class="form-select" onchange="checkLocationAndCreateSelect()">
                            </select>

                            <label for="select-building">Building</label>
                            <select id="select-building" class="form-select" onchange="loadingChart()">
                                <option value="-"> - </option>
                            </select>

                            <label for="datetime-from">From</label>
                            <input id="datetime-from" type="date" class="form-control" value="2025-08-01" onchange="loadingChart()">

                            <label for="datetime-to">To</label>
                            <input id="datetime-to" type="date" class="form-control" value="2025-08-16" onchange="loadingChart()">
                        </div>

                        <div class="w-75 p-2" id="linear-chart">
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-10 p-3 w-100">
                        <div class="d-flex justify-content-between align-items-center px-2">
                            <h3>Table</h3>
                            <div class="d-flex justify-content-end gap-2">
                                <div>Export : </div>
                                <input class="btn btn-sm border-1 bg-primary" style="width: 30%;" value="CSV" onclick="ExportCSV()">
                                <input class="btn btn-sm border-1 bg-primary" style="width: 30%;" value="Excel" onclick="ExportExcel()">
                            </div>
                        </div>
                        <div class="d-flex w-100 align-items-center justify-content-between p-2 rounded shadow-sm">
                            <div class="form-check me-3">
                                <input id="is-table-all-value" type="checkbox" class="form-check-input" onchange="ReloadTable()">
                                <label class="form-check-label" for="is-table-all-value">
                                    Show All Values
                                </label>
                            </div>

                            <!-- Select Rows -->
                            <div class="d-flex align-items-center">
                                <label for="select-table-show" class="me-2 mb-0 fw-semibold">Rows :</label>
                                <select id="select-table-show" class="form-select form-select-sm w-auto" onchange="ReloadTable()">
                                    <!-- <option value="all">All</option> -->
                                    <option selected value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>

                        <Table class="table table-bordered" id="table-data">
                            <thead>
                            </thead>
                            <tbody>
                            </tbody>
                        </Table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-report.html"; ?>
</body>

</html>