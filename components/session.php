<?php
require_once __DIR__ . '/serial.php';
session_start();


$timeout = 1800;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

// อัพเดทเวลาล่าสุดที่ใช้งาน
$_SESSION['last_activity'] = time();


function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../pages/login.php");
        exit();
    }
    if (!checktoken()) {
        header("Location: ../pages/notallow.php");
        exit();
    }
}
function check()
{
    if (!checktoken()) {
        header("Location: ../pages/notallow.php");
        exit();
    }
}
function checkSession()
{
    if (!isset($_SESSION['lid'])) {
        header("Location: ../pages/locations.php");
        exit();
    } else if (!isset($_SESSION['gid'])) {
        header("Location: ../pages/groups.php");
        exit();
    } else if (!isset($_SESSION['tid'])) {
        header("Location: ../pages/groups.php");
        exit();
    }
}

function logout()
{
    session_unset();
    session_destroy();
    header("Location: ../pages/login.php");
    exit();
}

// รายการภาษาที่รองรับ
$supportedLangs = ['th', 'en'];

// ตรวจสอบการเปลี่ยนภาษาจาก URL
if (isset($_GET['lang']) && in_array($_GET['lang'], $supportedLangs)) {
    $_SESSION['lang'] = $_GET['lang'];
}

// กำหนดค่าภาษาเริ่มต้น หากยังไม่เคยเลือก
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'th';
}

// โหลดภาษา
$langCode = $_SESSION['lang'];
$langFile = __DIR__ . "/../lang/$langCode.php";
if (file_exists($langFile)) {
    require_once $langFile;
} else {
    // fallback ภาษาอังกฤษ
    require_once __DIR__ . "/../lang/en.php";
}
function buildLangSwitchLink($targetLang)
{
    $query = $_GET;
    $query['lang'] = $targetLang;
    return '?' . http_build_query($query);
}

if (isset($_GET['theme'])) {
    $_SESSION['theme'] = $_GET['theme'];
}

if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'dark';
}

/* ==========================================================
   EMS "Aurora" design palette — blue accent on cool slate.
   These PHP vars are echoed inline on <body> and in pages.
   The canonical token set lives in scripts/style.html (:root).
   ========================================================== */
if ($_SESSION['theme'] === 'dark') {
    $bg = '#0B0F14';          // app background
    $bgsec = '#141A21';       // panels / surfaces
    $secon = '#8B98A5';       // secondary text
    $text = '#E6EDF3';        // primary text
    $textMuted = '#8B98A5';   // muted text
    $btnColor = '#1C242E';    // neutral button surface
    $accentColor = '#60A5FA'; // blue accent (bright, for dark bg)
} else {
    $bg = '#EEF2F6';
    $bgsec = '#FFFFFF';
    $secon = '#586472';
    $text = '#0F172A';
    $textMuted = '#586472';
    $btnColor = '#E6EBF0';
    $accentColor = '#2563EB'; // blue accent (deep, for light bg)
}

function buildthemeSwitchLink($targetheme)
{
    $query = $_GET;
    $query['theme'] = $targetheme;
    return '?' . http_build_query($query);
}

function setLocation($targetLocationID)
{
    $_SESSION['lid'] = $targetLocationID;
}

function setGroup($targetGroupID)
{
    $_SESSION['gid'] = $targetGroupID;
}

function setTypeMeter($targetTypeMeterID)
{
    $_SESSION['tmid'] = $targetTypeMeterID;
}
// ตั้งค่าเริ่มต้นถ้ายังไม่มี session