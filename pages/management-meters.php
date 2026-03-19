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
    <title><?= $lang['config'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/management-meters.css">
</head>

<body style="height: 100svh; overflow: hidden;">
    <div id="main" class="d-flex" style="height: 100svh; overflow: hidden;">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column" style="height: 100svh; overflow: hidden;">
            <?php include "../components/header.php"; ?>

            <main class="meters-main">
                <div class="meters-panel">

                    <!-- ── Sidebar ── -->
                    <aside class="meters-sidebar">
                        <div class="meters-sidebar__header">
                            <span class="meters-sidebar__title">รายชื่อมิเตอร์ทั้งหมด</span>
                            <span class="meters-sidebar__count" id="meter-count">...</span>
                        </div>

                        <!-- JS uses id="meter-list" — preserved -->
                        <div id="meter-list" class="h-100 w-100">
                            <div class="meter-skeleton">
                                <div class="meter-skeleton__icon"></div>
                                <div class="meter-skeleton__name"></div>
                            </div>
                            <div class="meter-skeleton">
                                <div class="meter-skeleton__icon"></div>
                                <div class="meter-skeleton__name"></div>
                            </div>
                            <div class="meter-skeleton">
                                <div class="meter-skeleton__icon"></div>
                                <div class="meter-skeleton__name"></div>
                            </div>
                            <div class="meter-skeleton">
                                <div class="meter-skeleton__icon"></div>
                                <div class="meter-skeleton__name"></div>
                            </div>
                            <div class="meter-skeleton">
                                <div class="meter-skeleton__icon"></div>
                                <div class="meter-skeleton__name"></div>
                            </div>
                        </div>
                    </aside>

                    <!-- ── Content ── -->
                    <div class="meters-content">

                        <!-- JS uses id="menu-config-1" / "menu-config-2" — preserved -->
                        <div class="meters-tabs">
                            <div id="menu-config-1" class="menu active">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="2" />
                                    <path
                                        d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                                </svg>
                                มิเตอร์
                            </div>
                            <div id="menu-config-2" class="menu">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M5 12.55a11 11 0 0 1 14.08 0" />
                                    <path d="M1.42 9a16 16 0 0 1 21.16 0" />
                                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0" />
                                    <circle cx="12" cy="20" r="1" fill="currentColor" />
                                </svg>
                                เชื่อมต่อ
                            </div>
                        </div>

                        <!-- JS uses id="config" inside id="meter-form" — preserved -->
                        <form id="meter-form">
                            <div id="config" class="w-100 py-2"
                                style=" background:var(--bg-card); color:var(--text-body); min-height: 500px; ">
                                <div class=" meters-placeholder">
                                    <div class="meters-placeholder__icon-wrap">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v11m0 0H5a2 2 0 0 1-2-2V9m6 5h10a2 2 0 0 0 2-2V9m0 0H3" />
                                            <circle cx="12" cy="17" r="3" />
                                            <path d="M12 14v-2M9.27 15.5l-1.5-.87M14.73 15.5l1.5-.87" />
                                        </svg>
                                    </div>
                                    <h5><?= $lang['cmisb'] ?></h5>
                                    <p>เพื่อเรียกดูข้อมูลการใช้พลังงาน
                                        สถานะการเชื่อมต่อ<br>และประวัติการแจ้งเตือนของมิเตอร์ที่ต้องการ</p>
                                </div>
                            </div>
                        </form>

                    </div><!-- /meters-content -->
                </div><!-- /meters-panel -->
            </main>

            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-management-meters.html"; ?>

    <script>
    window.addEventListener('load', () => {
        const observer = new MutationObserver(() => {
            const items = document.querySelectorAll('#meter-list > div:not(.meter-skeleton)');
            const badge = document.getElementById('meter-count');
            if (badge && items.length > 0) {
                badge.textContent = items.length + ' รายการ';
            }
        });
        const list = document.getElementById('meter-list');
        if (list) observer.observe(list, {
            childList: true
        });
    });
    </script>

</body>

</html>