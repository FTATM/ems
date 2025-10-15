<?php
function decode_aes_gcm(string $token, string $binaryKey)
{

    $cipher = 'aes-256-gcm';
    $data = base64_decode($token, true);
    if ($data === false) return false;

    $ivlen = openssl_cipher_iv_length($cipher);
    $key = hash('sha256', $binaryKey, true);
    $taglen = 16;
    if (strlen($data) < ($ivlen + $taglen)) return false;

    $iv = substr($data, 0, $ivlen);
    $tag = substr($data, $ivlen, $taglen);
    $ciphertext = substr($data, $ivlen + $taglen);

    $plain = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
    return $plain === false ? false : $plain;
}
