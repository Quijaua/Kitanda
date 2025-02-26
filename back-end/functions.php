<?php
function registerLog($log){
    $now = new DateTime();
    $log_file_name = $now->format('Y-m-d-His');
    $log_file = $log_file_name . '.log';
    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/log/' . $log_file, 'w');
    error_log($log, 3, $_SERVER['DOCUMENT_ROOT'] . '/log/' . $log_file);
    fclose($_SERVER['DOCUMENT_ROOT'] . '/log/' . $log_file);

    return NULL;
}