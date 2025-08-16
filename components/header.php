<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?> <!-- ต้องมี session_start() -->

<header class="fw-medium">
    <nav class="navbar d-flex justify-content-center justify-content-lg-between navbar-expand-lg navbar-light"
        style="background-color: <?= $bgsec ?>;">

        <!-- โลโก้และ sidebar button -->
        <div>
            <button id="sidebar_btn" class="btn ms-2" style="padding: 0;">
                <i class="bi bi-list fs-3 w-100 h-100"></i>
            </button>
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
                        <a class="nav-link active text" style="color: <?= $text ?>;"
                            href="../pages/login.php"><?= $lang['login'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text" style="color: <?= $text ?>;"
                            href="../pages/register.php"><?= $lang['register'] ?></a>
                    </li>
                </ul>
            <?php else: ?>
                <!-- Login แล้ว -->
                <img src="../assets/images/bg_cards/1.png"
                    style="border-radius: 25px; height: 50px; width: 50px; margin-right: 10px;">

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link text-white" href="#" id="navbarDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= $lang['hi'] ?>! <?= htmlspecialchars($_SESSION['username']) ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <!-- เพิ่มเมนูอื่นได้ตามต้องการ -->
                                <li><a class="dropdown-item" href="../pages/logout.php"><?= $lang['logout'] ?></a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </nav>
</header>

