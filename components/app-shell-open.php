<?php
/* =====================================================================
   app-shell-open.php  —  closes <head>, opens <body> and the app shell.
   Pair with app-shell-close.php.  See doc-open.php for the flags.
   Structure produced:
     </head>
     <body class="ems-locked?">
       <div id="main" class="ems-shell ems-shell--locked? ems-shell--nonav?">
         <sidemenu.php>                    (unless $EMS_SHELL_NO_NAV)
         <div class="ems-shell__main">
           <header.php>
             <div class="ems-shell__scroll">
               ... page content ...
   ===================================================================== */
$__ems_locked = !empty($EMS_SHELL_LOCKED);
$__ems_nonav  = !empty($EMS_SHELL_NO_NAV);
?>
</head>
<body class="<?= $__ems_locked ? 'ems-locked' : '' ?>">
<div id="main" class="ems-shell<?= $__ems_locked ? ' ems-shell--locked' : '' ?><?= $__ems_nonav ? ' ems-shell--nonav' : '' ?>">
<?php if (!$__ems_nonav) include __DIR__ . '/sidemenu.php'; ?>
    <div class="ems-shell__main">
<?php include __DIR__ . '/header.php'; ?>
        <div class="ems-shell__scroll">
