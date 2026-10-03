<?php
include '../components/session.php';
checkLogin();

if (!isset($_SESSION['gid'])) {
    header("Location: ../pages/locations.php?step=2");
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['room'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/overview-room.css">
    <script>
    const LANG = <?= json_encode($lang) ?>;
    const GROUP_ID = "<?= (int)$_SESSION['gid'] ?>";
    </script>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>">
    <div id="main">
        <?php include "../components/sidemenu.php"; ?>

        <div class="ov-wrapper">
            <?php include "../components/header.php"; ?>

            <div class="ov-content">

                <!-- ── Filter Bar ── -->
                <div class="room-filter-bar">
                    <div class="room-filter-group">
                        <span class="room-filter-label"><?= $lang['location'] ?></span>
                        <div class="room-location-badge">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span id="location"></span>
                        </div>
                    </div>
                    <div class="room-filter-divider"></div>
                    <div class="room-filter-group">
                        <span class="room-filter-label"><?= $lang['group'] ?></span>
                        <div class="room-group-badge">
                            <i class="bi bi-collection-fill"></i>
                            <span id="group"></span>
                        </div>
                    </div>

                    <div class="room-filter-spacer"></div>

                    <button type="button" class="room-btn room-btn--primary" onclick="openAddModal()">
                        <i class="bi bi-plus-lg"></i> <?= $lang['add_room'] ?>
                    </button>
                </div>

                <!-- ── Status filter ── -->
                <div class="room-status-filter" id="room-status-filter">
                    <button type="button" class="room-status-filter__btn is-active" data-filter="all" onclick="setStatusFilter('all')"><?= $lang['all'] ?></button>
                    <button type="button" class="room-status-filter__btn" data-filter="empty" onclick="setStatusFilter('empty')"><?= $lang['room_status_empty'] ?></button>
                    <button type="button" class="room-status-filter__btn" data-filter="occupied" onclick="setStatusFilter('occupied')"><?= $lang['room_status_occupied'] ?></button>
                    <button type="button" class="room-status-filter__btn" data-filter="maintenance" onclick="setStatusFilter('maintenance')"><?= $lang['room_status_maintenance'] ?></button>
                </div>

                <!-- ── Empty state ── -->
                <div id="ov-empty" class="ov-empty" hidden>
                    <i class="bi bi-door-closed"></i>
                    <h3><?= $lang['no_room_found'] ?></h3>
                    <p><?= $lang['no_room_found_hint'] ?></p>
                </div>

                <!-- ── Room grid ── -->
                <div class="room-grid-wrap">
                    <div class="room-grid" id="room-grid">
                        <div class="room-card room-card--skeleton"></div>
                        <div class="room-card room-card--skeleton"></div>
                        <div class="room-card room-card--skeleton"></div>
                    </div>
                </div>

            </div>

            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <!-- 🔧 Modal เพิ่มห้อง -->
    <div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['add_room'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add-room-name" class="form-label"><?= $lang['room_name'] ?></label>
                        <input type="text" class="form-control" id="add-room-name" maxlength="10">
                        <div class="form-text"><?= $lang['room_name_max_hint'] ?></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary" onclick="submitAddRoom()"><?= $lang['save'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔧 Modal แก้ไขชื่อห้อง -->
    <div class="modal fade" id="renameRoomModal" tabindex="-1" aria-labelledby="renameRoomModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['rename_room'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="rename-room-id">
                    <div class="mb-3">
                        <label for="rename-room-name" class="form-label"><?= $lang['room_name'] ?></label>
                        <input type="text" class="form-control" id="rename-room-name" maxlength="10">
                        <div class="form-text"><?= $lang['room_name_max_hint'] ?></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary" onclick="submitRenameRoom()"><?= $lang['save'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- ❌ Modal ยืนยันการลบห้อง -->
    <div class="modal fade" id="deleteRoomModal" tabindex="-1" aria-labelledby="deleteRoomModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['delete_room'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-0"><?= $lang['room_confirm_delete'] ?></p>
                    <input type="hidden" id="delete-room-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-danger" onclick="submitDeleteRoom()"><?= $lang['confirm_delete'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-overview-room.html"; ?>
</body>

</html>
