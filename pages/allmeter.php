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
    <title><?= $lang['allmeter'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/allmeter.css">
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

                <div class="d-flex align-items-center gap-3">
                    <div class="allmeter-topbar__refresh">
                        <span class="material-icons-outlined refresh-spin">sync</span>
                        <span class="refresh-label">Refresh every:</span>
                        <input type="number" id="input-refresh" class="refresh-input" value="15" min="1" max="30"
                            onchange="setRefreshTime()">
                        <span class="refresh-label">Seconds</span>
                    </div>
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

    <script>
    (function() {
        const STORAGE_KEY = 'ems-theme';
        const html = document.documentElement;
        const icon = document.getElementById('theme-icon');

        function applyTheme(dark) {
            if (dark) {
                html.classList.add('dark');
                if (icon) icon.textContent = 'light_mode';
            } else {
                html.classList.remove('dark');
                if (icon) icon.textContent = 'dark_mode';
            }
        }

        /* โหลด preference ที่บันทึกไว้ หรือ fallback ตาม OS */
        const saved = localStorage.getItem(STORAGE_KEY);
        const prefersDark = saved !== null ?
            saved === 'dark' :
            window.matchMedia('(prefers-color-scheme: dark)').matches;

        applyTheme(prefersDark);

        window.toggleTheme = function() {
            const isDark = html.classList.toggle('dark');
            localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
            if (icon) icon.textContent = isDark ? 'light_mode' : 'dark_mode';
        };
    })();
    </script>

</body>

</html>