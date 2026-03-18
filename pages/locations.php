<?php
include '../components/session.php';
// checkLogin();
?>
<!DOCTYPE html>
<html lang="<?= $langCode ?>">
<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['home'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/locations.css">
</head>

<body class="d-flex flex-column" style="min-height: 100svh;">

    <?php include "../components/header.php"; ?>

    <main class="locations-main">

        <div class="locations-hero">
            <h2><?= $lang['chooselocate'] ?? 'เลือกสถานที่/โครงการ' ?></h2>
            <p>กรุณาเลือกโครงการที่คุณต้องการตรวจสอบข้อมูลการใช้พลังงานในขณะนี้</p>
        </div>

        <!-- ซ่อน table เดิม (CSS จัดการด้วย #table-location { display:none }) -->
        <table id="table-location">
            <thead>
                <tr>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <!-- Card list -->
        <div class="locations-list" id="location-list">
            <!-- Skeleton -->
            <div class="location-card location-card--skeleton">
                <div class="location-card__icon"></div>
                <span class="location-card__name"></span>
            </div>
            <div class="location-card location-card--skeleton">
                <div class="location-card__icon"></div>
                <span class="location-card__name"></span>
            </div>
        </div>

    </main>
    <?php include "../components/footer.php"; ?>

    <!-- Modal แก้ไขชื่อ -->
    <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="renameModalLabel">แก้ไขชื่อ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="rename-id">
                    <div class="mb-3">
                        <label for="new-name" class="form-label fw-semibold">ชื่อใหม่</label>
                        <input type="text" class="form-control text-black" id="new-name" placeholder="กรอกชื่อใหม่...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="submitRename()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <?php include "../scripts/scriptjs.html"; ?>

    <script>
    let meters = [];
    let types = [];
    let locations = [];

    document.addEventListener("DOMContentLoaded", async () => {
        await process();
    });

    async function process() {
        await fetchAll();
        generateTable();
    }

    async function fetchAll() {
        let responselocation = await fetch("../config/fetch-locations.php");
        let json_l = await responselocation.json();
        locations = json_l.data;
    }

    function generateTable() {
        const list = document.getElementById("location-list");
        list.innerHTML = ''; // ลบ skeleton

        if (!locations || Object.values(locations).length === 0) {
            list.innerHTML = '<p class="text-center text-muted py-4">ไม่พบสถานที่</p>';
            return;
        }

        Object.values(locations).forEach((row, i) => {
            const card = document.createElement("div");
            card.className = "location-card";
            card.style.animationDelay = `${i * 0.08}s`;
            card.innerHTML = `
                <div class="location-card__icon" style="background-color:#8BAE66; border-radius:12px; min-width:44px; min-height:44px; margin-right:1.25rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="white">
                    <path d="M12 11.5A2.5 2.5 0 0 1 9.5 9A2.5 2.5 0 0 1 12 6.5A2.5 2.5 0 0 1 14.5 9a2.5 2.5 0 0 1-2.5 2.5M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7"/>
                    </svg>
                </div>
                <span class="location-card__name">${row.name}</span>
                <span class="location-card__chevron">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M9.29 6.71a1 1 0 000 1.41L13.17 12l-3.88 3.88a1 1 0 001.41 1.41l4.59-4.59a1 1 0 000-1.41L10.7 6.7a1 1 0 00-1.41.01z"/>
                    </svg>
                </span>
                `;
            card.onclick = () => goToGroups(row.id);
            list.appendChild(card);
        });
    }

    function loadHoverImage(name) {
        console.log("Hover on : " + name);
        let img = document.getElementById("img-showLocation");
        if (img) img.setAttribute("src", "../assets/images/provinces/" + name + ".png");
    }

    async function goToGroups(id) {
        let success = await setSession("lid", id);
        if (success) {
            window.location.href = "groups.php";
        }
    }

    function submitRename() {
        const id = document.getElementById('rename-id').value;
        const name = document.getElementById('new-name').value;
        if (!name.trim()) return;
        bootstrap.Modal.getInstance(document.getElementById('renameModal'))?.hide();
    }
    </script>

</body>

</html>