<?php
namespace System\Config;

require_once APPPATH . 'Config/config.php'; // include credentials

/**
 * QueryBuilder Class - PHP 8.3 (MySQLi optimized)
 * Features: Transactions, Raw Query, Utility functions, Pagination
 */
class QueryBuilder
{
    private \mysqli $conn;
    private string $table = '';
    private array $select = [];
    private array $where = [];
    private array $bindings = [];
    private array $order = [];
    private ?string $limit = null;
    private ?string $offset = null;
    private array $join = [];
    private ?string $groupBy = null;
    private ?string $having = null;

    public function __construct(?\mysqli $conn = null)
    {
        if ($conn instanceof \mysqli) {
            $this->conn = $conn;
        } else {
            $this->conn = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($this->conn->connect_error) {
                throw new \Exception("Connection failed: " . $this->conn->connect_error);
            }
        }

        $this->conn->set_charset("utf8mb4");
    }

    public function getLastInsertId(): int
    {
        return (int)$this->conn->insert_id;
    }

    /* ------------------ TRANSACTIONS ------------------ */
    public function beginTransaction(): bool { return $this->conn->begin_transaction(); }
    public function commit(): bool { return $this->conn->commit(); }
    public function rollback(): bool { return $this->conn->rollback(); }

    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /* ------------------ RAW QUERY ------------------ */
    public function query(string $sql, array $bindings = []): array|bool
    {
        $this->bindings = $bindings;
        return $this->execute($sql);
    }

    /* ------------------ BUILDER METHODS ------------------ */
    public function table(string $table): self
    {
        $this->reset();
        $this->table = $table;
        return $this;
    }

    public function select(string|array $columns = ['*']): self
    {
        $this->select = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * where('col', '=', 1)
     * where([['col1', '=', 1], ['col2', '>', 5]])
     * where('col', 1) // defaults to '='
     */
    public function where(string|array $column, ?string $operator = null, mixed $value = null): self
    {
        if (is_array($column)) {
            foreach ($column as $cond) {
                [$col, $op, $val] = $cond;
                $this->where[] = "$col $op ?";
                $this->bindings[] = $val;
            }
        } else {
            if ($value === null) {
                $value = $operator;
                $operator = '=';
            }
            $this->where[] = "$column $operator ?";
            $this->bindings[] = $value;
        }
        return $this;
    }

    public function orWhere(string $column, string $operator, mixed $value): self
    {
        $prefix = empty($this->where) ? '' : 'OR ';
        $this->where[] = "$prefix$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function whereRaw(string $condition): self
    {
        $this->where[] = $condition;
        return $this;
    }

    public function whereBetween(string $column, mixed $start, mixed $end): self
    {
    $this->where[] = "$column BETWEEN ? AND ?";
    $this->bindings[] = $start;
    $this->bindings[] = $end;
    return $this;
    }
    
    public function in(string $column, array $values): self
    {
        $placeholders = implode(", ", array_fill(0, count($values), "?"));
        $this->where[] = "$column IN ($placeholders)";
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }

    public function notIn(string $column, array $values): self
    {
        $placeholders = implode(", ", array_fill(0, count($values), "?"));
        $this->where[] = "$column NOT IN ($placeholders)";
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = "INNER"): self
    {
        $this->join[] = "$type JOIN $table ON $first $operator $second";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->order[] = "$column $direction";
        return $this;
    }

    public function groupBy(string|array $columns): self
    {
        $cols = is_array($columns) ? implode(", ", $columns) : $columns;
        $this->groupBy = "GROUP BY $cols";
        return $this;
    }

    public function having(string $condition): self
    {
        $this->having = "HAVING $condition";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = "LIMIT $limit";
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = "OFFSET $offset";
        return $this;
    }

    /* ------------------ EXECUTION ------------------ */
    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        return $this->execute($sql);
    }

    public function first(): ?array
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function insert(array $data): int|bool
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $this->bindings = array_values($data);

        $this->execute($sql);
        return $this->conn->insert_id ?: true;
    }

    public function update(array $data): bool
    {
        $set = implode(", ", array_map(fn($k) => "$k = ?", array_keys($data)));
        $sql = "UPDATE {$this->table} SET $set " . $this->buildWhere();
        $this->bindings = array_merge(array_values($data), $this->bindings);

        $this->execute($sql);
        return $this->conn->errno === 0;
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table} " . $this->buildWhere();
        $this->execute($sql);
        return $this->conn->errno === 0;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) as aggregate FROM {$this->table} " . $this->buildWhere();
        $result = $this->execute($sql);
        return (int)($result[0]['aggregate'] ?? 0);
    }

    public function exists(): bool
    {
        $sql = "SELECT 1 FROM {$this->table} " . $this->buildWhere() . " LIMIT 1";
        $result = $this->execute($sql);
        return !empty($result);
    }

    public function truncate(): bool
    {
        $sql = "TRUNCATE TABLE {$this->table}";
        return $this->execute($sql);
    }

     public function whereGroup(callable $callback): self
    {
        $group = new static($this->conn);
        $callback($group);

        if ($group->where) {
            $this->where[] = '(' . implode(' AND ', $group->where) . ')';
            $this->bindings = array_merge($this->bindings, $group->bindings);
        }

        return $this;
    }

    public function orWhereGroup(callable $callback): self
    {
        $group = new static($this->conn);
        $callback($group);

        if ($group->where) {
            $prefix = empty($this->where) ? '' : 'OR ';
            $this->where[] = $prefix . '(' . implode(' AND ', $group->where) . ')';
            $this->bindings = array_merge($this->bindings, $group->bindings);
        }

        return $this;
    }
    /* ------------------ PAGINATION ------------------ */
    public function paginate(int $perPage, int $page = 1): array
    {
        $total = $this->count();
        $lastPage = (int)ceil($total / $perPage);
        $page = max(1, min($page, $lastPage));

        $this->limit($perPage)->offset(($page - 1) * $perPage);
        $data = $this->get();

        return [
            "data" => $data,
            "total" => $total,
            "per_page" => $perPage,
            "current_page" => $page,
            "last_page" => $lastPage
        ];
    }

    /* ------------------ PRIVATE HELPERS ------------------ */
    private function buildSelectQuery(): string
    {
        $columns = $this->select ? implode(", ", $this->select) : "*";
        $sql = "SELECT $columns FROM {$this->table} ";
        if ($this->join) {
            $sql .= implode(" ", $this->join) . " ";
        }
        $sql .= $this->buildWhere();
        if ($this->groupBy) $sql .= " $this->groupBy";
        if ($this->having) $sql .= " $this->having";
        if ($this->order) $sql .= " ORDER BY " . implode(", ", $this->order);
        if ($this->limit) $sql .= " $this->limit";
        if ($this->offset) $sql .= " $this->offset";
        return trim($sql);
    }

    private function buildWhere(): string
    {
        return $this->where ? "WHERE " . implode(" AND ", $this->where) : '';
    }

    private function execute(string $sql): array|bool
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new \Exception("SQL Prepare Error: " . $this->conn->error);
        }

        if ($this->bindings) {
            $types = $this->getTypes($this->bindings);
            $stmt->bind_param($types, ...$this->bindings);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $this->bindings = [];
        $this->where = [];

        if ($result === false) {
            return true;
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function getTypes(array $values): string
    {
        return implode('', array_map(function ($v) {
            return match (true) {
                is_int($v)   => 'i',
                is_float($v) => 'd',
                is_null($v)  => 's',
                default      => 's',
            };
        }, $values));
    }

    private function reset(): void
    {
        $this->select = [];
        $this->where = [];
        $this->bindings = [];
        $this->order = [];
        $this->limit = null;
        $this->offset = null;
        $this->join = [];
        $this->groupBy = null;
        $this->having = null;
    }
}
