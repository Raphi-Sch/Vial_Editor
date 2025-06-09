<?php
function file_upload($form_field_name, $destination_directory)
{
    // Return true if everything OK.
    $MAX_FILE_SIZE = 10485760;
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

        // You should also check filesize here. 1MB = 1048576 Bytes
        if ($_FILES[$form_field_name]['size'] > $MAX_FILE_SIZE) {
            throw new RuntimeException("File size is larger than the server allows. (Limit : " . number_format($MAX_FILE_SIZE / 1048576, 2) . " MB, Your file : " . number_format($_FILES[$form_field_name]['size'] / 1048576, 2) . " MB).");
        }

        // Check MIME
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            $finfo->file($_FILES[$form_field_name]['tmp_name']),
            array(
                'vil' => 'application/json',
            ),
            true
        )) {
            throw new RuntimeException('File format not supported.');
        }

        // Name
        $name = sha1_file($_FILES[$form_field_name]['tmp_name']);

        // Move
        if (!move_uploaded_file($_FILES[$form_field_name]['tmp_name'], sprintf("$destination_directory/%s", $name))) {
            throw new RuntimeException('Unable to move the file.');
        }
    } catch (RuntimeException $e) {
        alert_set("Error when receiving the file : " . $e->getMessage());
        return false; // Error occur
    }
    return $name; // Everything OK
}
