<?php
declare(strict_types=1);

namespace App\Controllers;

use System\Config\Controller;

class InstallController extends Controller
{
    public function __construct() {}

    public function index()
    {
        // Try connecting to database and check if option table exists
        try {
            $db = db();
            if ($db->query("SHOW TABLES LIKE 'site_options'")) {
                $check = $db->get();
                if (!empty($check)) {
                    // Already installed! Redirect to login
                    redirect('login');
                    exit();
                }
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        echo $this->view('install', ['error' => $error ?? null]);
    }

    public function run()
    {
        $adminUser = validate_data($_POST['admin_user'] ?? '');
        $adminEmail = validate_data($_POST['admin_email'] ?? '');
        $adminPass = $_POST['admin_pass'] ?? '';

        if (empty($adminUser) || empty($adminEmail) || empty($adminPass)) {
            flash('error_msg', 'All fields are required!', 'alert alert-danger');
            redirect('install');
            exit();
        }

        try {
            $db = db();

            // 1. Create Core Tables
            $queries = [
                "CREATE TABLE IF NOT EXISTS `site_options` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `option_name` VARCHAR(255) UNIQUE NOT NULL,
                    `option_value` LONGTEXT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `users` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `username` VARCHAR(100) UNIQUE NOT NULL,
                    `email` VARCHAR(191) UNIQUE NOT NULL,
                    `password` VARCHAR(255) NOT NULL,
                    `role` VARCHAR(50) NOT NULL, -- admin, editor, subscriber
                    `status` TINYINT DEFAULT 1,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `posts` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `title` VARCHAR(255) NOT NULL,
                    `slug` VARCHAR(255) UNIQUE NOT NULL,
                    `content` LONGTEXT NULL,
                    `excerpt` TEXT NULL,
                    `type` VARCHAR(50) DEFAULT 'post', -- post, page, custom post type
                    `status` VARCHAR(50) DEFAULT 'draft', -- draft, published, expired, scheduled
                    `publish_date` DATETIME NULL,
                    `author_id` INT NULL,
                    `parent_id` INT NULL,
                    `template` VARCHAR(255) NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `post_meta` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `post_id` INT NOT NULL,
                    `meta_key` VARCHAR(255) NOT NULL,
                    `meta_value` LONGTEXT NULL,
                    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
                    UNIQUE KEY `post_meta_key` (`post_id`, `meta_key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `taxonomies` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(255) NOT NULL,
                    `slug` VARCHAR(255) NOT NULL,
                    `type` VARCHAR(50) NOT NULL, -- category, tag
                    UNIQUE KEY `type_slug` (`type`, `slug`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `post_taxonomies` (
                    `post_id` INT NOT NULL,
                    `taxonomy_id` INT NOT NULL,
                    PRIMARY KEY (`post_id`, `taxonomy_id`),
                    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
                    FOREIGN KEY (`taxonomy_id`) REFERENCES `taxonomies`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `comments` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `post_id` INT NOT NULL,
                    `author_name` VARCHAR(100) NOT NULL,
                    `author_email` VARCHAR(191) NOT NULL,
                    `content` TEXT NOT NULL,
                    `status` VARCHAR(50) DEFAULT 'pending', -- pending, approved, spam
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `media_library` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `original_name` VARCHAR(255) NOT NULL,
                    `path` VARCHAR(255) NOT NULL,
                    `mime_type` VARCHAR(100) NOT NULL,
                    `dimensions` VARCHAR(50) NULL,
                    `user_id` INT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `menus` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(255) NOT NULL,
                    `location` VARCHAR(50) NULL -- main, footer
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `menu_items` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `menu_id` INT NOT NULL,
                    `parent_id` INT NULL,
                    `title` VARCHAR(255) NOT NULL,
                    `type` VARCHAR(50) NOT NULL, -- custom, page, category, post, tag
                    `url` VARCHAR(255) NULL,
                    `object_id` INT NULL,
                    `menu_order` INT DEFAULT 0,
                    FOREIGN KEY (`menu_id`) REFERENCES `menus`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `modules` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(100) UNIQUE NOT NULL,
                    `version` VARCHAR(50) NOT NULL,
                    `is_active` TINYINT DEFAULT 0
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `themes` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(100) UNIQUE NOT NULL,
                    `version` VARCHAR(50) NOT NULL,
                    `is_active` TINYINT DEFAULT 0,
                    `scope` VARCHAR(50) DEFAULT 'frontend' -- frontend, backend
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
            ];

            foreach ($queries as $q) {
                $db->query($q);
            }

            // 2. Create Admin User
            $hashedPass = password_hash($adminPass, PASSWORD_DEFAULT);
            $db->table('users')->insert([
                'username' => $adminUser,
                'email' => $adminEmail,
                'password' => $hashedPass,
                'role' => 'admin',
                'status' => 1
            ]);

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
                'widget_settings' => json_encode([
                    'sidebar_widgets' => ['search', 'categories', 'tags'],
                    'sidebar_layout' => 'right'
                ])
            ];

            foreach ($defaultSettings as $name => $val) {
                $db->table('site_options')->insert([
                    'option_name' => $name,
                    'option_value' => $val
                ]);
            }

            flash('success_msg', 'CMsys installed successfully! You can now log in.', 'alert alert-success');
            redirect('login');
            exit();

        } catch (\Throwable $e) {
            flash('error_msg', 'Installation failed: ' . $e->getMessage(), 'alert alert-danger');
            redirect('install');
            exit();
        }
    }
}
