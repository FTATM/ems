<?php
include '../components/session.php';
checkLogin();
checkSession();
?>
<!DOCTYPE html>
<html lang="<?= $langCode ?>">
<script>
/* no-flash dark mode: ตั้ง html.dark ก่อน first paint (header.php ตั้งช้าไปทำให้ตาราง sticky ค้างสีเดิม) */
try { if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark'); } catch (e) {}
</script>
<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['allmeter'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/allmeter.css">
    <script>
    const LANG = <?= json_encode($lang) ?>;
    </script>
</head>

<body style="min-height: 100svh;">
    <div id="main" class="d-flex" style="min-height: 100svh;">

        <?php include "../components/sidemenu.php"; ?>

        <div class="w-100 d-flex flex-column" style="min-height: 100svh;">

            <?php include "../components/header.php"; ?>

            <!-- ─── Page Header Bar ─── -->
            <div class="allmeter-topbar">
                <h4 class="allmeter-topbar__title">
                    <span class="material-icons-outlined">electric_meter</span>
                    <?= $lang['allmeter'] ?>
                </h4>

                <div class="allmeter-search">
                    <input type="text" id="meter-search" placeholder="<?= $lang['search'] ?>"
                        oninput="filterMeters()" autocomplete="off">
                </div>

                <div class="allmeter-topbar__refresh">
                    <span class="material-icons-outlined refresh-spin">sync</span>
                    <span class="refresh-label"><?= $lang['refreshevery'] ?>:</span>
                    <input type="number" id="input-refresh" class="refresh-input" value="15" min="1" max="60"
                        onchange="setRefreshTime()">
                    <span class="refresh-label"><?= $lang['seconds'] ?></span>
                </div>
            </div>

            <!-- ─── KPI tiles ─── -->
            <div class="allmeter-kpi" id="allmeter-kpi">
                <div class="kpi-tile">
                    <span class="kpi-value" id="kpi-total">–</span>
                    <span class="kpi-label"><?= $lang['total_meters'] ?></span>
                </div>
                <div class="kpi-tile">
                    <span class="kpi-value" id="kpi-sum-kw">–</span>
                    <span class="kpi-label"><?= $lang['sum_kw'] ?></span>
                </div>
                <div class="kpi-tile">
                    <span class="kpi-value" id="kpi-sum-kwh">–</span>
                    <span class="kpi-label"><?= $lang['sum_kwh'] ?></span>
                </div>
                <div class="kpi-tile">
                    <span class="kpi-value kpi-status">
                        <span class="kpi-dot kpi-dot--on"></span><span id="kpi-active">–</span>
                        <span class="kpi-status-sep">/</span>
                        <span class="kpi-dot kpi-dot--off"></span><span id="kpi-inactive">–</span>
                    </span>
                    <span class="kpi-label"><?= $lang['active'] ?> / <?= $lang['inactive'] ?></span>
                </div>
            </div>

            <!-- ─── Main Content ─── -->
            <main class="allmeter-main flex-grow-1">

                <div class="allmeter-card">
                    <div class="table-responsive">
                        <table id="table-meter" class="allmeter-table w-100">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                        <div id="allmeter-empty" class="allmeter-empty" hidden><?= $lang['no_meter_found'] ?></div>
                    </div>

                    <!-- Pagination -->
                    <div class="allmeter-footer">
                        <div id="pagination-info" class="pagination-info"></div>
                        <div id="pagination" class="allmeter-pagination"></div>
                    </div>
                </div>

            </main>
            <?php include "../components/footer.php"; ?>

        </div>
    </div>

    <script id="theme-data" type="application/json">
        <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-allmeter.html"; ?>
</body>

</html>
