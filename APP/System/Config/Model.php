<?php

declare(strict_types=1);

namespace System\Config;

require_once APPPATH . 'Config/config.php';

use System\Config\QueryBuilder;
use System\Config\SqliteBuilder;

class Model
{
    protected QueryBuilder|SqliteBuilder $db;

    public function __construct()
    {
        if (defined('DATABASE_TYPE') && DATABASE_TYPE === 'sqlite') {
            $this->db = new SqliteBuilder();
        } else {
            $this->db = new QueryBuilder();
        }
    }

    public function find_all(string $table, string $query = '', array $params = []): array
    {
        // RAW SQL with params
        if ($query !== '') {
            $result = $this->db->query($query, $params);
            return is_array($result) ? $result : [];
        }

        // Start QueryBuilder
        $qb = $this->db->table($table)->select('*');

        // QueryBuilder with params array
        // Format: [ ['col', '=', val], ['col2', '!=', val2] ]
        if (!empty($params)) {
            foreach ($params as $condition) {
                // Safety check
                if (count($condition) === 3) {
                    [$column, $operator, $value] = $condition;
                    $qb->where($column, $operator, $value);
                }
            }
        }

        // No SQL, no params → fetch all
        return $qb->get();
    }

    public function find_single(
        string $table,
        ?int $id = null,
        string $query = '',
        array $params = []
    ): ?object {
        // Case 3: Raw SQL query
        if ($query !== '') {
            $result = $this->db->query($query, $params);
            return isset($result[0]) ? (object)$result[0] : null;
        }

        // Initialize query builder
        $builder = $this->db->table($table);

        // Case 1 & 2: Optional ID condition
        if ($id !== null) {
            $builder->where('id', '=', $id);
        }

        if (!empty($params)) {
            foreach ($params as $condition) {
                if (count($condition) === 3) {
                    [$column, $operator, $value] = $condition;
                    $builder->where($column, $operator, $value);
                }
            }
        }
        // Fetch single row
        $row = $builder->first();

        return $row ? (object)$row : null;
    }

    public function num_rows(string $table, string $query = '', array $params = []): int
    {
        // RAW SQL with params → count returned rows
        if ($query !== '') {
            $rows = $this->db->query($query, $params);
            return is_array($rows) ? count($rows) : 0;
        }

        // QueryBuilder count()
        $qb = $this->db->table($table);

        // Format: [ ['col', '=', val], ['col2', '!=', val2] ]
        if (!empty($params)) {
            foreach ($params as $condition) {
                if (count($condition) === 3) {
                    [$column, $operator, $value] = $condition;
                    $qb->where($column, $operator, $value);
                }
            }
        }

        return (int)$qb->count();
    }

    public function insert(string $table, array $data): int|bool
    {
        return $this->db
            ->table($table)
            ->insert($data); // returns insert_id or false
    }

    public function update(string $table, array $data, int $id): bool
    {
        return $this->db
            ->table($table)
            ->where('id', '=', $id)
            ->update($data);
    }

    public function delete(string $table, int $id): bool
    {
        return $this->db
            ->table($table)
            ->where('id', '=', $id)
            ->delete();
    }

    public function newid(): int
    {
        return $this->db->getLastInsertId();
    }
}