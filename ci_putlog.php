<?php


/**
 * ci style 로그기록
 * @param string $udf
 * @param string $msg
 */
function putlog($udf = 'udf', $msg = '')
{
    $CI =& get_instance();
    $path = APPPATH . 'logs/';
    $filename = $path . $CI->uri->segment(1) . '-' . $udf . '-' . date('Y-m-d') . '.php';

    $header = null;
    if (!file_exists($filename)) {
        $header = <<<TEXT
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


TEXT;
    }


    $fp = fopen($filename, 'a');
    if (!empty($header)) {
        fwrite($fp, $header);
    }
    fwrite($fp, date('Y-m-d H:i:s') . ' --> ' . $msg . "\n");
    fclose($fp);

    if (!empty($header)) {
        chmod($filename, 0777);
    }
}