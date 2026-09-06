<?php
declare(strict_types=1);

namespace App\Libraries;

/**
 * Class Env
 *
 * A lightweight parser for loading environment variables from a .env file.
 */
class Env
{
    /**
     * Load environment variables from the given file path.
     *
     * @param string $path Absolute path to the .env file.
     */
    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Strip surrounding quotes
            if (preg_match('/^"([^"]*)"$/', $value, $matches) || preg_match('/^\'([^\']*)\'$/', $value, $matches)) {
                $value = $matches[1];
            }

            // Set to $_ENV, $_SERVER, and system getenv
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }
            if (!array_key_exists($key, $_SERVER)) {
                $_SERVER[$key] = $value;
            }
            putenv("{$key}={$value}");
        }
    }

    /**
     * Get the value of an environment variable.
     *
     * @param string $key Environmental key.
     * @param mixed $default Fallback value if key is not defined.
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        // Handle string representation of boolean values
        return match (strtolower($value)) {
            'true'  => true,
            'false' => false,
            'null'  => null,
            default => $value,
        };
    }
}
