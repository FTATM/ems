<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<link rel="stylesheet" href="../styles/sidemenu.css">

<!-- ===== OVERLAY (backdrop blur) ===== -->
<div class="sm-overlay" id="smOverlay"></div>

<!-- ===== SIDEBAR ===== -->
<nav class="sm-sidebar" id="smSidebar">

    <!-- Header -->
    <div class="sm-header">
        <div class="sm-header-btns">
            <button class="sm-pin" id="smPinBtn" type="button" aria-pressed="false" title="ปักหมุดเมนู">
                <i class="bi bi-pin-angle"></i>
            </button>
            <button class="sm-close" id="smCloseBtn" title="ปิดเมนู">✕</button>
        </div>
    </div>

    <!-- Nav List -->
    <ul class="sm-nav">

        <div class="sm-section">
            <?= $lang['homepage'] ?>
        </div>

        <li>
            <a href="../pages/diagram.php" class="sm-link <?= $current_page === 'diagram' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-diagram-3"></i></span>
                <span class="sm-label"><?= $lang['diagram'] ?></span>
            </a>
        </li>
        <li>
            <a href="../pages/gauge.php" class="sm-link <?= $current_page === 'gauge' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-speedometer2"></i></span>
                <span class="sm-label"><?= $lang['gauge'] ?></span>
            </a>
        </li>
        <li>
            <a href="../pages/phasor.php" class="sm-link <?= $current_page === 'phasor' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-activity"></i></span>
                <span class="sm-label"><?= $lang['gvoltage'] ?></span>
            </a>
        </li>
        <li>
            <a href="../pages/dashboard.php" class="sm-link <?= $current_page === 'dashboard' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-graph-up-arrow"></i></span>
                <span class="sm-label"><?= $lang['egraph'] ?></span>
            </a>
        </li>
        <li>
            <a href="../pages/allmeter.php" class="sm-link <?= $current_page === 'allmeter' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-grid-3x3-gap"></i></span>
                <span class="sm-label"><?= $lang['allmeter'] ?></span>
            </a>
        </li>

        <hr class="sm-divider">
        <div class="sm-section">
            <?= $lang['report'] ?>
        </div>

        <li>
            <a href="../pages/report-meter.php" class="sm-link <?= $current_page === 'report-meter' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-file-earmark-bar-graph"></i></span>
                <span class="sm-label"><?= $lang['reporttotal'] ?></span>
            </a>
        </li>
        <li>
            <a href="../pages/report-meter-detail.php"
                class="sm-link <?= $current_page === 'report-meter-detail' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-file-earmark-text"></i></span>
                <span class="sm-label"><?= $lang['reportdetail'] ?></span>
            </a>
        </li>
        <li>
            <a href="../pages/report-electric.php"
                class="sm-link <?= $current_page === 'report-electric' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-lightning"></i></span>
                <span class="sm-label"><?= $lang['reportelec'] ?></span>
            </a>
        </li>
        <li>
            <a href="../pages/map.php" class="sm-link <?= $current_page === 'map' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-map"></i></span>
                <span class="sm-label"><?= $lang['overview'] ?></span>
            </a>
        </li>

        <hr class="sm-divider">
        <div class="sm-section">
            <?= $lang['settings'] ?>
        </div>

        <li class="sm-has-sub">
            <a class="sm-link" id="smSystemToggle" onclick="smToggleSub()" aria-expanded="false">
                <span class="sm-icon"><i class="bi bi-gear"></i></span>
                <span class="sm-label"><?= $lang['system'] ?></span>
                <i class="bi bi-chevron-right sm-arrow" id="smArrow"></i>
            </a>
            <ul class="sm-subnav" id="systemSubNav">
                <li>
                    <a href="../pages/management-locations.php"
                        class="sm-link <?= $current_page === 'management-locations' ? 'active' : '' ?>">
                        <span class="sm-icon"><i class="bi bi-geo-alt"></i></span>
                        <span class="sm-label"><?= $lang['locationmnm'] ?></span>
                    </a>
                </li>
                <li>
                    <a href="../pages/management-groups.php"
                        class="sm-link <?= $current_page === 'management-groups' ? 'active' : '' ?>">
                        <span class="sm-icon"><i class="bi bi-collection"></i></span>
                        <span class="sm-label"><?= $lang['groupmnm'] ?></span>
                    </a>
                </li>
                <li>
                    <a href="../pages/management-meters.php"
                        class="sm-link <?= $current_page === 'management-meters' ? 'active' : '' ?>">
                        <span class="sm-icon"><i class="bi bi-cpu"></i></span>
                        <span class="sm-label"><?= $lang['metermnm'] ?></span>
                    </a>
                </li>
                <li>
                    <a href="../pages/management-users.php"
                        class="sm-link <?= $current_page === 'management-users' ? 'active' : '' ?>">
                        <span class="sm-icon"><i class="bi bi-people"></i></span>
                        <span class="sm-label"><?= $lang['usermnm'] ?></span>
                    </a>
                </li>
                <li>
                    <a href="../pages/mndidb.php" class="sm-link <?= $current_page === 'mndidb' ? 'active' : '' ?>">
                        <span class="sm-icon"><i class="bi bi-database"></i></span>
                        <span class="sm-label">MNDIDB</span>
                    </a>
                </li>
            </ul>
        </li>

    </ul>

    <!-- Footer: Language -->
    <div class="sm-footer">
        <div class="sm-lang-label"><?= $lang['chooselang'] ?></div>
        <div class="sm-lang-row">
            <a href="<?= buildLangSwitchLink('th') ?>" class="sm-lang-btn <?= $langCode == 'th' ? 'active' : '' ?>">
                ไทย
            </a>
            <a href="<?= buildLangSwitchLink('en') ?>" class="sm-lang-btn <?= $langCode == 'en' ? 'active' : '' ?>">
                English
            </a>
        </div>
    </div>

</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('smSidebar');
    const overlay = document.getElementById('smOverlay');
    const closeBtn = document.getElementById('smCloseBtn');

    function smOpen() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.classList.add('sm-body-lock');
    }

    function smClose() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('sm-body-lock');
    }

    /* bind ปุ่มเปิด — รองรับทั้ง #sidebar_open และ #hamburgerBtn */
    ['sidebar_open', 'hamburgerBtn'].forEach(function(id) {
        const btn = document.getElementById(id);
        if (btn) btn.addEventListener('click', smOpen);
    });

    closeBtn.addEventListener('click', smClose);
    overlay.addEventListener('click', smClose);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') smClose();
    });

    /* ป้องกัน click ใน sidebar ปิดตัวเอง */
    sidebar.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    /* ปิดเมื่อคลิก nav link */
    document.querySelectorAll('.sm-link[href]').forEach(function(a) {
        a.addEventListener('click', smClose);
    });

    /* expose ให้เรียกจากภายนอกได้ */
    window.smOpen = smOpen;
    window.smClose = smClose;

    /* ── Submenu ── */
    window.smToggleSub = function() {
        const sub = document.getElementById('systemSubNav');
        const arrow = document.getElementById('smArrow');
        const btn = document.getElementById('smSystemToggle');
        const open = sub.classList.contains('open');
        sub.classList.toggle('open', !open);
        arrow.style.transform = open ? 'rotate(0deg)' : 'rotate(90deg)';
        btn.setAttribute('aria-expanded', String(!open));
    };

    /* ── Mini-rail: dock / expand / pin (desktop >=1200px) ── */
    var mqDock = window.matchMedia('(min-width:1200px)');
    var PIN_KEY = 'ems.sidebarPinned';
    var pinned = false;
    try { pinned = localStorage.getItem(PIN_KEY) === '1'; } catch (e) {}

    function applyPin(p) {
        document.body.classList.toggle('sm-rail-pinned', !!p && mqDock.matches);
        var b = document.getElementById('smPinBtn');
        if (b) b.setAttribute('aria-pressed', String(!!p));
    }
    applyPin(pinned);

    var pinBtn = document.getElementById('smPinBtn');
    if (pinBtn) pinBtn.addEventListener('click', function () {
        pinned = !pinned;
        try { localStorage.setItem(PIN_KEY, pinned ? '1' : '0'); } catch (e) {}
        applyPin(pinned);
    });

    function onDockChange() {
        applyPin(pinned);
        if (mqDock.matches) smClose();   /* drop any drawer state when docking */
    }
    if (mqDock.addEventListener) mqDock.addEventListener('change', onDockChange);
    else if (mqDock.addListener) mqDock.addListener(onDockChange);

    /* opening the drawer is a no-op while the rail is docked */
    var _smOpen = smOpen;
    window.smOpen = function () { if (mqDock.matches) return; _smOpen(); };

    /* ── Dark mode: sync localStorage ── */
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark');
        const moon = document.getElementById('icon-moon');
        const sun = document.getElementById('icon-sun');
        if (moon) moon.style.display = 'none';
        if (sun) sun.style.display = 'inline';
    }
});
</script>