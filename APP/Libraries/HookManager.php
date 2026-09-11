<?php
declare(strict_types=1);

namespace App\Libraries;

class HookManager
{
    private static ?self $instance = null;
    private array $actions = [];
    private array $filters = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addAction(string $hook, callable $callback, int $priority = 10): void
    {
        $this->actions[$hook][$priority][] = $callback;
    }

    public function doAction(string $hook, ...$args): void
    {
        if (!isset($this->actions[$hook])) {
            return;
        }

        $priorities = $this->actions[$hook];
        ksort($priorities);

        foreach ($priorities as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }

    public function addFilter(string $hook, callable $callback, int $priority = 10): void
    {
        $this->filters[$hook][$priority][] = $callback;
    }

    public function applyFilters(string $hook, mixed $value, ...$args): mixed
    {
        if (!isset($this->filters[$hook])) {
            return $value;
        }

        $priorities = $this->filters[$hook];
        ksort($priorities);

        foreach ($priorities as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                $params = array_merge([$value], $args);
                $value = call_user_func_array($callback, $params);
            }
        }

        return $value;
    }
}
