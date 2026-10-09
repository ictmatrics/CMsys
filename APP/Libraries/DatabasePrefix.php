<?php
declare(strict_types=1);

namespace App\Libraries;

/**
 * Class DatabasePrefix
 *
 * Manages customizable database table prefixes across the architecture.
 * Default prefix is 'tblcmsys_'.
 */
class DatabasePrefix
{
    private static ?string $prefix = null;

    /**
     * Get the active database prefix from configuration or fallback to 'tblcmsys_'.
     */
    public static function get(): string
    {
        if (self::$prefix === null) {
            $configured = env('DB_PREFIX', 'tblcmsys_');
            self::$prefix = is_string($configured) ? $configured : 'tblcmsys_';
        }
        return self::$prefix;
    }

    /**
     * Explicitly override the database prefix (e.g. during installer setup).
     */
    public static function set(?string $prefix): void
    {
        self::$prefix = $prefix;
    }

    /**
     * Prepend the active database prefix to a table name if not already prefixed.
     *
     * @param string $table Target table name (e.g. 'posts' or 'tblcmsys_posts')
     * @return string Fully-qualified table name with prefix
     */
    public static function table(string $table = ''): string
    {
        $p = self::get();
        if ($table === '') {
            return $p;
        }

        // Avoid double prefixing
        if ($p === '' || str_starts_with($table, $p)) {
            return $table;
        }

        return $p . $table;
    }
}
