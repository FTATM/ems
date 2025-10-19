<?php
require_once __DIR__ . '/openssl.php';

$key = '__Key__'; //enter key here
$token = '__Token__Here___'; // enter token here

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
