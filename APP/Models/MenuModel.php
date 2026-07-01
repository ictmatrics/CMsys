<?php
declare(strict_types=1);

namespace App\Models;

use System\Config\Model;

class MenuModel extends Model
{
    private string $table = 'menus';
    private string $itemsTable = 'menu_items';

    public function __construct()
    {
        parent::__construct();
    }

    public function getMenus(): array
    {
        return $this->find_all($this->table);
    }

    public function getMenuById(int $id): ?object
    {
        return $this->find_single($this->table, $id);
    }

    public function getMenuByLocation(string $location): ?object
    {
        return $this->find_single($this->table, null, '', [['location', '=', $location]]);
    }

    public function saveMenu(array $data): int|bool
    {
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        
        // Atomic constraint: enforce single menu location mapping
        if (!empty($data['location'])) {
            $this->db->query("UPDATE {$this->table} SET location = NULL WHERE location = ?", [$data['location']]);
        }

        if ($id > 0) {
            unset($data['id']);
            $this->update($this->table, $data, $id);
            return $id;
        } else {
            return $this->insert($this->table, $data);
        }
    }

    public function deleteMenu(int $id): bool
    {
        $this->db->table($this->itemsTable)->where('menu_id', '=', $id)->delete();
        return $this->delete($this->table, $id);
    }

    public function getMenuItems(int $menuId): array
    {
        $query = "SELECT * FROM {$this->itemsTable} WHERE menu_id = ? ORDER BY menu_order ASC";
        return $this->db->query($query, [$menuId]);
    }

    public function saveMenuItems(int $menuId, array $items): bool
    {
        // 1. Delete existing items
        $this->db->table($this->itemsTable)->where('menu_id', '=', $menuId)->delete();

        // 2. Insert new items in order, retaining parent-child hierarchy mapping
        $idMap = []; // Maps client-side temporary IDs to actual inserted IDs
        foreach ($items as $item) {
            $parentId = null;
            if (!empty($item['parent_temp_id']) && isset($idMap[$item['parent_temp_id']])) {
                $parentId = $idMap[$item['parent_temp_id']];
            }

            $insertData = [
                'menu_id' => $menuId,
                'parent_id' => $parentId,
                'title' => validate_data($item['title'] ?? ''),
                'type' => validate_data($item['type'] ?? 'custom'),
                'url' => validate_data($item['url'] ?? ''),
                'object_id' => isset($item['object_id']) && $item['object_id'] !== '' ? (int)$item['object_id'] : null,
                'menu_order' => (int)($item['menu_order'] ?? 0)
            ];

            $newId = $this->insert($this->itemsTable, $insertData);
            if ($newId && !empty($item['temp_id'])) {
                $idMap[$item['temp_id']] = $newId;
            }
        }

        return true;
    }
}
