<?php
namespace System\Config;



use mysqli; // import mysqli from global namespace


class Database {
    private mysqli $link;

    public function __construct() {
        $this->link = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->link->connect_errno) {
            $this->log_db_errors("Connection failed: " . $this->link->connect_error, '');
            exit();
        }
        $this->link->set_charset("utf8");
    }

    public function __destruct() {
        $this->disconnect();
    }

    private function log_db_errors($error, $query) {
        die('<p>Query: ' . htmlentities($query) . '<br />Error: ' . $error . '</p>');
    }

    public function filter($data) {
        if (!is_array($data)) {
            return $this->link->real_escape_string(trim(htmlentities($data)));
        } else {
            return array_map([$this, 'filter'], $data);
        }
    }
    public function query($query) {
        $full_query = $this->link->query($query);
    
        if ($this->link->error) {
            $this->log_db_errors($this->link->error, $query);
            return false;
        }
    
        // Free the result set if it exists
       
        return true;
    }
    public function get_results(string $query, array $params = []): array {
        $stmt = $this->link->prepare($query);
        if ($params) {
            $stmt->bind_param(...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

public function get_single(string $query, array $params = []): ?object
{
    $stmt = $this->link->prepare($query);

    if ($stmt === false) {
        $this->log_db_errors($this->link->error, $query);
        return null;
    }

    if ($params) {
        // Assuming $params is an array where the first element is the types string and the rest are the variables
        $stmt->bind_param(...$params);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result === false) {
        $this->log_db_errors($stmt->error, $query);
        return null;
    }

    $row = $result->fetch_object();
    $result->free();
    $stmt->close();

    return $row ?: null;
}



    public function insert(string $table, array $data): bool {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), '?'));
        $types = str_repeat('s', count($data)); // Assuming all data are strings for simplicity
        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->link->prepare($query);
        $stmt->bind_param($types, ...array_values($data));
        return $stmt->execute();
    }

    public function update(string $table, array $data, array $where): bool {
        $set = implode(", ", array_map(fn($key) => "$key = ?", array_keys($data)));
        $whereClause = implode(" AND ", array_map(fn($key) => "$key = ?", array_keys($where)));
        $types = str_repeat('s', count($data) + count($where));
        $query = "UPDATE $table SET $set WHERE $whereClause";
        $stmt = $this->link->prepare($query);
        $stmt->bind_param($types, ...array_merge(array_values($data), array_values($where)));
        return $stmt->execute();
    }

    public function delete(string $table, array $where): bool {
        $whereClause = implode(" AND ", array_map(fn($key) => "$key = ?", array_keys($where)));
        $types = str_repeat('s', count($where));
        $query = "DELETE FROM $table WHERE $whereClause";
        $stmt = $this->link->prepare($query);
        $stmt->bind_param($types, ...array_values($where));
        return $stmt->execute();
    }

    public function lastid(): int {
        return $this->link->insert_id;
    }
    public function num_rows(string $query): int 
    {
           $num_rows = $this->link->query( $query );
           if( $this->link->error )
       {
           $this->log_db_errors( $this->link->error, $query );
           return $this->link->error;
       }
        else
       {
        return $num_rows->num_rows;
       }
   }
    public function num_fields(string $query): int {
        $result = $this->link->query($query);
        return $result->field_count;
    }

    public function list_fields(string $query): array {
        $result = $this->link->query($query);
        return $result->fetch_fields();
    }

    public function truncate(array $tables): int {
        $truncated = 0;
        foreach ($tables as $table) {
            $query = "TRUNCATE TABLE `$table`";
            if ($this->link->query($query) !== false) {
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

    public function disconnect() {
        $this->link->close();
    }

    public function begin_transaction() {
        $this->link->begin_transaction();
    }

    public function rollback() {
        $this->link->rollback();
    }

    public function commit() {
        $this->link->commit();
    }

    public function table_exists($name)
    {
        $name = $this->link->real_escape_string($name);
        $query = $this->link->query("SHOW TABLES LIKE '$name'");
            if ($query && $query->num_rows > 0) {
            return true; // Table exists
        } else {
           // echo "No table named '$name' found.";
            return false; // Table does not exist
        }
    }
}
