<?php
declare(strict_types=1);

namespace App\Models;

use System\Config\Model;
use App\Libraries\DatabasePrefix;

class BaseModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Resolve table name using the active prefix.
     */
    protected function prefix(string $table): string
    {
        return DatabasePrefix::table($table);
    }

    public function find_all(string $table, string $query = '', array $params = []): array
    {
        return parent::find_all(DatabasePrefix::table($table), $query, $params);
    }

    public function find_single(
        string $table,
        ?int $id = null,
        string $query = '',
        array $params = []
    ): ?object {
        return parent::find_single(DatabasePrefix::table($table), $id, $query, $params);
    }

    public function num_rows(string $table, string $query = '', array $params = []): int
    {
        return parent::num_rows(DatabasePrefix::table($table), $query, $params);
    }

    public function insert(string $table, array $data): int|bool
    {
        return parent::insert(DatabasePrefix::table($table), $data);
    }

    public function update(string $table, array $data, int $id): bool
    {
        return parent::update(DatabasePrefix::table($table), $data, $id);
    }

    public function delete(string $table, int $id): bool
    {
        return parent::delete(DatabasePrefix::table($table), $id);
    }
}
