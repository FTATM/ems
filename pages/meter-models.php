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
    <title><?= $lang['meter_models'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/management-meters.css">
    <link rel="stylesheet" href="../styles/meter-models.css">
</head>

<body style="height: 100svh; overflow: hidden;">
    <div id="main" class="d-flex" style="height: 100svh; overflow: hidden;">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column" style="height: 100svh; overflow: hidden;">
            <?php include "../components/header.php"; ?>

            <main class="meters-main">
                <div class="meters-panel">

                    <!-- ── รายชื่อรุ่นมิเตอร์ ── -->
                    <aside class="meters-sidebar">
                        <div class="meters-sidebar__header">
                            <span class="meters-sidebar__title"><?= $lang['meter_models'] ?></span>
                            <span class="meters-sidebar__count" id="model-count">...</span>
                            <button type="button" class="btn btn-sm btn-primary w-100 mb-2" onclick="openNewModelForm()">
                                <i class="bi bi-plus-lg"></i> <?= $lang['add_model'] ?>
                            </button>
                            <a class="btn btn-sm btn-outline-secondary w-100 mb-2" href="management-meters.php">
                                <?= $lang['back_to_meters'] ?>
                            </a>
                        </div>

                        <div id="model-list" class="h-100 w-100">
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

                    <!-- ── รายละเอียดรุ่น + register map ── -->
                    <div class="meters-content">
                        <div id="model-config" class="w-100 p-3"
                            style="background:var(--bg-card); color:var(--text-body); min-height: 500px;">
                            <div class="meters-placeholder">
                                <div class="meters-placeholder__icon-wrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="16" rx="2" />
                                        <path d="M3 10h18M9 10v10" />
                                    </svg>
                                </div>
                                <h5><?= $lang['select_model'] ?></h5>
                                <p><?= $lang['select_model_desc'] ?></p>
                            </div>
                        </div>
                    </div>

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
    <?php include "../scripts/scriptjs-meter-models.html"; ?>

</body>

</html>
