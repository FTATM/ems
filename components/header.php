<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?> <!-- ต้องมี session_start() -->

<header class="fw-medium">
    <nav class="navbar d-flex justify-content-center justify-content-lg-between navbar-expand-lg navbar-light"
        style="background-color: <?= $bgsec ?>;">

        <!-- โลโก้และ sidebar button -->
        <div>
            <button id="sidebar_btn" class="btn ms-2 text-white" style="padding: 0;"><i class="bi bi-list fs-3 w-100 h-100"></i> </button>
            <a class="navbar-brand ms-2" href="../pages/home.php">
                <img src="../assets/images/logo1600x600tpw.png" alt="Logo" width="200">
            </a>
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

