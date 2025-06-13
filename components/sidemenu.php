<div class="sidebar collapsed " id="sidebar" style="border-right: 1px solid #aaa; transition: width 0.3s ease, padding 0.3s ease; background-color: <?= $bgsec ?>;">
    <ul class="navbar-nav gap-2">
        <div class="">
            <img src="../assets/images/logo.png" width="100%">
        </div>
        <li class="d-flex align-items-center gap-2">
            <i class="bi bi-house"></i>
            <span><a class="nav-link active text" href="../pages/chat.php"><?= $lang['dashboard'] ?></a></span>
        </li>
        <li class="d-flex align-items-center gap-2">
            <i class="bi bi-journal-check"></i>
            <span><a class="nav-link active text" href="../pages/mycourse.php"><?= $lang['bills'] ?></a></span>
        </li>
        <li class="d-flex align-items-center gap-2">
            <i class="bi bi-file-person"></i>
            <span><a class="nav-link active text" href="../pages/results.php"><?= $lang['payment'] ?></a></span>
        </li>
        <!-- <li class="gap-2">
            <button class="btn btn-link nav-link text px-0" data-bs-toggle="collapse" data-bs-target="#systemSubNav" aria-expanded="false">
                <i class="bi bi-gear"></i> <?= $lang['system'] ?>
            </button>
            <ul class="collapse nav flex-column " id="systemSubNav">
                <li class="d-flex align-items-start gap-2">
                    <i class="bi bi-info-square"></i>
                    <a class="nav-secondary text" href="../pages/infomation.php"><?= $lang['information'] ?><?= $lang['system'] ?></a>
                </li>
                <li class="d-flex align-items-start gap-2">
                    <i class="bi bi-bug"></i>
                    <a class="nav-secondary text" href="../pages/log.php"><?= $lang['logs'] ?></a>
                </li>
                <li class="d-flex align-items-start gap-2">
                    <i class="bi bi-database-fill-down"></i>
                    <a class="nav-secondary text" href="../pages/backup.php"><?= $lang['backup'] ?></a>
                </li>
            </ul>
        </li> -->


        <div class="text-center mt-5"><?= $lang['chooselang'] ?></div>
        <!-- วางใน Navbar หรือเมนู -->
        <div class="d-flex justify-content-center p-2">
            <a href="<?= buildLangSwitchLink('th') ?>" class="btn btn-sm <?= $langCode == 'th' ? 'active' : '' ?>" style="background-color: <?= $btnColor ?>; color: <?= $text ?>;">ไทย</a>
            <a href="<?= buildLangSwitchLink('en') ?>" class="btn btn-sm <?= $langCode == 'en' ? 'active' : '' ?>" style="background-color: <?= $btnColor ?>; color: <?= $text ?>;">English</a>
        </div>
        <div class="text-center mt-5"><?= $lang['choosetheme'] ?></div>
        <!-- วางใน Navbar หรือเมนู -->
        <div class="d-flex justify-content-center p-2">
            <a href="<?= buildthemeSwitchLink('light') ?>" class="btn btn-sm <?= $theme == 'light' ? 'active' : '' ?>" style="background-color: <?= $btnColor ?>; color: <?= $text ?>;"><?= $lang['light'] ?></a>
            <a href="<?= buildthemeSwitchLink('dark') ?>" class="btn btn-sm <?= $theme == 'dark' ? 'active' : '' ?>" style="background-color: <?= $btnColor ?>; color: <?= $text ?>;"><?= $lang['dark'] ?></a>
        </div>
    </ul>
</div>