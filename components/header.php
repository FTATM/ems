<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

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

.ic--round-log-in {
    display: inline-block;
    width: 1.2em;
    height: 1.2em;
    --svg: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%23000' d='M9 2h9c1.1 0 2 .9 2 2v16c0 1.1-.9 2-2 2H9c-1.1 0-2-.9-2-2v-1a1 1 0 0 1 2 0v1h9V4H9v1a1 1 0 1 1-2 0V4c0-1.1.9-2 2-2'/%3E%3Cpath fill='%23000' d='M10.795 16.295c.39.39 1.02.39 1.41 0l3.588-3.588a1 1 0 0 0 0-1.414l-3.588-3.588a.999.999 0 0 0-1.411 1.411L12.67 11H4a1 1 0 0 0 0 2h8.67l-1.876 1.884a1 1 0 0 0 .001 1.411'/%3E%3C/svg%3E");
    background-color: currentColor;
    -webkit-mask-image: var(--svg);
    mask-image: var(--svg);
    -webkit-mask-repeat: no-repeat;
    mask-repeat: no-repeat;
    -webkit-mask-size: 100% 100%;
    mask-size: 100% 100%;
}

/* Dark mode styles for header */
html.dark .navbar {
    background-color: #18181b !important;
    border-color: #27272a !important;
}

html.dark .navbar-brand div[style*="1a1a1a"] {
    color: #f4f4f5 !important;
}

html.dark .navbar-brand div[style*="6c757d"] {
    color: #a1a1aa !important;
}

html.dark #theme-toggle-btn {
    color: #8BAE66 !important;
}

.username-text {
    color: #1a1a1a;
}

html.dark .username-text {
    color: #f4f4f5 !important;
}
</style>

<header class="fw-medium border-bottom shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-3 py-2">

        <!-- ซ้าย: hamburger + logo -->
        <div class="d-flex align-items-center gap-2">
            <button id="sidebar_open" class="btn p-0 border-0" style="color: #8BAE66;">
                <i class="bi bi-list fs-3"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center gap-2 mb-0" href="../pages/dashboard.php">
                <div class="rounded d-flex align-items-center justify-content-center"
                    style="width:45px; height:45px; background-color:#8BAE66;">
                    <span class="cbi--solar-battery" style="color:white; width:1.8em; height:1.8em;"></span>
                </div>
                <div class="lh-1">
                    <div style="font-size:1.4rem; font-weight:700; color:#1a1a1a; line-height:1.1;">EMS</div>
                    <div style="font-size:0.72rem; color:#6c757d; font-weight:400;">Energy Management Solutions</div>
                </div>
            </a>
        </div>

        <!-- ขวา: dark mode + user + logout -->
        <div class="d-none d-lg-flex align-items-center gap-3 ms-auto">

            <!-- Dark Mode Toggle -->
            <button id="theme-toggle-btn" class="btn p-0 border-0" title="Toggle dark mode" onclick="toggleDarkMode()"
                style="color:#8BAE66; line-height:1;">
                <i class="bi bi-moon-fill fs-5" id="icon-moon"></i>
                <i class="bi bi-sun-fill fs-5" id="icon-sun" style="display:none;"></i>
            </button>

            <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- ยังไม่ได้ Login -->
            <a class="nav-link px-3 py-1 d-flex align-items-center gap-2" href="../pages/login.php"
                style="border: 1.5px solid #8BAE66; border-radius: 20px; color: #8BAE66; font-weight: 600; transition: all 0.2s;"
                onmouseover="this.style.backgroundColor='#8BAE66'; this.style.color='white';"
                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#8BAE66';">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5l-5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z" />
                </svg>
                <?= $lang['login'] ?>
            </a>
            <?php else: ?>
            <!-- Avatar + ชื่อ (คลิกไปหน้า user.php) -->
            <a href="../pages/user.php" class="d-flex align-items-center gap-2 text-decoration-none" title="My Profile">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:36px; height:36px; background-color:#e8eee0; cursor:pointer; transition: background-color 0.2s;"
                    onmouseover="this.style.backgroundColor='#d0dcc0';"
                    onmouseout="this.style.backgroundColor='#e8eee0';">
                    <i class="bi bi-person-fill" style="color:#8BAE66; font-size:1.1rem;"></i>
                </div>
                <span style="font-weight:600; color:#1a1a1a; line-height:1.1;" class="username-text">
                    <?= htmlspecialchars($_SESSION['username'] ?? $_SESSION['name'] ?? 'Admin') ?>
                </span>
            </a>
            <!-- Logout -->
            <a href="../pages/logout.php" class="btn p-0 border-0" title="Logout">
                <i class="bi bi-box-arrow-right fs-4" style="color:#8BAE66;"></i>
            </a>
            <?php endif; ?>

        </div>
    </nav>
</header>

<script>
/* ── Dark Mode Toggle ── */
function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');

    document.getElementById('icon-moon').style.display = isDark ? 'none' : 'inline';
    document.getElementById('icon-sun').style.display = isDark ? 'inline' : 'none';

    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

/* โหลด preference จาก localStorage เมื่อเปิดหน้า */
(function() {
    const saved = localStorage.getItem('theme');
    if (saved === 'dark') {
        document.documentElement.classList.add('dark');
        const moon = document.getElementById('icon-moon');
        const sun = document.getElementById('icon-sun');
        if (moon) moon.style.display = 'none';
        if (sun) sun.style.display = 'inline';
    }
})();

/* ── เปิด Sidebar เมื่อกดปุ่ม hamburger ── */
document.addEventListener('DOMContentLoaded', function() {
    const openBtn = document.getElementById('sidebar_open');
    if (openBtn) {
        openBtn.addEventListener('click', function() {
            if (typeof window.smOpen === 'function') {
                window.smOpen();
            }
        });
    }
});
</script>