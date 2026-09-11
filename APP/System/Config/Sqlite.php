<?php
die('sqlite');
class Database {
    private PDO $link;

    public function __construct() {
        try {
            $this->link = new PDO('sqlite:' . DB_FILE);
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->link->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->log_db_errors("Connection failed: " . $e->getMessage(), '');
            exit();
        }
    }

    public function __destruct() {
        $this->disconnect();
    }

    private function log_db_errors($error, $query) {
        die('<p>Query: ' . htmlentities($query) . '<br />Error: ' . $error . '</p>');
    }

    public function filter($data) {
        if (!is_array($data)) {
            return trim(htmlentities($data, ENT_QUOTES, 'UTF-8'));
        } else {
            return array_map([$this, 'filter'], $data);
        }
    }

    public function query($query, array $params = []): bool {
        try {
            $stmt = $this->link->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->log_db_errors($e->getMessage(), $query);
            return false;
        }
    }

    public function get_results(string $query, array $params = []): array {
        $stmt = $this->link->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function get_single(string $query, array $params = []): ?object {
        $stmt = $this->link->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row ?: null;
    }

    public function insert(string $table, array $data): bool {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), '?'));
        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        return $this->query($query, array_values($data));
    }

    public function update(string $table, array $data, array $where): bool {
        $set = implode(", ", array_map(fn($key) => "$key = ?", array_keys($data)));
        $whereClause = implode(" AND ", array_map(fn($key) => "$key = ?", array_keys($where)));
        $query = "UPDATE $table SET $set WHERE $whereClause";
        return $this->query($query, array_merge(array_values($data), array_values($where)));
    }

    public function delete(string $table, array $where): bool {
        $whereClause = implode(" AND ", array_map(fn($key) => "$key = ?", array_keys($where)));
        $query = "DELETE FROM $table WHERE $whereClause";
        return $this->query($query, array_values($where));
    }

    public function lastid(): int {
        return (int) $this->link->lastInsertId();
    }

    public function num_rows(string $query, array $params = []): int {
        $stmt = $this->link->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function num_fields(string $query): int {
        $stmt = $this->link->query($query);
        return $stmt->columnCount();
    }

    public function list_fields(string $query): array {
        $stmt = $this->link->query($query);
        $fields = [];
        for ($i = 0; $i < $stmt->columnCount(); $i++) {
            $meta = $stmt->getColumnMeta($i);
            $fields[] = $meta['name'];
        }
        return $fields;
    }

    public function truncate(array $tables): int {
        $truncated = 0;
        foreach ($tables as $table) {
            if ($this->query("DELETE FROM $table")) {
                $this->query("VACUUM"); // Free space in SQLite
                $truncated++;
            }
        }
        return $truncated;
    }

    public function display($variable, bool $echo = true): string {
        $output = is_array($variable) ? '<pre>' . print_r($variable, true) . '</pre>' : (string)$variable;
        if ($echo) {
            echo $output;
        }
        return $output;
    }

   private function disconnect(): void 
{
    
        $this->link = null; // PDO connection closed
    
}


    public function begin_transaction() {
        $this->link->beginTransaction();
    }

    public function rollback() {
        $this->link->rollBack();
    }

    public function commit() {
        $this->link->commit();
    }

    public function table_exists($name): bool {
        $stmt = $this->link->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?");
        $stmt->execute([$name]);
        return (bool) $stmt->fetch();
    }
}
