<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?> <!-- ต้องมี session_start() -->

<header class="fw-medium">
    <nav class="navbar d-flex justify-content-center justify-content-lg-between navbar-expand-lg navbar-light"
        style="background-color: <?= $bgsec ?>;">

        <!-- โลโก้และ sidebar button -->
        <div class="d-flex align-items-center">
            <button id="sidebar_open" class="btn ms-2 text-white" style="padding: 0;"><i class="bi bi-list fs-3 w-100 h-100"></i> </button>
            <a class="navbar-brand ms-2" href="../pages/dashboard.php">
                <img src="../assets/images/Logo_First.jpg" alt="Logo" width="180">
            </a>
            <div class="d-flex gap-2">
                <img src="../assets/images/Logo 01_0.jpg" class="rounded" alt="logo01" width="40px" height="35px">
                <img src="../assets/images/Logo 02_0.jpg" class="rounded" alt="logo01" width="40px" height="35px">
                <img src="../assets/images/Logo 03_0.jpg" class="rounded" alt="logo01" width="40px" height="35px">
                <img src="../assets/images/Logo 04_0.jpg" class="rounded" alt="logo01" width="40px" height="35px">
            </div>
        </div>

        <!-- เมนูด้านขวา -->
        <div class="w-15 d-none d-lg-flex align-items-center">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <!-- ยังไม่ได้ Login -->
                <ul class="navbar-nav gap-3">
                    <li class="nav-item">
                        <a class="nav-link text" style="color: <?= $text ?>;"
                            href="../pages/login.php"><?= $lang['login'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text" style="color: <?= $text ?>;"
                            href="../pages/register.php"><?= $lang['register'] ?></a>
                    </li>
                </ul>
            <?php else: ?>
                <?php include "../components/avatar.php"; ?>

               
            <?php endif; ?>
        </div>
    </nav>
</header>

