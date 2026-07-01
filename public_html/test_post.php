<?php
$_SERVER['REQUEST_URI'] = '/admin/media/delete';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_HOST'] = 'cmsys.wis';
$_POST['id'] = 1;
$_POST['action'] = 'delete';

// We need to bypass Auth?
session_start();
$_SESSION['ICTM_Auth'] = true;
$_SESSION['user_id'] = 1;

require_once __DIR__ . '/index.php';
