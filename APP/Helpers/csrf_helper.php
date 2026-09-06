<?php
declare(strict_types=1);

if (defined('APPPATH') && file_exists(APPPATH . 'Libraries/Csrf.php')) {
    require_once APPPATH . 'Libraries/Csrf.php';
}

use App\Libraries\Csrf;

/**
 * Global CSRF helper functions.
 */

if (!function_exists('csrf_token')) {
    /**
     * Get active CSRF token.
     */
    function csrf_token(): string
    {
        return Csrf::getToken();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Render hidden CSRF token input field.
     */
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('csrf_meta')) {
    /**
     * Render HTML meta tag for CSRF token.
     */
    function csrf_meta(): string
    {
        return Csrf::meta();
    }
}

if (!function_exists('csrf_validate')) {
    /**
     * Validate CSRF token.
     */
    function csrf_validate(?string $token = null): bool
    {
        return Csrf::validate($token);
    }
}
