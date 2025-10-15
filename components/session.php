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
    $_SESSION['lang'] = 'en';
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

if ($_SESSION['theme'] === 'dark') {
    $bg = '#121212';
    $bgsec = '#1e1e1e';
    $secon = '#9e9e9e';
    $text = '#e0e0e0';
    $btnColor = '#424242';
    $accentColor = '#81D4FA';
} else {
    $bg = '#f9f9f9';
    $bgsec = '#383838ff';
    $secon = '#616161';
    $text = '#ffffffff';
    $btnColor = '#303030ff';
    $accentColor = '#03A9F4';
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