<?php

declare(strict_types=1);

/**
 * --------------------------------------------------------------------------
 * Database Helper
 * --------------------------------------------------------------------------
 *
 * Provides a simple factory function to obtain a fresh QueryBuilder or SqliteBuilder instance
 * depending on the active database connection.
 *
 * Usage (anywhere in the application):
 *
 *   $db = db();
 *   $rows = db()->query("SELECT * FROM site_options");
 */

use System\Config\QueryBuilder;
use System\Config\SqliteBuilder;

/**
 * Return a new QueryBuilder or SqliteBuilder instance.
 *
 * @return \System\Config\QueryBuilder|\System\Config\SqliteBuilder
 */
function db(): QueryBuilder|SqliteBuilder
{
    if (defined('DATABASE_TYPE') && DATABASE_TYPE === 'sqlite') {
        return new SqliteBuilder();
    }
    return new QueryBuilder();
}
