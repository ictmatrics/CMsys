<?php
declare(strict_types=1);

// Core compile-time constants updated to use environmental variables with legacy fallbacks.
require_once APPPATH . 'Helpers/env_helper.php';

define('APPKEY', env('APP_KEY', 'licensecodes'));
define('SITENAME', env('SITE_NAME', 'CMsys'));
define('APPVERSION', env('APP_VERSION', '1.0.1'));
define('FRAMEWORK', 'ICTM Framework 4.5.1');
define('DOMAIN', env('DOMAIN', ''));

// Build runtime-dependent BASE_URL once
$protocol = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
) ? 'https://' : 'http://';

// Get the directory and normalize slashes
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

// FIX 1: Strip "public_html" from the internal path so it doesn't leak into your redirects
$scriptDir = preg_replace('#/public_html$#', '', $scriptDir);

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = $protocol . $host . $scriptDir;

// FIX 2: Standardize the trailing slash
$baseUrl = rtrim($baseUrl, '/');

define('BASE_URL', $baseUrl);

// Mailer settings
define('E_HOST', env('MAIL_HOST', 'mail.domain.com'));
define('E_MAIL', env('MAIL_FROM_ADDRESS', 'no-reply@domain.com'));
define('E_NAME', env('MAIL_FROM_NAME', 'CMsys Contact'));
define('E_PORT', env('MAIL_PORT', '465'));
define('E_PASS', env('MAIL_PASSWORD', 'password'));
