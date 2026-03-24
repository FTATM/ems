<?php
include '../components/session.php';
?>
<!DOCTYPE html>
<html lang="<?= $langCode ?>">
<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['home'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/mndidb.css">
</head>

<body>
    <div id="main" class="d-flex" style="height:100svh; overflow:hidden;">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column" style="height:100svh; overflow:hidden;">
            <?php include "../components/header.php"; ?>

            <main class="mndidb-main">

                <!-- Hero -->
                <div class="mndidb-hero">
                    <h2><?= $lang['database_console_title'] ?></h2>
                    <p><?= $lang['database_console_desc'] ?></p>
                </div>

                <!-- Layout -->
                <div class="mndidb-body">

                    <!-- Left: SQL editor -->
                    <div class="mndidb-editor-wrap">

                        <!-- Toolbar -->
                        <div class="mndidb-toolbar">
                            <div class="mndidb-toolbar__label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <ellipse cx="12" cy="5" rx="9" ry="3" />
                                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3" />
                                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" />
                                </svg>
                                <?= $lang['sql_editor'] ?>
                            </div>
                            <div class="mndidb-toolbar__actions">
                                <button class="mndidb-btn mndidb-btn--danger" onclick="cleartext()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6" />
                                        <path d="M19 6l-1 14H6L5 6" />
                                        <path d="M10 11v6" />
                                        <path d="M14 11v6" />
                                        <path d="M9 6V4h6v2" />
                                    </svg>
                                    <?= $lang['clear'] ?>
                                </button>
                                <button id="btn-run" class="mndidb-btn mndidb-btn--primary" onclick="excuteStringSQL()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polygon points="5 3 19 12 5 21 5 3" />
                                    </svg>
                                    <?= $lang['execute'] ?>
                                </button>
                            </div>
                        </div>

                        <!-- Editor -->
                        <div id="sqltext" contenteditable="true" class="mndidb-editor" oninput="highlightSQL()"></div>

                        <!-- Result label -->
                        <div class="mndidb-result-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                            </svg>
                            <?= $lang['resultt'] ?>
                        </div>
                        <div id="output" class="mndidb-output"></div>

                    </div><!-- /.mndidb-editor-wrap -->

                    <!-- Right: Menu -->
                    <aside class="mndidb-sidebar">
                        <div class="mndidb-sidebar__title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <line x1="8" y1="6" x2="21" y2="6" />
                                <line x1="8" y1="12" x2="21" y2="12" />
                                <line x1="8" y1="18" x2="21" y2="18" />
                                <line x1="3" y1="6" x2="3.01" y2="6" />
                                <line x1="3" y1="12" x2="3.01" y2="12" />
                                <line x1="3" y1="18" x2="3.01" y2="18" />
                            </svg>
                            <?= $lang['menu'] ?>
                        </div>

                        <div class="mndidb-menu-card">
                            <div class="mndidb-menu-card__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg> <?= $lang['update_datetime'] ?>
                            </div>
                            <div class="mndidb-menu-card__body">
                                <div class="mndidb-menu-card__title"></div>
                                <p class="mndidb-menu-card__desc"><?= $lang['update_datetime_desc'] ?></p>
                                <p id="status-update-date" class="mndidb-menu-card__status"></p>
                                <button id="btn-update-all" class="mndidb-btn mndidb-btn--primary w-100"
                                    onclick="updatedatetime()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="23 4 23 10 17 10" />
                                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                                    </svg>
                                    <?= $lang['update'] ?>
                                </button>
                            </div>
                        </div>

                    </aside>

                </div><!-- /.mndidb-body -->

            </main>
            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-mndidb.html"; ?>
</body>

</html>