<?php
include '../components/session.php';
checkLogin();
checkSession();

$EMS_PAGE_TITLE = $lang['config'] . ' - EMS';
$EMS_SHELL_LOCKED = true;            // config workbench — lock the viewport
include '../components/doc-open.php';
?>
    <link rel="stylesheet" href="../styles/management-meters.css">

<?php include '../components/app-shell-open.php'; ?>

            <div class="meters-page">

                <!-- ── Hero toolbar ── -->
                <div class="ems-toolbar meters-hero">
                    <span class="meters-hero__title">
                        <span class="meters-hero__icon"><i class="bi bi-sliders2"></i></span>
                        <?= $lang['config'] ?>
                    </span>
                    <label class="meters-search">
                        <i class="bi bi-search"></i>
                        <input id="meter-search" type="text" placeholder="<?= $lang['search_meter'] ?>"
                            oninput="filterMeterList()" autocomplete="off">
                    </label>
                    <div class="ems-toolbar__spacer"></div>
                    <span class="ems-pill" id="meter-count">…</span>
                </div>

                <!-- ── Body: sidebar list + config card ── -->
                <div class="meters-panel">

                    <!-- Sidebar — JS uses id="meter-list" -->
                    <aside class="ems-panel meters-sidebar">
                        <div class="meters-sidebar__head"><?= $lang['list_of_all_meters'] ?></div>
                        <div id="meter-list">
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

                    <!-- Content — split 70:30. JS uses id="menu-config-1" / "menu-config-2"
                         / "meter-form" / "config" ; #menu-config-* are kept in the DOM as the
                         pane headers (the page shim below drives both panes off one #config). -->
                    <div class="ems-card meters-content">

                        <!-- General / meter settings pane (70%) -->
                        <section class="meters-pane meters-pane--general">
                            <div id="menu-config-1" class="menu active meters-pane__head">
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

                            <form id="meter-form">
                                <div id="config">
                                    <div class="meters-placeholder">
                                        <div class="meters-placeholder__icon-wrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
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
                        </section>

                        <!-- Notify pane (30%) — filled by the shim from #config's notify render -->
                        <section class="meters-pane meters-pane--notify">
                            <div id="menu-config-2" class="menu meters-pane__head">
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
                            <div id="notify-pane">
                                <div class="notify-empty"><?= $lang['view_meter_info_sub'] ?></div>
                            </div>
                        </section>

                    </div><!-- /meters-content -->
                </div><!-- /meters-panel -->

            </div><!-- /meters-page -->

<?php include '../components/app-shell-close.php'; ?>

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

    <script>
    /* ── 70:30 split shim ──────────────────────────────────────────────
       scriptjs-management-meters.html renders EITHER the meter form OR the
       notify list into #config, gated by which of #menu-config-1/2 is
       .active. This page shows both at once, so wrap loadDataMeter():
       render the notify view once (menu-config-2 active) and copy its
       markup into #notify-pane, then render the meter form into #config
       (menu-config-1 active) as the resting state. The vendor script is
       untouched. */
    window.addEventListener('load', () => {
        if (typeof loadDataMeter !== 'function') return;

        const renderOne  = loadDataMeter;                       // original
        const m1         = document.getElementById('menu-config-1');
        const m2         = document.getElementById('menu-config-2');
        const config     = document.getElementById('config');
        const notifyPane = document.getElementById('notify-pane');
        if (!m1 || !m2 || !config || !notifyPane) return;

        window.loadDataMeter = async function (meter) {
            if (!meter || Object.keys(meter).length === 0) return;

            // 1) notify view → right pane
            m1.classList.remove('active');
            m2.classList.add('active');
            await renderOne(meter);
            notifyPane.innerHTML = config.innerHTML;

            // 2) meter form → #config (left pane) = resting state
            m2.classList.remove('active');
            m1.classList.add('active');
            await renderOne(meter);
        };
    });
    </script>

<?php include '../components/doc-close.php'; ?>
