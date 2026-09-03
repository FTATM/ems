<?php
include '../components/session.php';
checkLogin();

$initialStep = (isset($_GET['step']) && $_GET['step'] == '2' && isset($_SESSION['lid'])) ? 2 : 1;
?>
<!DOCTYPE html>
<html lang="<?= $langCode ?>">
<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['chooselocate'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/locations.css">
    <script>
    const LANG = <?= json_encode($lang) ?>;
    const INITIAL_STEP = <?= $initialStep ?>;
    const SESSION_LID = "<?= (int)($_SESSION['lid'] ?? 0) ?>";
    </script>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>;">
    <div id="main" class="d-flex flex-column">

        <?php include "../components/header.php"; ?>

        <main class="loc-main">

            <!-- ── Stepper bar ── -->
            <div class="step-bar" id="step-bar">
                <div class="step-node is-active" data-step="1">
                    <span class="step-dot">1</span>
                    <span class="step-label"><?= $lang['step_location'] ?></span>
                </div>
                <div class="step-line"></div>
                <div class="step-node" data-step="2">
                    <span class="step-dot">2</span>
                    <span class="step-label"><?= $lang['step_group'] ?></span>
                </div>
            </div>

            <!-- ── Step 1 : เลือกสถานที่ ── -->
            <section class="step-pane is-active" id="pane-1">
                <div class="loc-hero">
                    <h2><?= $lang['chooselocate'] ?></h2>
                    <p><?= $lang['select_project_for_energy'] ?></p>
                </div>

                <div class="loc-toolbar">
                    <div class="loc-search">
                        <input type="text" id="location-search" placeholder="<?= $lang['search_location'] ?>"
                            oninput="filterLocationCards()">
                    </div>
                </div>

                <div class="loc-list-wrap">
                    <div class="loc-list" id="location-list">
                        <div class="loc-card loc-card--skeleton">
                            <div class="loc-card__icon"></div>
                            <span class="loc-card__name"></span>
                        </div>
                        <div class="loc-card loc-card--skeleton">
                            <div class="loc-card__icon"></div>
                            <span class="loc-card__name"></span>
                        </div>
                        <div class="loc-card loc-card--skeleton">
                            <div class="loc-card__icon"></div>
                            <span class="loc-card__name"></span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── Step 2 : เลือกกลุ่ม ── -->
            <section class="step-pane" id="pane-2">
                <div class="step-breadcrumb">
                    <button type="button" class="step-back" onclick="goToStep(1)">
                        <i class="bi bi-chevron-left"></i>
                        <span><?= $lang['back'] ?></span>
                    </button>
                    <span class="step-breadcrumb__sep">/</span>
                    <span class="step-breadcrumb__current">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span id="selected-location-name">—</span>
                    </span>
                </div>

                <div class="loc-hero loc-hero--sm">
                    <h2><?= $lang['choosegroup'] ?></h2>
                    <p><?= $lang['select_group_for_energy'] ?></p>
                </div>

                <div class="loc-toolbar">
                    <div class="loc-search">
                        <input type="text" id="group-search" placeholder="<?= $lang['search_group'] ?>"
                            oninput="filterGroupCards()">
                    </div>
                </div>

                <div class="loc-list-wrap">
                    <div class="loc-list" id="groups-list">
                        <div class="group-card group-card--skeleton" aria-hidden="true">
                            <div class="group-card__icon"></div>
                            <div class="group-card__info">
                                <span class="group-card__name">&nbsp;</span>
                                <span class="group-card__location">&nbsp;</span>
                            </div>
                        </div>
                        <div class="group-card group-card--skeleton" aria-hidden="true">
                            <div class="group-card__icon"></div>
                            <div class="group-card__info">
                                <span class="group-card__name">&nbsp;</span>
                                <span class="group-card__location">&nbsp;</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <?php include "../components/footer.php"; ?>

    </div><!-- /#main -->

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-locations.html"; ?>
</body>

</html>
