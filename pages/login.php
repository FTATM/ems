<?php
include '../components/session.php';
?>


<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>
<head>
    <meta charset="UTF-8">
    <title><?=$lang['login']?> - RMS</title>
</head>

<body>
    <?php include "../components/header.php"; ?>
    <div class="d-flex justify-content-center align-items-center flex-column" style="width: 100vw; height: 90vh;">
        <div class="bg-white shadow rounded-2 p-4 w-100" style="max-width: 300px;">
            <h2 class="text-center mb-lg-5 mb-3"><?=$lang['login']?></h2>
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form onsubmit="loginUser(); return false;">
                <label class="form-label fw-medium"><?=$lang['username']?></label>
                <input id="username" class="form-control rounded" type="text" name="username" required placeholder="<?=$lang['husername']?>"><br>
                <label class="form-label fw-medium"><?=$lang['password']?></label>
                <input id="password" class="form-control rounded" type="password" name="password" required placeholder="<?=$lang['hpassword']?>"><br>
                <button class="btn btn-primary w-100" type="submit"><?=$lang['login']?></button>
            </form>
        </div>
    </div>
        <?php include "../scripts/scriptjs.html"; ?>
        <?php include '../scripts/scriptjs-login-user.html'; ?>

        <script>
        // แสดง Alert เมื่อ logout สำเร็จ
        if (window.location.search.includes('logout=success')) {
            Swal.fire({
                icon: 'success',
                title: 'ออกจากระบบสำเร็จ',
                text: 'คุณได้ออกจากระบบเรียบร้อยแล้ว',
                timer: 1500,
                showConfirmButton: false
            });
            // ลบ query string ออกหลังแสดง alert
            window.history.replaceState({}, document.title, window.location.pathname);
        

        }
        </script>
</body>

</html>