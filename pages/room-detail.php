<?php
include '../components/session.php';
checkLogin();

$roomId = (int)($_GET['id'] ?? 0);
if ($roomId <= 0) {
    header("Location: ../pages/overview-room.php");
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
    <link rel="stylesheet" href="../styles/room-detail.css">
    <script>
    const LANG = <?= json_encode($lang) ?>;
    const ROOM_ID = <?= (int)$roomId ?>;
    </script>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>">
    <div id="main">
        <?php include "../components/sidemenu.php"; ?>

        <div class="ov-wrapper">
            <?php include "../components/header.php"; ?>

            <div class="ov-content">

                <div class="rd-breadcrumb">
                    <button type="button" class="room-btn" onclick="goBack()">
                        <i class="bi bi-chevron-left"></i> <?= $lang['back'] ?>
                    </button>
                    <span class="rd-breadcrumb__sep">/</span>
                    <span class="rd-breadcrumb__current" id="rd-room-name">—</span>
                </div>

                <div id="rd-loading" class="dash-skeleton" style="padding:2rem 0;">
                    <div class="dash-skeleton-line" style="width:60%"></div>
                    <div class="dash-skeleton-line" style="width:90%"></div>
                    <div class="dash-skeleton-line" style="width:75%"></div>
                </div>

                <div id="rd-body" hidden>
                <div class="rd-split">
                    <!-- ── ฝั่งซ้าย 70% (rd): ข้อมูลห้อง/ผู้เช่า/มิเตอร์/ออกบิล/สรุปยอด ── -->
                    <div class="rd-split__main">

                    <!-- ── Room info + status ── -->
                    <div class="rd-grid">
                        <div class="rd-card">
                            <div class="rd-card__title" style="justify-content:space-between; display:flex;">
                                <span><i class="bi bi-door-closed-fill"></i> <?= $lang['room'] ?></span>
                                <button type="button" class="btn-icon-sm" title="<?= $lang['edit_room'] ?>" onclick="openEditRoomModal()">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <div class="rd-info-grid" id="rd-room-info"></div>
                        </div>

                        <div class="rd-card">
                            <div class="rd-card__title" style="justify-content:space-between; display:flex;">
                                <span><i class="bi bi-person-fill"></i> <?= $lang['room_tenant'] ?></span>
                                <button type="button" class="btn-icon-sm" title="<?= $lang['manage_tenant'] ?>" id="rd-tenant-edit-btn" onclick="openTenantModal()">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <div id="rd-tenant-info"></div>
                        </div>
                    </div>

                    <!-- ── Assigned meters + Generate bill ── -->
                    <div class="rd-grid">
                        <div class="rd-card">
                            <div class="rd-card__title"><i class="bi bi-plug-fill"></i> <?= $lang['assigned_meters'] ?></div>
                            <div class="rd-info-grid">
                                <div>
                                    <span class="ov-label"><?= $lang['electrical'] ?></span>
                                    <select class="form-select form-select-sm" id="rd-meter-elec" onchange="assignMeter(1, this.value)"></select>
                                </div>
                                <div>
                                    <span class="ov-label"><?= $lang['water'] ?></span>
                                    <select class="form-select form-select-sm" id="rd-meter-water" onchange="assignMeter(2, this.value)"></select>
                                </div>
                            </div>
                        </div>

                        <div class="rd-card">
                            <div class="rd-card__title"><i class="bi bi-receipt-cutoff"></i> <?= $lang['generate_bill'] ?></div>
                            <p class="ov-muted" style="margin-bottom:0.6rem;"><?= $lang['generate_bill_hint'] ?></p>
                            <button type="button" class="room-btn room-btn--primary" onclick="openGenerateBillModal()">
                                <i class="bi bi-plus-lg"></i> <?= $lang['generate_bill'] ?>
                            </button>
                        </div>
                    </div>

                    <!-- ── Bill totals ── -->
                    <div class="rd-stats" id="rd-stats"></div>

                    </div><!-- /rd-split__main -->

                    <!-- ── ฝั่งขวา 30% (ov): ตารางประวัติ — sticky ตอนจอกว้าง ── -->
                    <div class="rd-split__side">

                    <!-- ── Contract history ── -->
                    <h6 class="ov-subheading"><i class="bi bi-file-earmark-person"></i> <?= $lang['room_contract_period'] ?></h6>
                    <div class="ov-table-wrap">
                        <table class="ov-table">
                            <thead>
                                <tr>
                                    <th><?= $lang['room_tenant'] ?></th>
                                    <th><?= $lang['room_contract_period'] ?></th>
                                    <th><?= $lang['status'] ?></th>
                                </tr>
                            </thead>
                            <tbody id="rd-contracts-body"></tbody>
                        </table>
                    </div>

                    <!-- ── Bill history ── -->
                    <h6 class="ov-subheading">
                        <i class="bi bi-receipt"></i> <?= $lang['room_latest_bill'] ?>
                        <a id="rd-print-bill-link" href="#" target="_blank" class="room-btn" style="margin-left:auto; font-size:0.78rem; padding:0.3rem 0.7rem;" hidden>
                            <i class="bi bi-printer"></i> <?= $lang['exportpdf'] ?>
                        </a>
                    </h6>
                    <div class="ov-table-wrap">
                        <table class="ov-table">
                            <thead>
                                <tr>
                                    <th><?= $lang['room_type'] ?></th>
                                    <th><?= $lang['room_contract_period'] ?></th>
                                    <th><?= $lang['room_price'] ?></th>
                                    <th><?= $lang['status'] ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="rd-bills-body"></tbody>
                        </table>
                    </div>

                    <!-- ── Payment history ── -->
                    <h6 class="ov-subheading"><i class="bi bi-cash-coin"></i> <?= $lang['payment'] ?></h6>
                    <div class="ov-table-wrap">
                        <table class="ov-table">
                            <thead>
                                <tr>
                                    <th><?= $lang['datalasttime'] ?></th>
                                    <th><?= $lang['room_price'] ?></th>
                                    <th><?= $lang['note'] ?></th>
                                </tr>
                            </thead>
                            <tbody id="rd-payments-body"></tbody>
                        </table>
                    </div>

                    </div><!-- /rd-split__side -->
                </div><!-- /rd-split -->
                </div>

            </div>

            <?php include "../components/footer.php"; ?>
        </div>
    </div>

    <!-- ✏️ Modal แก้ไขห้อง -->
    <div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['edit_room'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="er-name" class="form-label"><?= $lang['room_name'] ?></label>
                        <input type="text" class="form-control" id="er-name" maxlength="10">
                        <div class="form-text"><?= $lang['room_name_max_hint'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="er-type" class="form-label"><?= $lang['room_type'] ?></label>
                        <input type="text" class="form-control" id="er-type">
                    </div>
                    <div class="mb-3">
                        <label for="er-size" class="form-label"><?= $lang['room_size'] ?> (m²)</label>
                        <input type="number" step="0.01" class="form-control" id="er-size">
                    </div>
                    <div class="mb-3">
                        <label for="er-price" class="form-label"><?= $lang['room_price'] ?></label>
                        <input type="number" step="0.01" class="form-control" id="er-price">
                    </div>
                    <div class="mb-3">
                        <label for="er-note" class="form-label"><?= $lang['note'] ?></label>
                        <textarea class="form-control" id="er-note" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary" onclick="submitEditRoom()"><?= $lang['save'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 👤 Modal จัดการผู้เช่า/สัญญา -->
    <div class="modal fade" id="tenantModal" tabindex="-1" aria-labelledby="tenantModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenantModalLabel"><?= $lang['manage_tenant'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body p-4" id="tenant-modal-body">
                    <!-- filled by JS depending on occupied/empty -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary" id="tenant-modal-submit-btn"><?= $lang['save'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 💳 Modal บันทึกการชำระเงิน -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['record_payment'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="pay-bill-id">
                    <p class="ov-muted" id="pay-bill-summary"></p>
                    <div class="mb-3">
                        <label class="form-label"><?= $lang['payment_amount'] ?></label>
                        <input type="number" step="0.01" class="form-control" id="pay-amount">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= $lang['payment_date'] ?></label>
                        <input type="date" class="form-control" id="pay-date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= $lang['payment_method'] ?></label>
                        <select class="form-select" id="pay-method">
                            <option value="cash"><?= $lang['payment_method_cash'] ?></option>
                            <option value="transfer"><?= $lang['payment_method_transfer'] ?></option>
                            <option value="other"><?= $lang['payment_method_other'] ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= $lang['note'] ?></label>
                        <input type="text" class="form-control" id="pay-note">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary" onclick="submitPayment()"><?= $lang['save'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🧾 Modal ออกบิล -->
    <div class="modal fade" id="generateBillModal" tabindex="-1" aria-labelledby="generateBillModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $lang['generate_bill'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= $lang['close'] ?>"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="gb-start-date" class="form-label"><?= $lang['from'] ?></label>
                        <input type="date" class="form-control" id="gb-start-date">
                    </div>
                    <div class="mb-3">
                        <label for="gb-end-date" class="form-label"><?= $lang['to'] ?></label>
                        <input type="date" class="form-control" id="gb-end-date">
                    </div>
                    <div id="gb-result"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $lang['cancel'] ?></button>
                    <button type="button" class="btn btn-primary" id="gb-submit-btn" onclick="submitGenerateBill()"><?= $lang['generate_bill'] ?></button>
                </div>
            </div>
        </div>
    </div>

    <script id="theme-data" type="application/json">
    <?= json_encode($_SESSION['theme'], JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php include "../scripts/scriptjs.html"; ?>
    <?php include "../scripts/scriptjs-room-detail.html"; ?>
</body>

</html>
