<?php
declare(strict_types=1);

namespace App\Libraries;

/**
 * Class Csrf
 *
 * Provides Cross-Site Request Forgery (CSRF) token generation, hidden input field output,
 * meta tag output for AJAX, and timing-attack safe token validation.
 */
class Csrf
{
    /**
     * Session key for storing CSRF token.
     */
    private const SESSION_KEY = '_csrf_token';

    /**
     * Ensures session is started.
     */
    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Generates a new cryptographically secure CSRF token and stores it in session.
     *
     * @param bool $forceNew Whether to force generating a new token even if one exists.
     * @return string The generated CSRF token.
     */
    public static function generateToken(bool $forceNew = false): string
    {
        self::startSession();

        if ($forceNew || empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Retrieves the current CSRF token from session, generating one if missing.
     *
     * @return string
     */
    public static function getToken(): string
    {
        return self::generateToken(false);
    }

    /**
     * Renders a hidden HTML input field containing the CSRF token.
     *
     * @return string
     */
    public static function field(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Renders an HTML meta tag containing the CSRF token for AJAX headers.
     *
     * @return string
     */
    public static function meta(): string
    {
        $token = self::getToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Validates the provided CSRF token against the session token.
     * Checks input token parameter, $_POST, or request headers (X-CSRF-TOKEN, X-XSRF-TOKEN).
     *
     * @param string|null $token Optional token string to validate.
     * @return bool True if valid, false otherwise.
     */
    public static function validate(?string $token = null): bool
    {
        self::startSession();

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? '';
        if ($sessionToken === '') {
            return false;
        }

        if ($token === null || $token === '') {
            $token = $_POST['_csrf_token']
                ?? $_POST['csrf_token']
                ?? $_SERVER['HTTP_X_CSRF_TOKEN']
                ?? $_SERVER['HTTP_X_XSRF_TOKEN']
                ?? '';
        }

        if (!is_string($token) || $token === '') {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Automatically validates state-modifying requests (POST, PUT, PATCH, DELETE).
     *
     * @return bool
     */
    public static function check(): bool
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return self::validate();
        }

        return true;
    }
}
