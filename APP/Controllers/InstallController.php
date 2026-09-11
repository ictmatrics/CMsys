<?php
declare(strict_types=1);

namespace App\Controllers;

use System\Config\Controller;

class InstallController extends Controller
{
    public function __construct()
    {
    }

    public function index(): void
    {
        // If .env exists AND setup assets have already been purged, redirect out of installer
        if (file_exists(APPPATH . '.env') && !is_dir(APPPATH . 'Views/install')) {
            redirect('login');
            exit();
        }

        $envMissing = !file_exists(APPPATH . '.env');
        $isDeployed = false;
        $error = null;

        // If .env exists, check database deployment status
        if (!$envMissing) {
            try {
                $db = db();
                if (defined('DATABASE_TYPE') && DATABASE_TYPE === 'sqlite') {
                    $check = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='site_options'");
                } else {
                    $check = $db->query("SHOW TABLES LIKE 'site_options'");
                }

                if (is_array($check) && !empty($check)) {
                    $isDeployed = true;
                }
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        echo $this->view('install/install', [
            'error' => $error,
            'is_deployed' => $isDeployed,
            'env_missing' => $envMissing
        ]);
    }

    public function run(): void
    {
        $adminUser = validate_data($_POST['admin_user'] ?? '');
        $adminEmail = validate_data($_POST['admin_email'] ?? '');
        $adminPass = $_POST['admin_pass'] ?? '';

        if (empty($adminUser) || empty($adminEmail) || empty($adminPass)) {
            flash('error_msg', 'All fields are required!', 'alert alert-danger');
            redirect('install');
            exit();
        }

        // Auto-provision APP/.env if it does not exist
        if (!file_exists(APPPATH . '.env')) {
            $dbConnection = validate_data($_POST['db_connection'] ?? 'mysql');
            $dbHost = validate_data($_POST['db_host'] ?? 'localhost');
            $dbUser = validate_data($_POST['db_user'] ?? 'root');
            $dbPass = $_POST['db_pass'] ?? '';
            $dbName = validate_data($_POST['db_name'] ?? 'cmsys');
            $siteName = validate_data($_POST['site_name'] ?? 'CMsys');
            $appUrl = validate_data($_POST['app_url'] ?? (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : 'http://localhost'));

            $appKey = bin2hex(random_bytes(16));
            $envContent = "# CMsys Environment Configuration (ICTM Framework 4.5)\n\n"
                . "APP_KEY=\"{$appKey}\"\n"
                . "SITE_NAME=\"{$siteName}\"\n"
                . "APP_VERSION=\"1.2.0\"\n"
                . "APP_URL=\"{$appUrl}\"\n\n"
                . "# Database Configuration (mysql or sqlite)\n"
                . "DB_CONNECTION=\"{$dbConnection}\"\n"
                . "DB_HOST=\"{$dbHost}\"\n"
                . "DB_USER=\"{$dbUser}\"\n"
                . "DB_PASS=\"{$dbPass}\"\n"
                . "DB_NAME=\"{$dbName}\"\n\n"
                . "# Mailer Settings\n"
                . "MAIL_HOST=\"mail.domain.com\"\n"
                . "MAIL_FROM_ADDRESS=\"no-reply@domain.com\"\n"
                . "MAIL_FROM_NAME=\"CMsys Contact\"\n"
                . "MAIL_PORT=\"465\"\n"
                . "MAIL_PASSWORD=\"password\"\n";

            file_put_contents(APPPATH . '.env', $envContent);

            // Re-load environment variables into runtime
            \App\Libraries\Env::load(APPPATH . '.env');
        }

        try {
            $db = db();
            $isSqlite = defined('DATABASE_TYPE') && DATABASE_TYPE === 'sqlite';

            // 1. Create Core Tables using relocated schema.sql if available
            $schemaFile = APPPATH . 'Views/install/schema.sql';
            if (file_exists($schemaFile)) {
                $sql = (string)file_get_contents($schemaFile);
                $queries = $this->parseSqlStatements($sql, $isSqlite);
                foreach ($queries as $q) {
                    $db->query($q);
                }
            } else {
                // Fallback table creation
                $queries = $this->getFallbackQueries($isSqlite);
                foreach ($queries as $q) {
                    $db->query($q);
                }
            }

            // 2. Create or update Admin User
            $hashedPass = password_hash($adminPass, PASSWORD_DEFAULT);
            $existingUser = $db->table('users')->where('username', '=', $adminUser)->first();
            if ($existingUser) {
                $db->table('users')->where('id', '=', (int)$existingUser['id'])->update([
                    'email' => $adminEmail,
                    'password' => $hashedPass,
                    'role' => 'admin',
                    'status' => 1
                ]);
            } else {
                $db->table('users')->insert([
                    'username' => $adminUser,
                    'email' => $adminEmail,
                    'password' => $hashedPass,
                    'role' => 'admin',
                    'status' => 1
                ]);
            }

            // 3. Populate default settings in site_options
            $defaultSettings = [
                'site_title' => 'CMsys Website',
                'site_description' => 'A Content Management System built on ICTM Framework',
                'site_email' => $adminEmail,
                'maintenance_mode' => '0',
                'frontend_theme' => 'classic',
                'backend_theme' => 'classic',
                'posts_per_page' => '10',
                'smtp_host' => '',
                'smtp_user' => '',
                'smtp_pass' => '',
                'smtp_port' => '587',
                'smtp_encryption' => 'tls',
                'custom_css' => '',
                'custom_js_header' => '',
                'custom_js_footer' => '',
                'widget_settings' => (string)json_encode([
                    'sidebar_widgets' => ['search', 'categories', 'tags'],
                    'sidebar_layout' => 'right'
                ])
            ];

            foreach ($defaultSettings as $name => $val) {
                $existingOption = $db->table('site_options')->where('option_name', '=', $name)->first();
                if ($existingOption) {
                    $db->table('site_options')->where('option_name', '=', $name)->update([
                        'option_value' => $val
                    ]);
                } else {
                    $db->table('site_options')->insert([
                        'option_name' => $name,
                        'option_value' => $val
                    ]);
                }
            }

            flash('success_msg', 'Deployment successful! Please purge setup resources below to unlock the system.', 'alert alert-success');
            redirect('install');
            exit();
        } catch (\Throwable $e) {
            flash('error_msg', 'Installation failed: ' . $e->getMessage(), 'alert alert-danger');
            redirect('install');
            exit();
        }
    }

    public function purge(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('install');
            exit();
        }

        // Delete /app/views/install directory and all its contents
        $installViewDir = APPPATH . 'Views/install';
        if (is_dir($installViewDir)) {
            $this->recursiveDelete($installViewDir);
        }

        // Clean up legacy install view if present
        if (file_exists(APPPATH . 'Views/install.php')) {
            @unlink(APPPATH . 'Views/install.php');
        }

        // Clean up root schema.sql if present
        if (file_exists(ROOTPATH . 'schema.sql')) {
            @unlink(ROOTPATH . 'schema.sql');
        }

        // Remove InstallController.php on shutdown
        $controllerFile = __FILE__;
        register_shutdown_function(function () use ($controllerFile) {
            if (file_exists($controllerFile)) {
                @unlink($controllerFile);
            }
        });

        flash('success_msg', 'Setup resources purged successfully. All system areas are now unlocked!', 'alert alert-success');
        redirect('login');
        exit();
    }

    /**
     * Clean and extract executable SQL statements from raw SQL string.
     *
     * @return array<int, string>
     */
    private function parseSqlStatements(string $sql, bool $isSqlite = false): array
    {
        // Strip block comments /* ... */
        $clean = preg_replace('!/\*.*?\*/!s', '', $sql);
        if ($clean === null) {
            $clean = $sql;
        }

        // Strip single line comments (-- and #)
        $lines = explode("\n", $clean);
        $filtered = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) {
                continue;
            }
            $filtered[] = $line;
        }

        $rawStatements = explode(';', implode("\n", $filtered));
        $queries = [];

        foreach ($rawStatements as $stmt) {
            $q = trim($stmt);
            if ($q === '') {
                continue;
            }

            if ($isSqlite) {
                if (stripos($q, 'FOREIGN_KEY_CHECKS') !== false) {
                    continue;
                }
                $q = preg_replace('/ENGINE=[A-Za-z0-9]+/i', '', $q) ?? $q;
                $q = preg_replace('/DEFAULT\s+CHARSET=[A-Za-z0-9]+/i', '', $q) ?? $q;
                $q = preg_replace('/COLLATE=[A-Za-z0-9_]+/i', '', $q) ?? $q;
                $q = preg_replace('/INT\s+AUTO_INCREMENT\s+PRIMARY\s+KEY/i', 'INTEGER PRIMARY KEY AUTOINCREMENT', $q) ?? $q;
                $q = trim($q);
                if ($q === '') {
                    continue;
                }
            }

            $queries[] = $q;
        }

        return $queries;
    }

    /**
     * Fallback queries when schema.sql is not present.
     *
     * @return array<int, string>
     */
    private function getFallbackQueries(bool $isSqlite = false): array
    {
        if ($isSqlite) {
            return [
                "CREATE TABLE IF NOT EXISTS site_options (id INTEGER PRIMARY KEY AUTOINCREMENT, option_name VARCHAR(255) UNIQUE NOT NULL, option_value TEXT NOT NULL)",
                "CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR(100) UNIQUE NOT NULL, email VARCHAR(191) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, status TINYINT DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
                "CREATE TABLE IF NOT EXISTS posts (id INTEGER PRIMARY KEY AUTOINCREMENT, title VARCHAR(255) NOT NULL, slug VARCHAR(255) UNIQUE NOT NULL, content TEXT NULL, excerpt TEXT NULL, type VARCHAR(50) DEFAULT 'post', status VARCHAR(50) DEFAULT 'draft', publish_date DATETIME NULL, author_id INT NULL, parent_id INT NULL, template VARCHAR(255) NULL, views INT DEFAULT 0 NOT NULL, is_trending TINYINT DEFAULT 0 NOT NULL, related_posts TEXT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL)",
                "CREATE TABLE IF NOT EXISTS post_meta (id INTEGER PRIMARY KEY AUTOINCREMENT, post_id INT NOT NULL, meta_key VARCHAR(255) NOT NULL, meta_value TEXT NULL, FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE, UNIQUE (post_id, meta_key))",
                "CREATE TABLE IF NOT EXISTS taxonomies (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, UNIQUE (type, slug))",
                "CREATE TABLE IF NOT EXISTS post_taxonomies (post_id INT NOT NULL, taxonomy_id INT NOT NULL, PRIMARY KEY (post_id, taxonomy_id), FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE, FOREIGN KEY (taxonomy_id) REFERENCES taxonomies(id) ON DELETE CASCADE)",
                "CREATE TABLE IF NOT EXISTS comments (id INTEGER PRIMARY KEY AUTOINCREMENT, post_id INT NOT NULL, author_name VARCHAR(100) NOT NULL, author_email VARCHAR(191) NOT NULL, content TEXT NOT NULL, status VARCHAR(50) DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE)",
                "CREATE TABLE IF NOT EXISTS media_library (id INTEGER PRIMARY KEY AUTOINCREMENT, original_name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, mime_type VARCHAR(100) NOT NULL, dimensions VARCHAR(50) NULL, user_id INT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL)",
                "CREATE TABLE IF NOT EXISTS menus (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255) NOT NULL, location VARCHAR(50) NULL)",
                "CREATE TABLE IF NOT EXISTS menu_items (id INTEGER PRIMARY KEY AUTOINCREMENT, menu_id INT NOT NULL, parent_id INT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, url VARCHAR(255) NULL, object_id INT NULL, menu_order INT DEFAULT 0, FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE)",
                "CREATE TABLE IF NOT EXISTS modules (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(100) UNIQUE NOT NULL, version VARCHAR(50) NOT NULL, is_active TINYINT DEFAULT 0)",
                "CREATE TABLE IF NOT EXISTS themes (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(100) UNIQUE NOT NULL, version VARCHAR(50) NOT NULL, is_active TINYINT DEFAULT 0, scope VARCHAR(50) DEFAULT 'frontend')"
            ];
        }

        return [
            "CREATE TABLE IF NOT EXISTS `site_options` (`id` INT AUTO_INCREMENT PRIMARY KEY, `option_name` VARCHAR(255) UNIQUE NOT NULL, `option_value` LONGTEXT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `users` (`id` INT AUTO_INCREMENT PRIMARY KEY, `username` VARCHAR(100) UNIQUE NOT NULL, `email` VARCHAR(191) UNIQUE NOT NULL, `password` VARCHAR(255) NOT NULL, `role` VARCHAR(50) NOT NULL, `status` TINYINT DEFAULT 1, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `posts` (`id` INT AUTO_INCREMENT PRIMARY KEY, `title` VARCHAR(255) NOT NULL, `slug` VARCHAR(255) UNIQUE NOT NULL, `content` LONGTEXT NULL, `excerpt` TEXT NULL, `type` VARCHAR(50) DEFAULT 'post', `status` VARCHAR(50) DEFAULT 'draft', `publish_date` DATETIME NULL, `author_id` INT NULL, `parent_id` INT NULL, `template` VARCHAR(255) NULL, `views` INT DEFAULT 0 NOT NULL, `is_trending` TINYINT DEFAULT 0 NOT NULL, `related_posts` TEXT NULL, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `post_meta` (`id` INT AUTO_INCREMENT PRIMARY KEY, `post_id` INT NOT NULL, `meta_key` VARCHAR(255) NOT NULL, `meta_value` LONGTEXT NULL, FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE, UNIQUE KEY `post_meta_key` (`post_id`, `meta_key`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `taxonomies` (`id` INT AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) NOT NULL, `slug` VARCHAR(255) NOT NULL, `type` VARCHAR(50) NOT NULL, UNIQUE KEY `type_slug` (`type`, `slug`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `post_taxonomies` (`post_id` INT NOT NULL, `taxonomy_id` INT NOT NULL, PRIMARY KEY (`post_id`, `taxonomy_id`), FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE, FOREIGN KEY (`taxonomy_id`) REFERENCES `taxonomies`(`id`) ON DELETE CASCADE)",
            "CREATE TABLE IF NOT EXISTS `comments` (`id` INT AUTO_INCREMENT PRIMARY KEY, `post_id` INT NOT NULL, `author_name` VARCHAR(100) NOT NULL, `author_email` VARCHAR(191) NOT NULL, `content` TEXT NOT NULL, `status` VARCHAR(50) DEFAULT 'pending', `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `media_library` (`id` INT AUTO_INCREMENT PRIMARY KEY, `original_name` VARCHAR(255) NOT NULL, `path` VARCHAR(255) NOT NULL, `mime_type` VARCHAR(100) NOT NULL, `dimensions` VARCHAR(50) NULL, `user_id` INT NULL, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `menus` (`id` INT AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) NOT NULL, `location` VARCHAR(50) NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `menu_items` (`id` INT AUTO_INCREMENT PRIMARY KEY, `menu_id` INT NOT NULL, `parent_id` INT NULL, `title` VARCHAR(255) NOT NULL, `type` VARCHAR(50) NOT NULL, `url` VARCHAR(255) NULL, `object_id` INT NULL, `menu_order` INT DEFAULT 0, FOREIGN KEY (`menu_id`) REFERENCES `menus`(`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `modules` (`id` INT AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(100) UNIQUE NOT NULL, `version` VARCHAR(50) NOT NULL, `is_active` TINYINT DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `themes` (`id` INT AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(100) UNIQUE NOT NULL, `version` VARCHAR(50) NOT NULL, `is_active` TINYINT DEFAULT 0, `scope` VARCHAR(50) DEFAULT 'frontend') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];
    }

    private function recursiveDelete(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir) ?: [], ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->recursiveDelete($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}

