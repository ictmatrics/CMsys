<?php
declare(strict_types=1);

require_once APPPATH . 'Helpers/env_helper.php';

// Active database configuration (loads from .env with fallback to legacy config variables)
$legacyDatabase = 'mysql'; // Legacy variable fallback
$database = env('DB_CONNECTION', env('DATABASE_TYPE', $legacyDatabase));

if ($database === 'mysql') {
    define('DATABASE_TYPE', $database);
    define('DB_HOST', env('DB_HOST', 'localhost'));
    define('DB_USER', env('DB_USER', 'root'));
    define('DB_PASS', env('DB_PASS', ''));
    define('DB_NAME', env('DB_NAME', 'cmsys'));

    require_once APPPATH . 'System/Config/QueryBuilder.php';
} elseif ($database === 'sqlite') {
    define('DATABASE_TYPE', $database);

    $legacyDbFile = __DIR__ . '/database.sqlite';
    define('DB_FILE', env('DB_FILE', env('SQLITE_DB', $legacyDbFile)));

    // Automatically create the SQLite file if it does not exist
    if (!file_exists(DB_FILE)) {
        try {
            $db = new PDO('sqlite:' . DB_FILE);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->exec("PRAGMA foreign_keys = ON;"); // Enable foreign key support
            unset($db);
        } catch (PDOException $e) {
            die("Database creation failed: " . $e->getMessage());
        }
    }

    require_once APPPATH . 'System/Config/SqliteBuilder.php';
} else {
    define('DATABASE', '');
}
