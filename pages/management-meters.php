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
                            <span class="meters-sidebar__title"><?= $lang['list_of_all_meters'] ?></span>
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em"
                                    viewBox="0 0 24 24">
                                    <g fill="none">
                                        <path fill="currentColor" fill-opacity="0.16" fill-rule="evenodd"
                                            d="M19.806 20a9.77 9.77 0 0 0 2.13-5.037a9.7 9.7 0 0 0-.922-5.38a9.9 9.9 0 0 0-3.69-4.071A10.1 10.1 0 0 0 12 4c-1.884 0-3.73.524-5.324 1.512a9.9 9.9 0 0 0-3.69 4.07a9.7 9.7 0 0 0-.921 5.38A9.77 9.77 0 0 0 4.194 20z"
                                            clip-rule="evenodd" />
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="1"
                                            d="M12.707 15.293L18 10m1.806 10a9.77 9.77 0 0 0 2.13-5.037a9.7 9.7 0 0 0-.922-5.38a9.9 9.9 0 0 0-3.69-4.071A10.1 10.1 0 0 0 12 4c-1.884 0-3.73.524-5.324 1.512a9.9 9.9 0 0 0-3.69 4.07a9.7 9.7 0 0 0-.921 5.38A9.77 9.77 0 0 0 4.194 20zM13 16a1 1 0 1 1-2 0a1 1 0 0 1 2 0" />
                                    </g>
                                </svg>
                                <?= $lang['meter'] ?>
                            </div>
                            <div id="menu-config-2" class="menu">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em"
                                    viewBox="0 0 24 24">
                                    <g fill="none">
                                        <path fill="currentColor" d="M6 10v9h12v-9a6 6 0 0 0-12 0" opacity="0.16" />
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 19v-9a6 6 0 0 1 6-6v0a6 6 0 0 1 6 6v9M6 19h12M6 19H4m14 0h2m-9 3h2" />
                                        <circle cx="12" cy="3" r="1" stroke="currentColor" stroke-width="2" />
                                    </g>
                                </svg>
                                <?= $lang['notification'] ?>
                            </div>
                        </div>

                        <!-- JS uses id="config" inside id="meter-form" — preserved -->
                        <form id="meter-form">
                            <div id="config" class="w-100 p-3"
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
                                    <p>
                                        <?= $lang['view_meter_info_main'] ?><br>
                                        <?= $lang['view_meter_info_sub'] ?>
                                    </p>
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

    <script id="lang-data" type="application/json">
    <?= json_encode($lang, JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-management-meters.html"; ?>

    <script>
    window.addEventListener('load', () => {
        const observer = new MutationObserver(() => {
            const items = document.querySelectorAll('#meter-list > div:not(.meter-skeleton)');
            const badge = document.getElementById('meter-count');
            if (badge && items.length > 0) {
                badge.textContent = items.length + ' <?= $lang['list'] ?>';
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