<?php
include '../components/session.php';
?>


<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>
<head>
    <meta charset="UTF-8">
    <title>Permission Denied - RMS</title>
</head>

<body>
    <?php include "../components/header.php"; ?>
    </div>
        <?php include "../scripts/scriptjs.html"; ?>

        <script>
        // แสดง Alert เมื่อ logout สำเร็จ
        if (window.location.search.includes('permission=denied')) {
            Swal.fire({
                icon: 'error',
                title: 'การเข้าถึงถูกปฏิเสธ',
                text: 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้',
                timer: 1500,
                showConfirmButton: false
            });
            // ลบ query string ออกหลังแสดง alert
            window.history.replaceState({}, document.title, window.location.pathname);
        

        }
        </script>
</body>

</html>