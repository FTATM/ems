<?php
/* =====================================================================
   doc-open.php  —  opens the single <head> for a migrated page.

   Expects (all optional):
     $EMS_PAGE_TITLE     string  <title> text            (default "EMS")
     $EMS_SHELL_LOCKED   bool    viewport-lock the page  (monitor pages)
     $EMS_SHELL_NO_NAV   bool    omit the sidemenu       (Pattern C only)

   Usage in pages/X.php:
     include '../components/session.php';
     checkLogin(); checkSession();
     $EMS_PAGE_TITLE = $lang['home'] . ' - EMS';
     $EMS_SHELL_LOCKED = true;                // optional
     include '../components/doc-open.php';
     ?>  <link rel="stylesheet" href="../styles/X.css">
         <script>const LANG = <?= json_encode($lang) ?>;</script>
     <?php include '../components/app-shell-open.php';
   The <head> is left OPEN here — app-shell-open.php prints </head>.
   ===================================================================== */
$__ems_dark = (($_SESSION['theme'] ?? '') === 'dark') ? ' class="dark"' : '';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($langCode ?? 'th') ?>"<?= $__ems_dark ?>>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($EMS_PAGE_TITLE ?? 'EMS') ?></title>
<?php include __DIR__ . '/../scripts/ref.html'; ?>
<?php include __DIR__ . '/../scripts/style.html'; ?>
<link rel="stylesheet" href="../styles/ems-layout.css">
