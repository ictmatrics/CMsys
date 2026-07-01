<?php

declare(strict_types=1);

/**
 * --------------------------------------------------------------------------
 * Database Helper
 * --------------------------------------------------------------------------
 *
 * Provides a simple factory function to obtain a fresh QueryBuilder instance.
 *
 * Usage (anywhere in the application):
 *
 *   $db = db();
 *   $rows = db()->query("SHOW TABLES LIKE 'site_options'");
 */

/**
 * Return a new QueryBuilder instance.
 *
 * @return \System\Config\QueryBuilder
 */
function db(): \System\Config\QueryBuilder
{
    return new \System\Config\QueryBuilder();
}
