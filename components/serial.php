<?php
require_once __DIR__ . '/openssl.php';

$key = 'dZVwhLkkd5SsPEh5z_Y6TAnS5fVdaAVm';
$token = '9CjYDECU45VpJJ0fp/se/qtEBB0O49KdtlNxM7bgDwFBoHGSl4y0/fVfOK5Zky/GRhUK0O4AJsxhLuZg8npeoA==';  //key encode here USB\VID_0BDA&PID_8179\00E04C0001

function checktoken()
{
    global $key, $token;
    $instanceId = decode_aes_gcm($token, $key);
    if ($instanceId === false) {
        return false;
        exit(1);
    }
    $instanceId = html_entity_decode($instanceId, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $psCommand = "powershell -NoProfile -Command \"Try { Get-PnpDevice -InstanceId '$instanceId' -ErrorAction Stop | Select-Object -ExpandProperty Status } Catch { Write-Output 'NotFound' }\"";
    $output = trim(shell_exec($psCommand));

    if ($output !== 'Unknown' && $output !== 'NotFound') {
        return true;
        exit(1);
    } else {
        return false;
        exit(1);
    }
}
