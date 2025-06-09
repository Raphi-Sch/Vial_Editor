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