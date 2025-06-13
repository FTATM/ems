<?php
session_start();


function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../pages/login.php");
        exit();
    }
}

function logout()
{
    // Logging logout
    if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
        include "../config/connect.php";
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $log_sql = "INSERT INTO log_login (user_id, username, action, ip_address, user_agent) 
                    VALUES ('$user_id', '$username', 'logout', '$ip_address', '$user_agent')";

        $conn->query($log_sql);
    }

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
function buildLangSwitchLink($targetLang) {
    $query = $_GET;
    $query['lang'] = $targetLang;
    return '?' . http_build_query($query);
}


// ตรวจสอบการเปลี่ยนภาษาจาก URL
if (isset($_GET['theme'])) {
    $_SESSION['theme'] = $_GET['theme'];
}

// ตั้งค่าเริ่มต้นถ้ายังไม่มี session
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';
}

// สไตล์แบบ inline (ง่ายสุด)
$theme = $_SESSION['theme'];
$bg = $theme === 'dark' ? '#666' : '#fdfeff';
$bgsec = $theme === 'dark' ? '#aaa' : "#fff";
$secon = $theme === 'dark' ? '#bbb' : '#555';
$text = $theme === 'dark' ? '#eee' : '#000';
$btnColor = $theme === 'dark' ? '#777' : '#999';

function buildthemeSwitchLink($targetheme) {
    $query = $_GET;
    $query['theme'] = $targetheme;
    return '?' . http_build_query($query);
}