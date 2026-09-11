<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * --------------------------------------------------------------------------
 * ICTM_Auth Filter
 * --------------------------------------------------------------------------
 *
 * Centralises the authentication & RBAC guard that was previously duplicated
 * inside every controller __construct().
 *
 * Usage (inside any controller constructor):
 *
 *   \App\Filters\ICTM_Auth::guard();               // require any logged-in user
 *   \App\Filters\ICTM_Auth::guard('admin');         // require admin role
 *   \App\Filters\ICTM_Auth::guard('admin','editor');// require admin or editor role
 */
class ICTM_Auth
{
    /**
     * Guard the current request.
     *
     * - Ensures a PHP session is active.
     * - Redirects unauthenticated visitors to /login.
     * - Redirects users whose role is not in $allowedRoles to the homepage.
     *   If $allowedRoles is empty, any authenticated role is accepted.
     *
     * @param string ...$allowedRoles  Optional list of permitted roles.
     */
    public static function guard(string ...$allowedRoles): void
    {
        // Ensure session is running (flash_helper already calls session_start(),
        // but this guard is safe to call from any context).
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Must be authenticated
        if (!isset($_SESSION['ICTM_Auth'])) {
            redirect('login');
            exit();
        }

        $role = $_SESSION['ICTM_Auth']['role'] ?? '';

        // 2. Subscribers are never allowed into the admin area
        if ($role === 'subscriber') {
            redirect('');
            exit();
        }

        // 3. Role-based restriction (if specific roles were requested)
        if (!empty($allowedRoles) && !in_array($role, $allowedRoles, true)) {
            flash('error_msg', 'Access denied: Requires administrator privileges.', 'alert alert-danger');
            redirect('admin/dashboard');
            exit();
        }
    }
}
