<?php
include '../components/session.php';
checkLogin();
checkSession();

// ── โหมดแก้ไข: ถ้ามี ?id= ให้ดึงข้อมูล user จาก DB ──
$editMode = false;
$userData = null;

if (!empty($_GET['id'])) {
    $editId = intval($_GET['id']);
    include "../config/connect.php";

    $stmt = $conn->prepare("SELECT id, username, full_name, id_card, phone, email, address, is_admin FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $userData = $row;
        $editMode = true;
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="<?= $langCode ?>">

<?php include "../scripts/ref.html"; ?>
<?php include "../scripts/style.html"; ?>

<head>
    <meta charset="UTF-8">
    <title><?= $editMode ? 'แก้ไขผู้ใช้' : $lang['group'] ?> - AMS</title>
    <link rel="stylesheet" href="../styles/user.css">
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>!important;">
    <div id="main">
        <?php include "../components/sidemenu.php"; ?>
        <div class="page-wrapper">
            <?php include "../components/header.php"; ?>

            <div class="page-content">

                <!-- Toast Notification -->
                <div id="toast" class="toast" role="alert" aria-live="polite"></div>

                <!-- Form Card -->
                <div class="form-card">
                    <form method="POST" action="../config/create-user.php" id="userForm"
                        <?= $editMode ? 'data-edit-mode="true" data-user-id="' . $userData['id'] . '"' : '' ?>>

                        <?php if ($editMode): ?>
                        <input type="hidden" name="edit_id" value="<?= $userData['id'] ?>">
                        <?php endif; ?>

                        <!-- Section: Account -->
                        <div class="form-section-title">
                            <i class="bi bi-shield-lock"></i> ข้อมูลบัญชี
                        </div>

                        <div class="form-grid-2">
                            <div class="mb-3">
                                <label class="form-label">Username <span class="req">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-person"></i>
                                    <input type="text" class="form-control <?= $editMode ? 'inline-editable' : '' ?>"
                                        name="username" id="username" placeholder="กรอก username"
                                        value="<?= $editMode ? htmlspecialchars($userData['username']) : '' ?>"
                                        <?= $editMode ? 'data-action="username"' : 'required' ?>>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    รหัสผ่าน <?= $editMode ? '' : '<span class="req">*</span>' ?>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-lock"></i>
                                    <input type="password"
                                        class="form-control <?= $editMode ? 'inline-editable' : '' ?>" name="password"
                                        id="password"
                                        placeholder="<?= $editMode ? 'กรอกรหัสผ่านใหม่' : 'กรอกรหัสผ่าน' ?>"
                                        <?= $editMode ? 'data-action="password" data-allow-empty="true"' : 'required' ?>>
                                </div>
                                <?php if ($editMode): ?>
                                <small class="text-muted-note">เว้นว่างไว้หากไม่ต้องการเปลี่ยน</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Section: Personal -->
                        <div class="form-section-title">
                            <i class="bi bi-person-vcard"></i> ข้อมูลส่วนตัว
                        </div>

                        <!-- full_name: readonly ในโหมดแก้ไข -->
                        <div class="mb-3">
                            <label class="form-label">ชื่อ-นามสกุล <span class="req">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person-fill"></i>
                                <input type="text" class="form-control <?= $editMode ? 'readonly-field' : '' ?>"
                                    name="full_name" id="full_name" placeholder="กรอกชื่อ-นามสกุล"
                                    value="<?= $editMode ? htmlspecialchars($userData['full_name']) : '' ?>"
                                    <?= $editMode ? 'readonly' : 'required' ?>>
                            </div>
                        </div>

                        <!-- id_card: readonly ในโหมดแก้ไข -->
                        <div class="mb-3">
                            <label class="form-label">เลขบัตรประชาชน <span class="req">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-credit-card"></i>
                                <input type="text" class="form-control <?= $editMode ? 'readonly-field' : '' ?>"
                                    name="id_card" id="id_card" placeholder="13 หลัก" maxlength="13"
                                    value="<?= $editMode ? htmlspecialchars($userData['id_card']) : '' ?>"
                                    <?= $editMode ? 'readonly' : 'required' ?>>
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <!-- phone: readonly ในโหมดแก้ไข -->
                            <div class="mb-3">
                                <label class="form-label">เบอร์โทรศัพท์ <span class="req">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-telephone"></i>
                                    <input type="tel" class="form-control <?= $editMode ? 'readonly-field' : '' ?>"
                                        name="phone" id="phone" placeholder="08xxxxxxxx"
                                        value="<?= $editMode ? htmlspecialchars($userData['phone']) : '' ?>"
                                        <?= $editMode ? 'readonly' : 'required' ?>>
                                </div>
                            </div>
                            <!-- email: readonly ในโหมดแก้ไข -->
                            <div class="mb-3">
                                <label class="form-label">อีเมล <span class="req">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-envelope"></i>
                                    <input type="email" class="form-control <?= $editMode ? 'readonly-field' : '' ?>"
                                        name="email" id="email" placeholder="you@example.com"
                                        value="<?= $editMode ? htmlspecialchars($userData['email']) : '' ?>"
                                        <?= $editMode ? 'readonly' : 'required' ?>>
                                </div>
                            </div>
                        </div>

                        <!-- address: readonly ในโหมดแก้ไข -->
                        <div class="mb-3">
                            <label class="form-label">ที่อยู่ <span class="req">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-geo-alt" style="top:12px;transform:none;"></i>
                                <textarea class="form-control <?= $editMode ? 'readonly-field' : '' ?>" name="address"
                                    id="address" rows="3" placeholder="กรอกที่อยู่"
                                    <?= $editMode ? 'readonly' : 'required' ?>><?= $editMode ? htmlspecialchars($userData['address']) : '' ?></textarea>
                            </div>
                        </div>

                        <!-- Section: Role (admin only) -->
                        <?php if ($_SESSION['user']['is_admin'] == 2): ?>
                        <div class="form-section-title">
                            <i class="bi bi-people"></i> สิทธิ์การใช้งาน
                        </div>
                        <div class="role-selector mb-2">
                            <div class="role-option">
                                <input type="radio" name="role" id="role-admin" value="admin"
                                    <?= ($editMode && $userData['is_admin'] >= 1) ? 'checked' : '' ?>>
                                <label for="role-admin">
                                    <i class="bi bi-shield-fill-check"></i> Admin
                                </label>
                            </div>
                            <div class="role-option">
                                <input type="radio" name="role" id="role-user" value="user"
                                    <?= (!$editMode || $userData['is_admin'] == 0) ? 'checked' : '' ?>>
                                <label for="role-user">
                                    <i class="bi bi-person-fill"></i> User
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div class="form-actions">
                            <a href="javascript:history.back()" class="btn-cancel">
                                <i class="bi bi-x-lg"></i> <?= $lang['cancel'] ?>
                            </a>
                            <button type="submit" class="btn-save">
                                <i class="bi bi-check-lg"></i>
                                <?= $editMode ? 'บันทึกการเปลี่ยนแปลง' : 'บันทึก' ?>
                            </button>
                        </div>

                    </form>
                </div>

            </div>

            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>

    <?php if ($editMode): ?>
    <!-- ── Inline Edit Script (เฉพาะโหมดแก้ไข) ── -->
    <script>
    (function() {
        const form = document.getElementById('userForm');
        const userId = form.dataset.userId;
        const toast = document.getElementById('toast');
        let toastTimer;

        function showToast(msg, type = 'success') {
            clearTimeout(toastTimer);
            toast.textContent = msg;
            toast.className = 'toast show ' + type;
            toastTimer = setTimeout(() => {
                toast.className = 'toast';
            }, 3000);
        }

        async function saveField(action, value) {
            const body = new URLSearchParams({
                id: userId,
                action,
                value
            });
            try {
                const res = await fetch('../config/update-user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body
                });
                const data = await res.json();
                if (data.success) {
                    showToast('✓ บันทึก ' + labelOf(action) + ' สำเร็จ', 'success');
                } else {
                    showToast('✗ ' + (data.message || 'เกิดข้อผิดพลาด'), 'error');
                }
            } catch (e) {
                showToast('✗ ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            }
        }

        function labelOf(action) {
            return {
                username: 'Username',
                password: 'รหัสผ่าน'
            } [action] || action;
        }

        document.querySelectorAll('.inline-editable').forEach(el => {
            // password ไม่มี original value — ตั้งเป็นค่าว่าง
            el._original = el.dataset.action === 'password' ? '' : (el.value || '');

            el.addEventListener('blur', async () => {
                const current = el.value;
                const allowEmpty = el.dataset.allowEmpty === 'true';

                // password: ถ้าว่างให้ข้าม ไม่ต้องบันทึก
                if (allowEmpty && current.trim() === '') return;

                if (current === el._original) return;

                if (!current.trim()) {
                    showToast('✗ ไม่สามารถบันทึกค่าว่างได้', 'error');
                    el.value = el._original;
                    return;
                }

                await saveField(el.dataset.action, current);

                // หลังบันทึก password สำเร็จ ให้เคลียร์ช่อง
                if (el.dataset.action === 'password') {
                    el.value = '';
                    el._original = '';
                } else {
                    el._original = current;
                }
            });

            el.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    el.blur();
                }
            });

            el.addEventListener('input', () => {
                const dirty = el.dataset.action === 'password' ?
                    el.value.trim() !== '' :
                    el.value !== el._original;
                el.classList.toggle('field-dirty', dirty);
            });
        });

    })();
    </script>
    <?php endif; ?>

</body>

</html>