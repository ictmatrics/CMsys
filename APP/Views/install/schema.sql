-- =====================================================================
-- CMsys Database Schema
-- Architecture: ICTM Framework 4.5 / 4.0.1
-- Location: /app/views/install/schema.sql
-- Description: Clean deployment DDL structure without default seed data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- Table: site_options
-- Stores system and site configuration settings
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `site_options` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `option_name` VARCHAR(255) UNIQUE NOT NULL,
    `option_value` LONGTEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: users
-- User accounts, roles, and authentication records
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) UNIQUE NOT NULL,
    `email` VARCHAR(191) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(50) NOT NULL DEFAULT 'subscriber', -- admin, editor, subscriber
    `status` TINYINT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: posts
-- Content entries: standard posts, static pages, and custom post types
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `posts` (
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
    `views` INT DEFAULT 0 NOT NULL,
    `is_trending` TINYINT DEFAULT 0 NOT NULL,
    `related_posts` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: post_meta
-- Extensible metadata and block configurations for posts and pages
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `post_meta` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT NOT NULL,
    `meta_key` VARCHAR(255) NOT NULL,
    `meta_value` LONGTEXT NULL,
    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `post_meta_key` (`post_id`, `meta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: taxonomies
-- Categories, tags, and custom classifications
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `taxonomies` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `type` VARCHAR(50) NOT NULL, -- category, tag
    UNIQUE KEY `type_slug` (`type`, `slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: post_taxonomies
-- Relationship mapping between posts and taxonomies
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `post_taxonomies` (
    `post_id` INT NOT NULL,
    `taxonomy_id` INT NOT NULL,
    PRIMARY KEY (`post_id`, `taxonomy_id`),
    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`taxonomy_id`) REFERENCES `taxonomies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: comments
-- User comments, moderation status, and post references
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT NOT NULL,
    `author_name` VARCHAR(100) NOT NULL,
    `author_email` VARCHAR(191) NOT NULL,
    `content` TEXT NOT NULL,
    `status` VARCHAR(50) DEFAULT 'pending', -- pending, approved, spam
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: media_library
-- Uploaded media files, MIME types, and dimensions
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `media_library` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `original_name` VARCHAR(255) NOT NULL,
    `path` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `dimensions` VARCHAR(50) NULL,
    `user_id` INT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: menus
-- Navigation menu containers
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `menus` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `location` VARCHAR(50) NULL -- main, footer
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: menu_items
-- Individual navigation links, order, and destination references
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `menu_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `menu_id` INT NOT NULL,
    `parent_id` INT NULL,
    `title` VARCHAR(255) NOT NULL,
    `type` VARCHAR(50) NOT NULL, -- custom, page, category, post, tag
    `url` VARCHAR(255) NULL,
    `object_id` INT NULL,
    `menu_order` INT DEFAULT 0,
    FOREIGN KEY (`menu_id`) REFERENCES `menus`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: modules
-- Extension registry for add-on custom modules (/app/vendors/modules)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `modules` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) UNIQUE NOT NULL,
    `version` VARCHAR(50) NOT NULL,
    `is_active` TINYINT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: themes
-- Extension registry for active & installed themes (/app/vendors/themes & /app/views/themes)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `themes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) UNIQUE NOT NULL,
    `version` VARCHAR(50) NOT NULL,
    `is_active` TINYINT DEFAULT 0,
    `scope` VARCHAR(50) DEFAULT 'frontend' -- frontend, backend
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
