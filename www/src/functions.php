<?php

if (!isset($_SESSION['alert'])) {
    $_SESSION['alert'] = null;
}

function alert_set($text)
{
    $_SESSION['alert'] = $text;
}

function alert_clear(){
    $_SESSION['alert'] = null;
}

function alert_get(){
    return $_SESSION['alert'];
}

function alert_is_set(){
    return !empty($_SESSION['alert']);
}

function upload_file_to_memory($form_field_name)
{
    $MAX_FILE_SIZE = 10485760; // (1MB = 1048576 Bytes)
    try {
        // If this request falls under any of them, treat it invalid.
        if (!isset($_FILES[$form_field_name]['error']) || is_array($_FILES[$form_field_name]['error'])) {
            throw new RuntimeException('Invalid input field.');
        }

        // Check $_FILES[$form_field_name]['error'] value.
        switch ($_FILES[$form_field_name]['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file send.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('File size is larger than the server allows.');
            default:
                throw new RuntimeException('Unknown error');
        }

        // Check filesize
        if ($_FILES[$form_field_name]['size'] > $MAX_FILE_SIZE) {
            throw new RuntimeException("File size is larger than the server allows. (Limit : " . number_format($MAX_FILE_SIZE / 1048576, 2) . " MB, Your file : " . number_format($_FILES[$form_field_name]['size'] / 1048576, 2) . " MB).");
        }

        // Check MIME
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === array_search(
            $finfo->file($_FILES[$form_field_name]['tmp_name']),
            array(
                'vil' => 'application/json',
            ),
            true
        )) {
            throw new RuntimeException('File format not supported.');
        }

        // Copy tmp file in memory (PHP Session)
        $_SESSION['vial_editor']['data'] = json_decode(file_get_contents($_FILES[$form_field_name]['tmp_name']), true, 512, JSON_BIGINT_AS_STRING | JSON_OBJECT_AS_ARRAY);

    } catch (RuntimeException $e) {
        alert_set("Error when receiving the file : " . $e->getMessage());
        return false; // Error occur
    }
    return true; // Everything OK
}