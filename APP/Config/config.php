<?php
$database = 'mysql'; // mysql or sqlite or blank for not using database


if ($database == 'mysql') {
  define('DATABASE_TYPE', $database);
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASS', '');
  define('DB_NAME', 'cmsys');
} elseif ($database == 'sqlite') {
  define('DATABASE_TYPE', $database);
  define('DB_FILE', __DIR__ . '/database.sqlite');

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
} else {
  define('DATABASE', '');
}
