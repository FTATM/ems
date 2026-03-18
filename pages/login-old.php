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
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['login'] ?> - EMS</title>
</head>

<body>
    <?php include "../components/header.php"; ?>
    <div class="d-flex justify-content-center align-items-center flex-column bg-black bg-opacity-75" style="width: 100vw; height: 90vh;">
        <div class="bg-white shadow rounded-2 p-4 w-100" style="max-width: 300px;">
            <h2 class="text-center mb-lg-5 mb-3"><?= $lang['login'] ?></h2>
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form onsubmit="login(); return false;">
                <label class="form-label fw-medium"><?= $lang['username'] ?></label>
                <input id="username" class="form-control rounded text-black" type="text" name="username" required placeholder="<?= $lang['husername'] ?>"><br>
                <label class="form-label fw-medium"><?= $lang['password'] ?></label>
                <input id="password" class="form-control rounded text-black" type="password" name="password" required placeholder="<?= $lang['hpassword'] ?>"><br>
                <button class="btn btn-primary w-100"><?= $lang['login'] ?></button>
                <!-- <a href="admin.php" class="btn btn-light w-100" type="submit">Admin</a> -->
            </form>
        </div>
    </div>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-login.html"; ?>

    <script>

    </script>
</body>

</html>