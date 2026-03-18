<?php
include '../components/session.php';
check();
if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/locations.php");
}
?>
<!DOCTYPE html>
<html lang="<?= $langCode ?>">
<?php include "../scripts/ref.html"; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['login'] ?> - EMS</title>
    <!-- Google Fonts & Material Symbols -->
    <link
        href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&family=Prompt:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />
    <!-- Separated CSS -->
    <link rel="stylesheet" href="../styles/login.css">
</head>

<body>

    <div class="login-wrapper">

        <!-- ===== LEFT PANEL ===== -->
        <div class="left-panel">
            <div class="left-inner">
                <div class="glass-card">
                    <div class="logo-wrap">
                        <div class="cbi--solar-battery"></div>
                    </div>
                    <h1>ระบบจัดการพลังงานอัจฉริยะ</h1>
                    <h2>Energy Management System</h2>
                    <div class="divider-line"></div>
                    <p><?= $lang['system_tagline'] ?></p>
                </div>

                <div class="icon-grid">
                    <div class="icon-item">
                        <div class="icon-box">
                            <span class="material-symbols-outlined material-symbols--monitoring-rounded"></span>
                        </div>
                        <span class="icon-label"><?= $lang['realtime'] ?></span>
                    </div>
                    <div class="icon-item">
                        <div class="icon-box">
                            <span class="material-symbols-outlined material-symbols--solar-power-rounded"></span>
                        </div>
                        <span class="icon-label"><?= $lang['clean_energy'] ?></span>
                    </div>
                    <div class="icon-item">
                        <div class="icon-box">
                            <span class="material-symbols-outlined material-symbols--energy-program-saving"></span>
                        </div>
                        <span class="icon-label"><?= $lang['energy_saving'] ?></span>
                    </div>
                    <div class="icon-item">
                        <div class="icon-box">
                            <span class="material-symbols-outlined material-symbols--analytics"></span>
                        </div>
                        <span class="icon-label"><?= $lang['analysis'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== RIGHT PANEL ===== -->
        <div class="right-panel">

            <!-- Dark mode toggle -->
            <button class="theme-toggle" onclick="toggleDarkMode()" title="Toggle dark mode">
                <span class="material-symbols-outlined" id="icon-moon">dark_mode</span>
                <span class="material-symbols-outlined" id="icon-sun" style="display:none;">light_mode</span>
            </button>

            <div class="form-container">
                <div class="form-heading">
                    <h2><?= $lang['login'] ?></h2>
                    <p><?= $lang['login_notice'] ?></p>
                    <div class="accent-bar"></div>
                </div>

                <?php if (!empty($error)) echo "<p style='color:red; margin-bottom:1rem;'>$error</p>"; ?>

                <form onsubmit="login(); return false;">

                    <!-- Username -->
                    <div class="field-group">
                        <label for="username"><?= $lang['username'] ?></label>
                        <div class="input-wrapper">
                            <span class="input-icon material-symbols-outlined mdi--user"></span>
                            <input id="username" type="text" name="username" required
                                placeholder="<?= $lang['husername'] ?>">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="field-group">
                        <label for="password"><?= $lang['password'] ?></label>
                        <div class="input-wrapper">
                            <span class="input-icon material-symbols-outlined mdi--password"></span>
                            <input id="password" type="password" name="password" required
                                placeholder="<?= $lang['hpassword'] ?>">
                        </div>
                    </div>

                    <!-- Submit -->
                    <button class="btn-login" type="submit">
                        <?= $lang['login'] ?>
                        <span class="material-symbols-outlined material-symbols--login-rounded"></span>
                    </button>

                </form>
            </div>

            <p class="footer-note">© 2024 Smart Energy Management Solutions. All Rights Reserved.</p>
        </div>

    </div>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-login.html"; ?>

    <script>
    /* ── Dark Mode Toggle (login page) ── */
    function toggleDarkMode() {
        const isDark = document.body.classList.toggle('dark');

        document.getElementById('icon-moon').style.display = isDark ? 'none' : 'inline';
        document.getElementById('icon-sun').style.display = isDark ? 'inline' : 'none';

        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }

    /* โหลด preference จาก localStorage เมื่อเปิดหน้า */
    (function() {
        const saved = localStorage.getItem('theme');
        if (saved === 'dark') {
            document.body.classList.add('dark');
            const moon = document.getElementById('icon-moon');
            const sun = document.getElementById('icon-sun');
            if (moon) moon.style.display = 'none';
            if (sun) sun.style.display = 'inline';
        }
    })();
    </script>

</body>

</html>