<?php
include '../components/session.php';
if ($_SESSION['user']['is_admin'] == 0) {
    header("Location: ../pages/permission_denied.php?permission=denied");
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $lang['usermnm'] ?> - EMS</title>
    <link rel="stylesheet" href="../styles/management-users.css">
</head>

<body style="height:100svh; overflow:hidden;">
    <div id="main" class="d-flex" style="height:100svh; overflow:hidden;">
        <?php include "../components/sidemenu.php"; ?>
        <div class="w-100 d-flex flex-column" style="height:100svh; overflow:hidden;">
            <?php include "../components/header.php"; ?>

            <main class="users-main">

                <!-- Hero -->
                <div class="users-hero">
                    <h2><?= $lang['usermnm'] ?></h2>
                    <p><?= $lang['user_management_desc'] ?></p>
                </div>

                <!-- Card -->
                <div class="users-card">

                    <!-- Toolbar -->
                    <div class="users-toolbar">
                        <div class="users-toolbar__left">
                            <span class="users-card__title"><?= $lang['list_of_users'] ?></span>
                            <span class="users-card__count" id="user-count">...</span>
                        </div>
                        <div class="users-toolbar__right">
                            <div class="users-search">
                                <input id="user-search" type="text" placeholder="<?= $lang['search_user'] ?>"
                                    oninput="applyUserFilter()" autocomplete="off">
                                <button type="button" id="user-search-clear" class="users-search__clear" hidden
                                    onclick="clearUserSearch()" aria-label="<?= $lang['close'] ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 6 6 18M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <button type="button" id="btn-add-user" class="btn-add-user" onclick="openAddUserModal()">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                                <?= $lang['add_user'] ?>
                            </button>
                        </div>
                    </div>

                    <!-- Table scroll wrapper -->
                    <div class="users-table-wrap">
                        <table id="table-user"></table>
                    </div>

                </div>

                <!-- Pagination -->
                <div id="pagination"></div>

            </main>

            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <!-- ── Modal แก้ไขข้อมูล ── -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['edit_personal_information'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id">
                    <label for="edit-full_name" class="form-label"><?= $lang['full_name'] ?></label>
                    <input type="text" class="form-control" id="edit-full_name">
                    <label for="edit-phone" class="form-label"><?= $lang['phone'] ?></label>
                    <input type="text" class="form-control" id="edit-phone">
                    <label for="edit-address" class="form-label"><?= $lang['address'] ?></label>
                    <input type="text" class="form-control" id="edit-address">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary" onclick="submitEdit()"><?= $lang['save'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Modal ยืนยันการลบ ── -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['confirm_deletion'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body">
                    <p style="color:var(--text-card); margin:0;"><?= $lang['confirm_delete_message'] ?></p>
                    <input type="hidden" id="delete-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-danger"
                        onclick="submitDelete()"><?= $lang['confirm'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Modal เปลี่ยนรหัสผ่าน ── -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="changePasswordForm">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $lang['change_password'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="<?= $lang['close'] ?>"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="change-password-id" name="user_id">
                        <label for="change-password-new" class="form-label"><?= $lang['new_password'] ?></label>
                        <input type="password" class="form-control" id="change-password-new" name="change-password-new"
                            required>
                        <label for="change-password-confirm" class="form-label"><?= $lang['confirm_password'] ?></label>
                        <input type="password" class="form-control" id="change-password-confirm"
                            name="change-password-confirm" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                        <button type="submit" class="btn btn-primary"><?= $lang['change'] ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Modal เพิ่มผู้ใช้ ── -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['add_user_title'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body">
                    <label for="add-username" class="form-label"><?= $lang['username'] ?></label>
                    <input type="text" class="form-control" id="add-username" autocomplete="off">
                    <label for="add-full_name" class="form-label"><?= $lang['full_name'] ?></label>
                    <input type="text" class="form-control" id="add-full_name">
                    <label for="add-phone" class="form-label"><?= $lang['phone'] ?></label>
                    <input type="text" class="form-control" id="add-phone">
                    <label for="add-email" class="form-label"><?= $lang['email'] ?></label>
                    <input type="text" class="form-control" id="add-email">
                    <label for="add-password" class="form-label"><?= $lang['password'] ?></label>
                    <input type="password" class="form-control" id="add-password" autocomplete="new-password">
                    <label for="add-id_card" class="form-label"><?= $lang['id_card'] ?></label>
                    <input type="text" class="form-control" id="add-id_card">
                    <label for="add-address" class="form-label"><?= $lang['address'] ?></label>
                    <input type="text" class="form-control" id="add-address">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary" onclick="submitAddUser()"><?= $lang['save'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>

    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-management-users.html"; ?>

    <script>
    /* ── User count badge label (จำนวนรวมตั้งใน applyUserFilter ของ scriptjs) ── */
    window.USER_COUNT_LABEL = '<?= $lang['list'] ?>';
    </script>

</body>

</html>