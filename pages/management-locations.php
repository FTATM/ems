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
    <title><?= $lang['allmeter'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/management-locations.css">
</head>

<body>
    <div id="main" class="d-flex" style="height:100svh; overflow:hidden;">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column" style="height:100svh; overflow:hidden;">
            <?php include "../components/header.php"; ?>

            <main class="locations-main">

                <!-- Hero -->
                <div class="locations-hero">
                    <h2><?= $lang['location_management'] ?></h2>
                    <p><?= $lang['select_location_action'] ?></p>
                </div>

                <!-- Content Card -->
                <div class="locations-card">

                    <!-- Card Header / Toolbar -->
                    <div class="locations-card__header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="locations-card__title"><?= $lang['all_location'] ?></span>
                            <span class="locations-card__count" id="location-count">—</span>
                        </div>
                        <button class="btn-create" onclick="openNewLocationModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            <?= $lang['create_location'] ?>
                        </button>
                    </div>

                    <!-- Scrollable list -->
                    <div class="locations-list-wrap">

                        <!-- Hidden legacy table (JS ยังคง populate ได้) -->
                        <table id="table-location" style="display:none;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div id="pagination" style="display:none;"></div>

                        <!-- Card list -->
                        <div class="locations-list" id="location-list">
                            <!-- Skeleton placeholders -->
                            <div class="location-card location-card--skeleton">
                                <div class="location-card__icon"></div>
                                <div class="location-card__body">
                                    <div class="location-card__id">&nbsp;</div>
                                    <div class="location-card__name">&nbsp;</div>
                                </div>
                            </div>
                            <div class="location-card location-card--skeleton">
                                <div class="location-card__icon"></div>
                                <div class="location-card__body">
                                    <div class="location-card__id">&nbsp;</div>
                                    <div class="location-card__name">&nbsp;</div>
                                </div>
                            </div>
                            <div class="location-card location-card--skeleton">
                                <div class="location-card__icon"></div>
                                <div class="location-card__body">
                                    <div class="location-card__id">&nbsp;</div>
                                    <div class="location-card__name">&nbsp;</div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /.locations-list-wrap -->

                    <!-- Pagination -->
                    <div id="pagination-visible" class="locations-pagination"></div>

                </div><!-- /.locations-card -->

            </main>
            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <!-- 🔧 Modal เพิ่มตำแหน่ง -->
    <div class="modal fade" id="newLocationModal" tabindex="-1" aria-labelledby="newLocationModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['create_location'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="newName" class="form-label"><?= $lang['location_name'] ?></label>
                        <input type="text" class="form-control" id="newName"
                            placeholder="<?= $lang['enter_location_name'] ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary"
                        onclick="submitNewLocation()"><?= $lang['save'] ?></button>
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
    <?php include "../scripts/scriptjs-management-locations.html"; ?>

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
        const rows = document.querySelectorAll('#table-location tbody tr');
        const list = document.getElementById('location-list');
        const countEl = document.getElementById('location-count');

        if (!list) return;

        if (countEl) countEl.textContent = rows.length || '—';

        if (rows.length === 0) {
            list.innerHTML = `
                <div class="locations-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 11.5A2.5 2.5 0 0 1 9.5 9A2.5 2.5 0 0 1 12 6.5A2.5 2.5 0 0 1 14.5 9a2.5 2.5 0 0 1-2.5 2.5M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7"/>
                    </svg>
                    <p><?= $lang['no_positions_available'] ?></p>
                </div>`;
            return;
        }

        list.innerHTML = '';

        rows.forEach((tr, i) => {
            const cells = tr.querySelectorAll('td');
            if (cells.length < 2) return;

            const id = cells[0].textContent.trim();
            const name = cells[1].textContent.trim();

            const renameInput = cells[2]?.querySelector('[onclick*="Rename"], [onclick*="rename"]');
            const deleteInput = cells[2]?.querySelector('[onclick*="Delete"], [onclick*="delete"]');
            const renameOnclick = renameInput ? renameInput.getAttribute('onclick') :
                `openRenameModal(${id}, '${name.replace(/'/g, "\\'")}')`;
            const deleteOnclick = deleteInput ? deleteInput.getAttribute('onclick') :
                `openDeleteModal(${id})`;

            const card = document.createElement('div');
            card.className = 'location-card';
            card.style.animationDelay = `${i * 0.06}s`;
            card.innerHTML = `
                <div class="location-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="white">
                        <path d="M12 11.5A2.5 2.5 0 0 1 9.5 9A2.5 2.5 0 0 1 12 6.5A2.5 2.5 0 0 1 14.5 9a2.5 2.5 0 0 1-2.5 2.5M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7"/>
                    </svg>
                </div>
                <div class="location-card__body">
                    <div class="location-card__name">${name}</div>
                </div>
                <div class="location-card__actions" onclick="event.stopPropagation()">
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

            card.addEventListener('click', () => {
                // window.location.href = `groups.php?lid=${id}`;
            });

            list.appendChild(card);
        });
    }
    </script>

</body>

</html>