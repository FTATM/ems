<div class="sidebar collapsed" id="sidebar" style="border-right: 1px solid #aaa; transition: width 0.3s ease, padding 0.3s ease; background-color: <?= $bgsec ?>;">
    <ul class="navbar-nav gap-2">
        <div class="d-flex position-relative p-3">
            <img src="../assets/images/logo_FT_white.png" width="100%">
            <button id="sidebar_close"
                class="btn px-1 text-white position-absolute rounded-circle border"
                style="padding: 0; top:0px; right:-25px; background-color: <?= $bg; ?>;">
                <i class="bi bi-arrow-left w-100 h-100"></i>
            </button>
        </div>
        <li>
            <a href="../pages/dashboard.php" class="d-flex align-items-center gap-2 text-menu">
                <i class="bi bi-receipt"></i>
                <span><?= $lang['dashboard'] ?></span>
            </a>
        </li>
        <li>
            <a href="../pages/allmeter.php" class="d-flex align-items-center gap-2 text-menu">
                <i class="bi bi-speedometer"></i>
                <span><?= $lang['allmeter'] ?></span>
            </a>
        </li>
        <li>
            <a href="../pages/gauge.php" class="d-flex align-items-center gap-2 text-menu">
                <i class="bi bi-speedometer"></i>
                <span><?= $lang['gauge'] ?></span>
            </a>
        </li>

        <li>
            <a href="../pages/map.php" class="d-flex align-items-center gap-2 text-menu">
                <i class="bi bi-house"></i>
                <span><?= $lang['overview'] ?></span>
            </a>
        </li>

        <li>
            <a href="../pages/phasor.php" class="d-flex align-items-center gap-2 text-menu">
                <i class="bi bi-speedometer"></i>
                <span><?= $lang['phasor'] ?></span>
            </a>
        </li>

        <li>
            <a href="../pages/diagram.php" class="d-flex align-items-center gap-2 text-menu">
                <i class="bi bi-house"></i>
                <span><?= $lang['diagram'] ?></span>
            </a>
        </li>

        <li>
            <a href="../pages/report-meter.php" class="d-flex align-items-center gap-2 text-menu">
                <i class="bi bi-file-person"></i>
                <span><?= $lang['reportsummary'] ?></span>
            </a>
        </li>

        <li>
            <a href="../pages/report-meter-detail.php" class="d-flex align-items-center gap-2 text-menu">
                <i class="bi bi-file-person"></i>
                <span><?= $lang['reportMeterDetail'] ?></span>
            </a>
        </li>

        <li>
            <a href="../pages/report-electric.php" class="d-flex align-items-center gap-2 text-menu">
                <i class="bi bi-receipt"></i>
                <span><?= $lang['reportelec'] ?></span>
            </a>
        </li>


        <li class="gap-2">
            <a class="d-flex align-items-center gap-2 text-menu" data-bs-toggle="collapse" data-bs-target="#systemSubNav" aria-expanded="false">
                <i class="bi bi-gear"></i>
                <span><?= $lang['system'] ?></span>
            </a>
            <ul class="collapse nav flex-column gap-2" id="systemSubNav">
                <li>
                    <a href="../pages/management-locations.php" class="d-flex align-items-center gap-2 text-menu">
                        <i class="bi bi-house"></i>
                        <span><?= $lang['locationmnm'] ?></span>
                    </a>
                </li>

                <li>
                    <a href="../pages/management-groups.php" class="d-flex align-items-center gap-2 text-menu">
                        <i class="bi bi-journal-check"></i>
                        <span><?= $lang['groupmnm'] ?></span>
                    </a>
                </li>

                <li>
                    <a href="../pages/management-meters.php" class="d-flex align-items-center gap-2 text-menu">
                        <i class="bi bi-setting-meter"></i>
                        <span><?= $lang['metermnm'] ?></span>
                    </a>
                </li>

                <li>
                    <a href="../pages/management-users.php" class="d-flex align-items-center gap-2 text-menu">
                        <i class="bi bi-people"></i>
                        <span><?= $lang['usermnm'] ?></span>
                    </a>
                </li>

                <li>
                    <a href="../pages/mndidb.php" class="d-flex align-items-center gap-2 text-menu">
                        <i class="bi bi-people"></i>
                        <span>MNDIDB</span>
                    </a>
                </li>
            </ul>
        </li>


        <div class="text-center mt-5"><?= $lang['chooselang'] ?></div>
        <!-- วางใน Navbar หรือเมนู -->
        <div class="d-flex justify-content-center p-2">
            <a href="<?= buildLangSwitchLink('th') ?>" class="btn btn-sm <?= $langCode == 'th' ? 'active' : '' ?>" style="background-color: <?= $btnColor ?>; color: <?= $text ?>;">ไทย</a>
            <a href="<?= buildLangSwitchLink('en') ?>" class="btn btn-sm <?= $langCode == 'en' ? 'active' : '' ?>" style="background-color: <?= $btnColor ?>; color: <?= $text ?>;">English</a>
        </div>
        <!-- <div class="text-center mt-5"><?= $lang['choosetheme'] ?></div>
        <div class="d-flex justify-content-center p-2">
            <a href="<?= buildthemeSwitchLink('light') ?>" class="btn btn-sm <?= $theme == 'light' ? 'active' : '' ?>" style="background-color: <?= $btnColor ?>; color: <?= $text ?>;"><?= $lang['light'] ?></a>
            <a href="<?= buildthemeSwitchLink('dark') ?>" class="btn btn-sm <?= $theme == 'dark' ? 'active' : '' ?>" style="background-color: <?= $btnColor ?>; color: <?= $text ?>;"><?= $lang['dark'] ?></a>
        </div> -->
    </ul>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>