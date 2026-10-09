<?php
declare(strict_types=1);

use App\Libraries\DatabasePrefix;

if (!function_exists('db_prefix')) {
    /**
     * Return the table name prefixed with the active system prefix (default: 'tblcmsys_').
     * If called with empty string, returns the active prefix itself.
     *
     * @param string $table Table name to prefix.
     * @return string Prefixed table name.
     */
    function db_prefix(string $table = ''): string
    {
        return DatabasePrefix::table($table);
    }
}
