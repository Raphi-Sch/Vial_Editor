<?php

if (!isset($_SESSION['alert'])) {
    $_SESSION['alert'] = null;
}

function alert_set($text)
{
    $_SESSION['alert'] = $text;
}

function alert_clear()
{
    $_SESSION['alert'] = null;
}

function alert_get()
{
    return $_SESSION['alert'];
}

function alert_is_set()
{
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

function swap_layout($layout, $A, $B)
{
    if ($A == $B) {
        alert_set("Can't swap identical layer");
        return false;
    }

    $_SESSION['vial_editor']['last_change']['text'] = "Layout $A and $B got swapped";
    $_SESSION['vial_editor']['last_change']['id_A'] = $A;
    $_SESSION['vial_editor']['last_change']['id_B'] = $B;

    // Copy A to tmp
    $_SESSION['vial_editor']['last_change']['old_A'] = $_SESSION['vial_editor']['data'][$layout][$A];
    $tmp = $_SESSION['vial_editor']['data'][$layout][$A];

    // Copy B to A
    $_SESSION['vial_editor']['last_change']['old_B'] = $_SESSION['vial_editor']['data'][$layout][$B];
    $_SESSION['vial_editor']['data'][$layout][$A] = $_SESSION['vial_editor']['data'][$layout][$B];
    $_SESSION['vial_editor']['last_change']['new_A'] = $_SESSION['vial_editor']['data'][$layout][$A];

    // Copy tmp to B
    $_SESSION['vial_editor']['data'][$layout][$B] = $tmp;
    $_SESSION['vial_editor']['last_change']['new_B'] = $_SESSION['vial_editor']['data'][$layout][$B];

    return true;
}

function display_layout($data)
{
    $tmp = "";
    foreach ($data as $element) {
        $HTML_th = "";
        $HTML_td = "";
        foreach ($element as $key => $value) {
            $HTML_th .= "<th>$key</th>";
            $HTML_td .= "<td>$value</td>";
        }
        $tmp .= "<table><tr>$HTML_th</tr><tr>$HTML_td</tr></table><br/>";
    }

    return $tmp;
}
