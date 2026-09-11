<?php
declare(strict_types=1);

namespace App\Models;

use System\Config\Model;

class CommentModel extends Model
{
    private string $table = 'comments';

    public function __construct()
    {
        parent::__construct();
    }

    public function getCommentsByPost(int $postId, string $status = 'approved'): array
    {
        $query = "SELECT * FROM {$this->table} WHERE post_id = ? AND status = ? ORDER BY created_at DESC";
        return $this->db->query($query, [$postId, $status]);
    }

    public function getAllComments(): array
    {
        $query = "SELECT c.*, p.title as post_title, p.slug as post_slug, p.type as post_type 
                  FROM {$this->table} c 
                  JOIN posts p ON c.post_id = p.id 
                  ORDER BY c.created_at DESC";
        return $this->db->query($query);
    }

    public function saveComment(array $data): int|bool
    {
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        if ($id > 0) {
            unset($data['id']);
            $this->update($this->table, $data, $id);
            return $id;
        } else {
            return $this->insert($this->table, $data);
        }
    }

    public function updateCommentStatus(int $id, string $status): bool
    {
        return $this->update($this->table, ['status' => $status], $id);
    }

    public function deleteComment(int $id): bool
    {
        return $this->delete($this->table, $id);
    }
}
