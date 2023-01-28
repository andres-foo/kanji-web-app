<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

session_start();
if(!isset($_SESSION['simple']) || $_SESSION['simple'] == 'on') {
    $_SESSION['simple'] = 'off';
} else {
    $_SESSION['simple'] = 'on';
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;