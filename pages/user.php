<?php
include '../components/session.php';
checkLogin();
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['building'] ?> - AMS</title>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important; min-height: 100svh;">
    <div id="main" class="">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 h-100 d-flex flex-column justify-content-center">
            <?php include "../components/header.php"; ?>
        </div>

        <!-- form CRUD  -->

        <div class="container mx-auto " style="max-width: 500px; margin-top: 20px;">
            <form class="p-4 bg-white bg-opacity-10 rounded shadow-sm"
                method="POST"
                action="../config/create-user.php">

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" id="username" placeholder="Enter username " required>
                </div>

                <div class="mb-3">
                    <label for="full_name" class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" class="form-control" name="full_name" id="full_name" placeholder="Enter full name" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                    <input type="tel" class="form-control" name="phone" id="phone" placeholder="08xxxxxxxx" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="you@example.com" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
                </div>

                <div class="mb-3">
                    <label for="id_card" class="form-label">เลขบัตรประชาชน</label>
                    <input type="text" class="form-control" name="id_card" id="id_card" placeholder="13 หลัก" maxlength="13" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">ที่อยู่</label>
                    <textarea class="form-control " name="address" id="address" rows="3" placeholder="Enter address" required></textarea>
                </div>
                <?php if ($_SESSION['user']['is_admin'] == 2): ?>
                    <div class="mb-3">
                        <label for="id_admin" class="form-label">ADMIN</label>
                        <input type="checkbox" class="form-check-input" >
                        <label for="id_user" class="form-label">USER</label>
                        <input type="checkbox" class="form-check-input" >
                    </div>
                <?php endif; ?>
                <!-- Button  -->
                <div class="d-flex justify-content-center grap-4">
                    <div class="d-flex items-center">
                        <button type="submit" class="btn btn-primary ">save</button>
                    </div>

                    <div class="d-flex items-center">
                        <button type="submit" class="btn btn-white border ">cancel</button>
                    </div>

                </div>
            </form>


        </div>

    </div>
    <!--  Modal แก้ไขชื่อ -->


    <!--  Modal ยืนยันการลบ -->


    <script id="theme-data" type="application/json">
        <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
</body>

</html>