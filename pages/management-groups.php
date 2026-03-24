<?php
include '../components/session.php';
checkLogin();
checkSession();
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['group'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/management-groups.css">
</head>

<body>
    <div id="main" class="d-flex" style="height:100svh; overflow:hidden;">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column" style="height:100svh; overflow:hidden;">
            <?php include "../components/header.php"; ?>

            <main class="groups-main">

                <!-- Hero -->
                <div class="groups-hero">
                    <h2><?= $lang['group'] ?> / <?= $lang['project'] ?></h2>
                    <p><?= $lang['select_group_action'] ?></p>
                </div>

                <!-- Content Card -->
                <div class="groups-card">

                    <!-- Card Header / Toolbar -->
                    <div class="groups-card__header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="groups-card__title"><?= $lang['all_group'] ?></span>
                            <span class="groups-card__count" id="group-count">—</span>
                        </div>
                        <button class="btn-create" onclick="openGroupModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            <?= $lang['create_group'] ?>
                        </button>
                    </div>

                    <!-- Scrollable list -->
                    <div class="groups-list-wrap">

                        <!-- Hidden legacy table (JS ยังคง populate ได้) -->
                        <table id="table-group" style="display:none;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div id="pagination" style="display:none;"></div>

                        <!-- Card list -->
                        <div class="groups-list" id="group-list">
                            <!-- Skeleton placeholders -->
                            <div class="group-card group-card--skeleton">
                                <div class="group-card__icon"></div>
                                <div class="group-card__body">
                                    <div class="group-card__id">&nbsp;</div>
                                    <div class="group-card__name">&nbsp;</div>
                                </div>
                            </div>
                            <div class="group-card group-card--skeleton">
                                <div class="group-card__icon"></div>
                                <div class="group-card__body">
                                    <div class="group-card__id">&nbsp;</div>
                                    <div class="group-card__name">&nbsp;</div>
                                </div>
                            </div>
                            <div class="group-card group-card--skeleton">
                                <div class="group-card__icon"></div>
                                <div class="group-card__body">
                                    <div class="group-card__id">&nbsp;</div>
                                    <div class="group-card__name">&nbsp;</div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /.groups-list-wrap -->

                    <!-- Pagination -->
                    <div id="pagination-visible" class="groups-pagination"></div>

                </div><!-- /.groups-card -->

            </main>
            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <!-- 🔧 Modal เพิ่มกลุ่ม -->
    <div class="modal fade" id="newGroupModal" tabindex="-1" aria-labelledby="newGroupModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['create_group'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="select-Location" class="form-label"><?= $lang['select_location'] ?></label>
                        <select class="form-select" id="select-Location"></select>
                    </div>
                    <div class="mb-3">
                        <label for="newName" class="form-label"><?= $lang['group_name'] ?></label>
                        <input type="text" class="form-control" id="newName"
                            placeholder="<?= $lang['enter_group_name'] ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary"
                        onclick="submitnewGroup()"><?= $lang['save'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔧 Modal แก้ไขชื่อ -->
    <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['edit_name'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="rename-id">
                    <div class="mb-3">
                        <label for="new-name" class="form-label"><?= $lang['new_name'] ?></label>
                        <input type="text" class="form-control" id="new-name"
                            placeholder="<?= $lang['enter_new_name'] ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary" onclick="submitRename()"><?= $lang['save'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- ❌ Modal ยืนยันการลบ -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['confirm_deletion'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-0"><?= $lang['confirm_delete_message'] ?></p>
                    <input type="hidden" id="delete-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-danger"
                        onclick="submitDelete()"><?= $lang['confirm_delete'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-management-groups.html"; ?>

    <!-- Card renderer: patch renderTablePage หลัง JS เดิม load -->
    <script>
    window.addEventListener('load', () => {
        const _originalRender = window.renderTablePage;

        window.renderTablePage = function(data, page) {
            _originalRender(data, page);
            _syncCardsFromTable();
        };

        setTimeout(_syncCardsFromTable, 100);
    });

    function _syncCardsFromTable() {
        const rows = document.querySelectorAll('#table-group tbody tr');
        const list = document.getElementById('group-list');
        const countEl = document.getElementById('group-count');

        if (!list) return;

        if (countEl) countEl.textContent = rows.length || '—';

        if (rows.length === 0) {
            list.innerHTML = `
                <div class="groups-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                        <path d="M16 7V5a2 2 0 0 0-4 0v2"/>
                        <line x1="12" y1="12" x2="12" y2="16"/>
                    </svg>
                    <p><?= $lang['no_groups'] ?></p>
                </div>`;
            return;
        }

        list.innerHTML = '';

        rows.forEach((tr, i) => {
            const cells = tr.querySelectorAll('td');
            if (cells.length < 3) return;

            const id = cells[0].textContent.trim();
            const locationName = cells[1].textContent.trim();
            const name = cells[2].textContent.trim();

            const renameInput = cells[3]?.querySelector('[onclick*="Rename"], [onclick*="rename"]');
            const deleteInput = cells[3]?.querySelector('[onclick*="Delete"], [onclick*="delete"]');
            const renameOnclick = renameInput ? renameInput.getAttribute('onclick') :
                `openRenameModal(${id}, '${name.replace(/'/g,"\\'")}')`;
            const deleteOnclick = deleteInput ? deleteInput.getAttribute('onclick') :
                `openDeleteModal(${id})`;

            const card = document.createElement('div');
            card.className = 'group-card';
            card.style.animationDelay = `${i * 0.06}s`;
            card.innerHTML = `
                <div class="group-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="white">
                        <path d="M2 7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7zm2 0v10h16V7H4zm2 2h12v2H6V9zm0 4h8v2H6v-2z"/>
                    </svg>
                </div>
                <div class="group-card__body">
                    <div class="group-card__name">${name}</div>
                    <div class="group-card__location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 11.5A2.5 2.5 0 0 1 9.5 9A2.5 2.5 0 0 1 12 6.5A2.5 2.5 0 0 1 14.5 9a2.5 2.5 0 0 1-2.5 2.5M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7"/>
                        </svg>
                        ${locationName}
                    </div>
                </div>
                <div class="group-card__actions" onclick="event.stopPropagation()">
                    <button class="btn-icon" title="แก้ไข" onclick="${renameOnclick}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button class="btn-icon btn-danger" title="ลบ" onclick="${deleteOnclick}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14H6L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                            <path d="M9 6V4h6v2"/>
                        </svg>
                    </button>
                </div>
            `;

            list.appendChild(card);
        });
    }
    </script>

</body>

</html>