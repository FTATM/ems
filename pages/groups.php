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
    <title><?= $lang['group'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/groups.css">
</head>

<body style="height:100svh;overflow:hidden;">
    <div id="main" class="d-flex flex-column" style="height:100svh;overflow:hidden;">

        <?php include "../components/header.php"; ?>

        <main class="groups-main"
            style="flex:1 1 0;min-height:0;overflow:hidden;display:flex;flex-direction:column;align-items:center;padding:1rem 1rem 0;">

            <!-- Hero -->
            <div class="groups-hero"
                style="flex-shrink:0; width:100%; max-width:720px; text-align:center; margin-bottom:0.75rem;">
                <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:0.25rem;"><?= $lang['choosegroup'] ?></h2>
                <p style="font-size:0.9rem; color:var(--text-body); margin:0;">
                    กรุณาเลือกกลุ่มที่คุณต้องการตรวจสอบข้อมูลการใช้พลังงาน</p>
            </div>

            <!-- Toolbar -->
            <div class="groups-toolbar" style="flex-shrink:0;margin-bottom:0.75rem;">
                <div class="groups-search">
                    <input type="text" id="group-search" placeholder="ค้นหากลุ่ม…" oninput="filterGroupCards()">
                </div>
            </div>

            <!-- Scroll wrapper: max-height ควบคุมความสูงกรอบ -->
            <div class="groups-list-wrap"
                style="flex-shrink:0; width:100%; max-width:720px; max-height:68vh; overflow-y:auto; overflow-x:hidden;padding-bottom:1rem;">
                <div class="groups-list" id="groups-list">
                    <div class="group-card group-card--skeleton" aria-hidden="true">
                        <div class="group-card__icon"><span
                                class="material-icons-outlined material-symbols--workspaces"></span></div>
                        <div class="group-card__info">
                            <span class="group-card__name">&nbsp;</span>
                            <span class="group-card__location">&nbsp;</span>
                        </div>
                    </div>
                    <div class="group-card group-card--skeleton" aria-hidden="true">
                        <div class="group-card__icon"><span
                                class="material-icons-outlined material-symbols--workspaces"></span></div>
                        <div class="group-card__info">
                            <span class="group-card__name">&nbsp;</span>
                            <span class="group-card__location">&nbsp;</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden legacy -->
            <table id="table-group" class="d-none">
                <thead>
                    <tr>
                        <th><?= $lang['location']; ?></th>
                        <th><?= $lang['name']; ?></th>
                        <th><?= $lang['action']; ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div id="pagination" class="d-none"></div>

        </main>

        <?php include "../components/footer.php"; ?>

    </div>

    <!-- Modal แก้ไขชื่อ -->
    <div class="modal fade" id="renameModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขชื่อ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="rename-id">
                    <div class="mb-3">
                        <label for="new-name" class="form-label">ชื่อใหม่</label>
                        <input type="text" class="form-control text-black" id="new-name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="submitRename()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal ยืนยันการลบ -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ยืนยันการลบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-black">คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?</p>
                    <input type="hidden" id="delete-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-danger" onclick="submitDelete()">ยืนยันลบ</button>
                </div>
            </div>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-groups.html"; ?>

    <script>
    (function() {
        function waitForGroups(callback) {
            if (typeof groups !== 'undefined' && groups.length > 0) callback();
            else setTimeout(() => waitForGroups(callback), 150);
        }

        function buildCards() {
            const list = document.getElementById('groups-list');
            if (!list) return;
            const filtered = groups.filter(g => g.location_id == locationID);
            if (!filtered.length) {
                list.innerHTML =
                    `<div style="text-align:center;padding:3rem;opacity:.5"><span class="material-icons-outlined" style="font-size:3rem">workspaces</span><p>ไม่พบกลุ่มในโลเคชั่นนี้</p></div>`;
                return;
            }
            list.innerHTML = '';
            filtered.forEach(function(row) {
                const locName = (typeof getNameLocation === 'function' && getNameLocation(locationID)) ?
                    getNameLocation(locationID).name : locationID;
                const card = document.createElement('div');
                card.className = 'group-card';
                card.style.cursor = 'pointer';
                card.innerHTML = `
                    <div class="group-card__icon"><span class="material-icons-outlined">workspaces</span></div>
                    <div class="group-card__info">
                        <span class="group-card__name">${row.name || '—'}</span>
                        <span class="group-card__location">
                            <span class="material-icons-outlined" style="font-size:.85rem;vertical-align:middle">location_on</span>
                            ${locName}
                        </span>
                    </div>
                    <div class="group-card__actions" onclick="event.stopPropagation()">
                <button class="btn-icon" title="Electrical" onclick="reDirectTo(1,${row.id})">
                    <span class="material-icons-outlined">bolt</span>
                    <span class="btn-icon__label">Electrical</span>
                </button>
                <button class="btn-icon" title="Water" onclick="reDirectTo(2,${row.id})" disabled style="opacity:0.35;cursor:not-allowed;pointer-events:none;">
                    <span class="material-icons-outlined">water_drop</span>
                    <span class="btn-icon__label">Water</span>
                </button>
                <button class="btn-icon" title="Room" onclick="reDirectTo(3,${row.id})" disabled style="opacity:0.35;cursor:not-allowed;pointer-events:none;">
                    <span class="material-icons-outlined">meeting_room</span>
                    <span class="btn-icon__label">Room</span>
                </button>
            </div>
                `;
                card.addEventListener('click', function() {
                    reDirectTo(1, row.id);
                });
                list.appendChild(card);
            });
        }

        waitForGroups(buildCards);

        window.filterGroupCards = function() {
            const q = (document.getElementById('group-search')?.value || '').toLowerCase();
            document.querySelectorAll('#groups-list .group-card').forEach(function(c) {
                c.style.display = c.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        };
    })();
    </script>

</body>

</html>