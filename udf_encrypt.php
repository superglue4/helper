<?php
/**
 * @param $str
 * @param $key
 * @return false|string
 */
function udf_encrypt($str = '', $key = '')
{
    return base64_encode(openssl_encrypt($key . $str, 'aes-256-cbc', $key, false, str_repeat(chr(0), 16)));
}

/**
 * @param $str
 * @param $key
 * @return false|string
 */
function udf_decrypt($str = '', $key = '')
{
    return str_replace($key, '', openssl_decrypt(base64_decode($str), 'aes-256-cbc', $key, false, str_repeat(chr(0), 16)));
}