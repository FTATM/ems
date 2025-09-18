<?php
session_start();

if (isset($_POST['key'])) {
    $_SESSION[$_POST['key']] = $_POST['value'];
    echo json_encode(["success" => true, "message" => "Success set session " . $_POST['key']]);
}
