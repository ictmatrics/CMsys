<?php
declare(strict_types=1);

namespace App\Models;

use System\Config\Model;

class MediaModel extends Model
{
    private string $table = 'media_library';

    public function __construct()
    {
        parent::__construct();
    }

    public function logMedia(string $originalName, string $path, string $mimeType, string $dimensions = '', ?int $userId = null): int|bool
    {
        return $this->insert($this->table, [
            'original_name' => $originalName,
            'path' => $path,
            'mime_type' => $mimeType,
            'dimensions' => $dimensions,
            'user_id' => $userId
        ]);
    }

    public function getAllMedia(): array
    {
        return $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
    }

    public function deleteMedia(int $id): bool
    {
        $media = $this->find_single($this->table, $id);
        if ($media) {
            // Delete physical file
            $filePath = ROOTPATH . 'public_html/' . ltrim($media->path, '/\\');
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            // Delete database log
            return $this->delete($this->table, $id);
        }
        return false;
    }
}
