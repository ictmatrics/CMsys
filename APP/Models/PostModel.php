<?php
declare(strict_types=1);

namespace App\Models;

use System\Config\Model;

class PostModel extends Model
{
    private string $table = 'posts';
    private string $metaTable = 'post_meta';

    public function __construct()
    {
        parent::__construct();
        $this->migrateSchema();
    }

    private function migrateSchema(): void
    {
        try {
            $tables = $this->db->query("SHOW TABLES LIKE '{$this->table}'");
            if (!empty($tables)) {
                $columns = $this->db->query("SHOW COLUMNS FROM `{$this->table}`");
                $existing = array_column($columns, 'Field');
                
                if (!in_array('views', $existing, true)) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `views` INT DEFAULT 0 NOT NULL");
                }
                if (!in_array('is_trending', $existing, true)) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `is_trending` TINYINT DEFAULT 0 NOT NULL");
                }
                if (!in_array('related_posts', $existing, true)) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `related_posts` TEXT NULL");
                }
            }
        } catch (\Throwable $e) {
            // Silence migration errors to prevent installer/setup crashes
        }
    }

    public function generateSlug(string $title, string $type = 'post', int $excludeId = 0): string
    {
        // Lowercase, remove special characters, replace spaces with hyphens
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s_]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        if (empty($slug)) {
            $slug = 'untitled';
        }

        // Collision check
        $originalSlug = $slug;
        $counter = 1;
        while (true) {
            $exists = $this->db->table($this->table)
                               ->select('id')
                               ->where('slug', '=', $slug)
                               ->where('type', '=', $type)
                               ->where('id', '!=', $excludeId)
                               ->first();
            if ($exists === null) {
                break;
            }
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function getPostById(int $id): ?object
    {
        $post = $this->find_single($this->table, $id);
        if ($post) {
            $post->meta = $this->getPostMeta($id);
        }
        return $post;
    }

    public function getPostBySlug(string $slug, string $type = 'post'): ?object
    {
        $post = $this->find_single($this->table, null, '', [
            ['slug', '=', $slug],
            ['type', '=', $type]
        ]);
        if ($post) {
            $post->meta = $this->getPostMeta((int)$post->id);
        }
        return $post;
    }

    public function savePost(array $data, array $meta = []): int|bool
    {
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $type = $data['type'] ?? 'post';
        
        // Slug generation/override
        $slug = validate_data($data['slug'] ?? '');
        if (empty($slug)) {
            $slug = $this->generateSlug($data['title'], $type, $id);
        } else {
            // Sanitize manual override slug
            $slug = strtolower($slug);
            $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
            // Check manual slug collision
            $slug = $this->generateSlug($slug, $type, $id);
        }
        $data['slug'] = $slug;

        if ($id > 0) {
            unset($data['id']);
            $this->update($this->table, $data, $id);
            $postId = $id;
        } else {
            $postId = $this->insert($this->table, $data);
        }

        if ($postId) {
            $this->savePostMeta((int)$postId, $meta);
            return $postId;
        }

        return false;
    }

    public function deletePost(int $id): bool
    {
        // 1. Delete associated comments
        $this->db->table('comments')->where('post_id', '=', $id)->delete();
        // 2. Delete associated taxonomies relationships
        $this->db->table('post_taxonomies')->where('post_id', '=', $id)->delete();
        // 3. Delete associated post meta
        $this->db->table($this->metaTable)->where('post_id', '=', $id)->delete();
        // 4. Delete the post itself
        return $this->delete($this->table, $id);
    }

    // Post Meta / Custom Fields Support
    public function getPostMeta(int $postId): array
    {
        $rows = $this->db->query("SELECT meta_key, meta_value FROM {$this->metaTable} WHERE post_id = ?", [$postId]);
        $meta = [];
        foreach ($rows as $row) {
            $meta[$row['meta_key']] = $row['meta_value'];
        }
        return $meta;
    }

    public function getSingleMeta(int $postId, string $key, string $default = ''): string
    {
        $row = $this->find_single($this->metaTable, null, '', [
            ['post_id', '=', $postId],
            ['meta_key', '=', $key]
        ]);
        return $row ? $row->meta_value : $default;
    }

    public function savePostMeta(int $postId, array $meta): void
    {
        foreach ($meta as $key => $value) {
            $existing = $this->db->query("SELECT id FROM {$this->metaTable} WHERE post_id = ? AND meta_key = ?", [$postId, $key]);
            if (!empty($existing)) {
                $this->db->query("UPDATE {$this->metaTable} SET meta_value = ? WHERE id = ?", [(string)$value, (int)$existing[0]['id']]);
            } else {
                $this->db->query("INSERT INTO {$this->metaTable} (post_id, meta_key, meta_value) VALUES (?, ?, ?)", [$postId, $key, (string)$value]);
            }
        }
    }

    public function getAllPosts(string $type = 'post', string $status = '', string $orderBy = 'id', string $orderDir = 'DESC'): array
    {
        $builder = $this->db->table($this->table)
                            ->where('type', '=', $type);
        if (!empty($status)) {
            $builder->where('status', '=', $status);
        }
        return $builder->orderBy($orderBy, $orderDir)->get();
    }

    public function getRecentPublishedPosts(int $limit = 5): array
    {
        return $this->db->table($this->table)
                        ->where('type', '=', 'post')
                        ->where('status', '=', 'published')
                        ->orderBy('created_at', 'DESC')
                        ->limit($limit)
                        ->get();
    }

    public function getPublishedPages(): array
    {
        return $this->db->table($this->table)
                        ->where('type', '=', 'page')
                        ->where('status', '=', 'published')
                        ->orderBy('created_at', 'DESC')
                        ->get();
    }

    public function getPublishedPosts(int $limit = 0): array
    {
        $builder = $this->db->table($this->table)
                            ->where('type', '=', 'post')
                            ->where('status', '=', 'published')
                            ->orderBy('created_at', 'DESC');
        if ($limit > 0) {
            $builder->limit($limit);
        }
        return $builder->get();
    }

    public function getRecentPosts(int $limit = 5): array
    {
        return $this->db->table($this->table)
                        ->orderBy('created_at', 'DESC')
                        ->limit($limit)
                        ->get();
    }

    public function getPostsByTaxonomy(int $taxonomyId): array
    {
        $query = "SELECT p.* FROM {$this->table} p 
                  JOIN post_taxonomies pt ON p.id = pt.post_id 
                  WHERE pt.taxonomy_id = ? AND p.status = 'published' 
                  ORDER BY p.created_at DESC";
        return $this->db->query($query, [$taxonomyId]);
    }

    public function incrementViews(int $postId): void
    {
        $this->db->query("UPDATE {$this->table} SET views = views + 1 WHERE id = ?", [$postId]);
    }

    public function getTrendingPosts(int $limit = 5): array
    {
        $posts = $this->getPublishedPosts();
        $scoredPosts = [];
        $gravity = 1.8;
        
        foreach ($posts as $post) {
            $postId = (int)(is_object($post) ? ($post->id ?? 0) : ($post['id'] ?? 0));
            $postViews = (int)(is_object($post) ? ($post->views ?? 0) : ($post['views'] ?? 0));
            $createdAt = is_object($post) ? ($post->created_at ?? '') : ($post['created_at'] ?? '');
            
            $timeSinceCreated = (time() - strtotime($createdAt)) / 3600;
            if ($timeSinceCreated < 0) {
                $timeSinceCreated = 0;
            }
            
            $score = $postViews / pow(($timeSinceCreated + 2), $gravity);
            
            if (is_object($post)) {
                $post->trend_score = $score;
                $scoredPosts[] = $post;
            } else {
                $post['trend_score'] = $score;
                $scoredPosts[] = $post;
            }
        }
        
        usort($scoredPosts, function ($a, $b) {
            $scoreA = is_object($a) ? $a->trend_score : $a['trend_score'];
            $scoreB = is_object($b) ? $b->trend_score : $b['trend_score'];
            return $scoreB <=> $scoreA;
        });
        
        return array_slice($scoredPosts, 0, $limit);
    }
}
