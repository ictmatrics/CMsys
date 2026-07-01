<?php
declare(strict_types=1);

namespace App\Models;

use System\Config\Model;

class TaxonomyModel extends Model
{
    private string $table = 'taxonomies';
    private string $relationTable = 'post_taxonomies';

    public function __construct()
    {
        parent::__construct();
    }

    public function getTaxonomyById(int $id): ?object
    {
        return $this->find_single($this->table, $id);
    }

    public function getTaxonomyBySlug(string $slug, string $type): ?object
    {
        return $this->find_single($this->table, null, '', [
            ['slug', '=', $slug],
            ['type', '=', $type]
        ]);
    }

    public function saveTaxonomy(array $data): int|bool
    {
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $type = $data['type'] ?? 'category';
        
        // Generate slug if empty
        if (empty($data['slug'])) {
            $slug = strtolower($data['name']);
            $slug = preg_replace('/[^a-z0-9-]/', '', str_replace(' ', '-', $slug));
            $data['slug'] = $slug;
        }

        // Check if taxonomy with same type and slug exists
        $existing = $this->getTaxonomyBySlug($data['slug'], $type);
        if ($existing && (int)$existing->id !== $id) {
            return false; // Duplicate slug
        }

        if ($id > 0) {
            unset($data['id']);
            $this->update($this->table, $data, $id);
            return $id;
        } else {
            return $this->insert($this->table, $data);
        }
    }

    public function deleteTaxonomy(int $id): bool
    {
        // 1. Delete relations
        $this->db->table($this->relationTable)->where('taxonomy_id', '=', $id)->delete();
        // 2. Delete taxonomy
        return $this->delete($this->table, $id);
    }

    public function getAllTaxonomies(string $type = 'category'): array
    {
        return $this->find_all($this->table, '', [['type', '=', $type]]);
    }

    public function getPostTaxonomies(int $postId, string $type = 'category'): array
    {
        $query = "SELECT t.* FROM {$this->table} t 
                  JOIN {$this->relationTable} pt ON t.id = pt.taxonomy_id 
                  WHERE pt.post_id = ? AND t.type = ?";
        return $this->db->query($query, [$postId, $type]);
    }

    public function setPostTaxonomies(int $postId, array $taxonomyIds, string $type = 'category'): void
    {
        // 1. Get current post taxonomies of this type
        $current = $this->getPostTaxonomies($postId, $type);
        $currentIds = array_map(fn($t) => (int)$t['id'], $current);

        // 2. Delete relations of this type that are not in $taxonomyIds
        foreach ($currentIds as $cId) {
            if (!in_array($cId, $taxonomyIds, true)) {
                $this->db->query("DELETE FROM {$this->relationTable} WHERE post_id = ? AND taxonomy_id = ?", [$postId, $cId]);
            }
        }

        // 3. Add new relations
        foreach ($taxonomyIds as $tId) {
            if (!in_array($tId, $currentIds, true)) {
                $this->insert($this->relationTable, [
                    'post_id' => $postId,
                    'taxonomy_id' => $tId
                ]);
            }
        }
    }
}
