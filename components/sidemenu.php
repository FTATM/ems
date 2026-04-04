<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<link rel="stylesheet" href="../styles/sidemenu.css">

<style>
.cbi--solar-battery {
    display: inline-block;
    width: 1.2em;
    height: 1.2em;
    --svg: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%23000' d='M17.757 11.771h3.257v.904h-3.257zm0 1.149h3.257v.904h-3.257zm0 1.121h3.257v.904h-3.257zm0 1.12h3.257v.904h-3.257zm.008 1.39a.635.635 0 0 0 .635.635h2a.636.636 0 0 0 .635-.635v-.269h-3.27zm3.287-9.609h-.122v-.337a.154.154 0 0 0-.153-.153h-2.783a.154.154 0 0 0-.153.153v.337h-.091a.947.947 0 0 0-.948.947v9.153c-1.238-1.843-1.4-1.754-1.517-1.694s-.091.186-.064.418a5.4 5.4 0 0 1 .041.907c-.025.539-.045 1.005-.322 1.19a1.08 1.08 0 0 1-.851 0a1.8 1.8 0 0 1-.549-.295l.032.021l2.771-5.284l-.076-.219l-3.083 5.9H2.08l.117.256h11.035l.237-.451a2.5 2.5 0 0 0 .569.257a1.7 1.7 0 0 0 .658.1a.74.74 0 0 0 .345-.117c.355-.238.378-.748.4-1.339a5.4 5.4 0 0 0-.042-.937a2 2 0 0 1-.018-.216a9.5 9.5 0 0 1 1.457 1.923a.944.944 0 0 0 .908.691h3.3A.947.947 0 0 0 22 17.2V7.889a.947.947 0 0 0-.948-.947m.168 9.721a.726.726 0 0 1-.726.726h-2.217a.726.726 0 0 1-.726-.726V8.32a.726.726 0 0 1 .726-.726h2.217a.726.726 0 0 1 .726.726ZM11.153 9.356a.06.06 0 0 0 .077-.035l.415-1.14a.06.06 0 0 0-.036-.077a.06.06 0 0 0-.076.036l-.415 1.14a.06.06 0 0 0 .035.076m.835.428l.779-.929a.06.06 0 0 0-.007-.085a.06.06 0 0 0-.084.008l-.78.929a.06.06 0 0 0 .008.084a.06.06 0 0 0 .084-.007m.472.672a.06.06 0 0 0 .081.022l1.051-.606a.06.06 0 0 0 .021-.082a.06.06 0 0 0-.081-.022l-1.05.606a.06.06 0 0 0-.022.082m.295.815a.06.06 0 0 0 .069.049l1.2-.211a.06.06 0 0 0 .048-.069a.06.06 0 0 0-.072-.049l-1.2.209a.06.06 0 0 0-.045.071m-6.18-.171l1.194.211a.06.06 0 0 0 .021-.118l-1.2-.21a.06.06 0 1 0-.02.117Zm1.479-.629a.06.06 0 0 0 .082-.022a.06.06 0 0 0-.022-.081l-1.05-.607a.06.06 0 1 0-.06.1zm.556-.693a.06.06 0 0 0 .084.008A.06.06 0 0 0 8.7 9.7l-.78-.929a.06.06 0 1 0-.091.076Zm.836-.424a.06.06 0 0 0 .035-.077l-.414-1.14a.06.06 0 0 0-.113.041l.415 1.14a.06.06 0 0 0 .077.036M10.3 9.2a.06.06 0 0 0 .06-.059V7.932a.06.06 0 1 0-.12 0v1.213a.06.06 0 0 0 .06.055m-3.486 2.476a.04.04 0 0 0-.033.014a.04.04 0 0 0-.014.034a.047.047 0 0 0 .047.047h6.963a.05.05 0 0 0 .034-.014a.04.04 0 0 0 .014-.033a.047.047 0 0 0-.048-.048h-1.285a2.2 2.2 0 0 0-4.393 0zm9.406.25H5.019L2 17.85h11.122zm-.643.441l-1.224 2.341h-.221l1.222-2.341zm-.336 0l-1.222 2.341h-.238L15 12.367Zm-.352 0l-1.221 2.341h-.221l1.22-2.341zm-.335 0l-1.22 2.341H13.1l1.22-2.341zm-.346 0l-1.219 2.341h-.222l1.219-2.341zm-.335 0l-1.219 2.341h-.238l1.218-2.341zm-.352 0L12.3 14.708h-.221l1.221-2.341Zm-.6 0l-1.216 2.341h-.221l1.216-2.341Zm-.335 0l-1.215 2.341h-.238l1.214-2.341zm-.352 0l-1.214 2.341h-.22l1.214-2.341zm-.335 0l-1.213 2.341h-.233l1.212-2.341zm-.347 0l-1.212 2.341h-.221l1.211-2.341zm-.335 0l-1.211 2.341h-.238l1.21-2.341zm-.352 0l-1.21 2.341h-.221l1.209-2.341zm-.631 0l-1.209 2.341H8.8l1.207-2.341zm-.336 0l-1.207 2.341h-.238l1.206-2.341zm-.352 0L8.34 14.708h-.222l1.206-2.341zm-.335 0l-1.2 2.341h-.238l1.205-2.341zm-.346 0l-1.2 2.341h-.226l1.2-2.341zm-.335 0l-1.2 2.341h-.242l1.2-2.341zm-.352 0l-1.2 2.341h-.224l1.2-2.341zm-.632 0l-1.2 2.341h-.222l1.2-2.341zm-.335 0l-1.2 2.341h-.238l1.2-2.341zm-.352 0l-1.2 2.341h-.22l1.2-2.341zm-.335 0l-1.2 2.341h-.231l1.2-2.341zm-.347 0l-1.2 2.341h-.218l1.2-2.341zm-.335 0l-1.2 2.341h-.234L5.6 12.367Zm-.574 0h.222L4.3 14.708h-.226Zm-2.584 5.065l1.195-2.341H4.1l-1.195 2.341zm.333 0l1.2-2.341h.238l-1.2 2.341zm.35 0l1.2-2.341h.221l-1.2 2.341zm.333 0l1.2-2.341h.23l-1.2 2.341zm.345 0l1.2-2.341h.221l-1.2 2.341zm.333 0l1.2-2.341h.238l-1.2 2.341zm.349 0l1.2-2.341h.221l-1.2 2.341zm.629 0l1.2-2.341h.222l-1.2 2.341zm.333 0l1.2-2.341h.241l-1.2 2.341zm.349 0l1.2-2.341h.222l-1.2 2.341zm.334 0l1.2-2.341h.233L6.6 17.432Zm.344 0l1.206-2.341h.221l-1.206 2.341zm.333 0l1.207-2.341h.238l-1.208 2.341zm.35 0l1.207-2.341h.222l-1.209 2.341zm.628 0l1.209-2.341h.222l-1.211 2.341zm.333 0l1.21-2.341h.238L8.6 17.432Zm.35 0l1.211-2.341h.221L8.93 17.432Zm.333 0l1.212-2.341h.233l-1.213 2.341zm.344 0l1.213-2.341h.222l-1.214 2.341zm.333 0l1.214-2.341h.238l-1.214 2.341zm.35 0l1.215-2.341h.221l-1.215 2.341zm.6 0l1.217-2.341h.213l-1.217 2.341zm.333 0l1.218-2.341h.238l-1.218 2.341zm.35 0l1.219-2.341h.221l-1.223 2.341zm.333 0l1.22-2.341h.233l-1.221 2.341zm.344 0l1.221-2.341h.222l-1.222 2.341zm.333 0l1.222-2.341h.238L12.6 17.432Zm.35 0l1.223-2.341h.221l-1.227 2.341z'/%3E%3C/svg%3E");
    background-color: currentColor;
    -webkit-mask-image: var(--svg);
    mask-image: var(--svg);
    -webkit-mask-repeat: no-repeat;
    mask-repeat: no-repeat;
    -webkit-mask-size: 100% 100%;
    mask-size: 100% 100%;
}
</style>

<!-- ===== OVERLAY (backdrop blur) ===== -->
<div class="sm-overlay" id="smOverlay"></div>

<!-- ===== SIDEBAR ===== -->
<nav class="sm-sidebar" id="smSidebar">

    <!-- Header -->
    <div class="sm-header">
        <div class="sm-logo-wrap">
            <div class="sm-logo-icon" style="width:45px; height:45px; background-color:#ffffff;">
                <span class="cbi--solar-battery" style="width:1.8em; height:1.8em;"></span>
            </div>
            <div>
                <div class="sm-logo-text">EMS</div>
                <div class="sm-logo-sub">Energy Management Solutions</div>
            </div>
        </div>
        <button class="sm-close" id="smCloseBtn" title="ปิดเมนู">✕</button>
    </div>

    <!-- Nav List -->
    <ul class="sm-nav">

        <div class="sm-section">
            <?= $lang['homepage'] ?>
        </div>

        <li>
            <a href="../pages/diagram.php" class="sm-link <?= $current_page === 'diagram' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-diagram-3"></i></span>
                <?= $lang['diagram'] ?>
            </a>
        </li>
        <li>
            <a href="../pages/gauge.php" class="sm-link <?= $current_page === 'gauge' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-speedometer2"></i></span>
                <?= $lang['gauge'] ?>
            </a>
        </li>
        <li>
            <a href="../pages/phasor.php" class="sm-link <?= $current_page === 'phasor' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-activity"></i></span>
                <?= $lang['gvoltage'] ?> x
            </a>
        </li>
        <li>
            <a href="../pages/dashboard.php" class="sm-link <?= $current_page === 'dashboard' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-graph-up-arrow"></i></span>
                <?= $lang['egraph'] ?>
            </a>
        </li>
        <li>
            <a href="../pages/allmeter.php" class="sm-link <?= $current_page === 'allmeter' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-grid-3x3-gap"></i></span>
                <?= $lang['allmeter'] ?> x
            </a>
        </li>

        <hr class="sm-divider">
        <div class="sm-section">
            <?= $lang['report'] ?>
        </div>

        <li>
            <a href="../pages/report-meter.php" class="sm-link <?= $current_page === 'report-meter' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-file-earmark-bar-graph"></i></span>
                <?= $lang['reporttotal'] ?> x
            </a>
        </li>
        <li>
            <a href="../pages/report-meter-detail.php"
                class="sm-link <?= $current_page === 'report-meter-detail' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-file-earmark-text"></i></span>
                <?= $lang['reportdetail'] ?> x
            </a>
        </li>
        <li>
            <a href="../pages/report-electric.php"
                class="sm-link <?= $current_page === 'report-electric' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-lightning"></i></span>
                <?= $lang['reportelec'] ?> x
            </a>
        </li>
        <li>
            <a href="../pages/map.php" class="sm-link <?= $current_page === 'map' ? 'active' : '' ?>">
                <span class="sm-icon"><i class="bi bi-map"></i></span>
                <?= $lang['overview'] ?> x
            </a>
        </li>

        <hr class="sm-divider">
        <div class="sm-section">
            <?= $lang['settings'] ?>
        </div>

        <li class="sm-has-sub">
            <a class="sm-link" id="smSystemToggle" onclick="smToggleSub()" aria-expanded="false">
                <span class="sm-icon"><i class="bi bi-gear"></i></span>
                <?= $lang['system'] ?>
                <i class="bi bi-chevron-right sm-arrow" id="smArrow"></i>
            </a>
            <ul class="sm-subnav" id="systemSubNav">
                <li>
                    <a href="../pages/management-locations.php"
                        class="sm-link <?= $current_page === 'management-locations' ? 'active' : '' ?>">
                        <span class="sm-icon"><i class="bi bi-geo-alt"></i></span>
                        <?= $lang['locationmnm'] ?>
                    </a>
                </li>
                <li>
                    <a href="../pages/management-groups.php"
                        class="sm-link <?= $current_page === 'management-groups' ? 'active' : '' ?>">
                        <span class="sm-icon"><i class="bi bi-collection"></i></span>
                        <?= $lang['groupmnm'] ?>
                    </a>
                </li>
                <li>
                    <a href="../pages/management-meters.php"
                        class="sm-link <?= $current_page === 'management-meters' ? 'active' : '' ?>">
                        <span class="sm-icon"><i class="bi bi-cpu"></i></span>
                        <?= $lang['metermnm'] ?>
                    </a>
                </li>
                <li>
                    <a href="../pages/management-users.php"
                        class="sm-link <?= $current_page === 'management-users' ? 'active' : '' ?>">
                        <span class="sm-icon"><i class="bi bi-people"></i></span>
                        <?= $lang['usermnm'] ?>
                    </a>
                </li>
                <li>
                    <a href="../pages/mndidb.php" class="sm-link <?= $current_page === 'mndidb' ? 'active' : '' ?>">
                        <span class="sm-icon"><i class="bi bi-database"></i></span>
                        MNDIDB
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