<?php
// Core compile-time constants
const APPKEY     = 'licensecodes';
const SITENAME   = 'Learnnia Exam';
const APPVERSION = '1.0.0';
const FRAMEWORK  = 'ICTM Framework 4.0';

// Build runtime-dependent BASE_URL once
$protocol = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
) ? 'https://' : 'http://';

$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl   = $protocol . $host ;
define('BASE_URL', $baseUrl);

// Mailer setup
  define('E_HOST', 'mail.learnnia.com');
  define('E_MAIL', 'no-reply@learnnia.com');
  define('E_NAME', 'Contact Us');
  define('E_PORT', '465');
  define('E_PASS', '4bELrn(c#R8(');