<?php

declare(strict_types=1);

namespace App\Models;

use System\Config\Model;

class FaqModel extends Model
{
    private string $table = 'faqs';

    public function __construct()
    {
        parent::__construct();
        $this->migrateSchema();
    }

    private function migrateSchema(): void
    {
        try {
            $tables = $this->db->query("SHOW TABLES LIKE '{$this->table}'");
            if (empty($tables)) {
                $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `question` VARCHAR(255) NOT NULL,
                    `answer` TEXT NOT NULL,
                    `category` VARCHAR(100) DEFAULT 'General',
                    `sort_order` INT DEFAULT 0,
                    `status` TINYINT(1) DEFAULT 1,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                $this->db->query($sql);

                // Insert seed FAQs
                $this->db->query("INSERT INTO `{$this->table}` (`question`, `answer`, `category`, `sort_order`, `status`) VALUES 
                ('What services does N-Tech provide?', 'N-Tech provides comprehensive software engineering, cloud integration, cybersecurity, and digital transformation solutions.', 'General', 1, 1),
                ('How do I request a project consultation?', 'You can contact us via our Contact Us page form or email us directly at support@ntech.com.', 'Consultation', 2, 1),
                ('What technology stack do you specialize in?', 'We specialize in modern web architectures, PHP MVC frameworks, Node.js, React, Python, and Cloud Services.', 'Technical', 3, 1),
                ('Do you provide post-deployment maintenance?', 'Yes, we offer 24/7 technical support and ongoing maintenance SLA packages for all digital products.', 'Support', 4, 1);");
            }
        } catch (\Throwable $e) {
            // Silence migration exceptions
        }
    }

    public function getAllFaqs(): array
    {
        return $this->db->table($this->table)
                        ->orderBy('sort_order', 'ASC')
                        ->orderBy('id', 'DESC')
                        ->get();
    }

    public function getPublishedFaqs(): array
    {
        return $this->db->table($this->table)
                        ->where('status', '=', 1)
                        ->orderBy('sort_order', 'ASC')
                        ->orderBy('id', 'DESC')
                        ->get();
    }

    public function saveFaq(array $data, ?int $id = null): bool
    {
        if ($id !== null && $id > 0) {
            return $this->update($this->table, $data, $id);
        }
        return (bool)$this->insert($this->table, $data);
    }

    public function deleteFaq(int $id): bool
    {
        return $this->delete($this->table, $id);
    }
}
