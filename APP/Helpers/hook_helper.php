<?php
declare(strict_types=1);

use App\Libraries\HookManager;

if (!function_exists('add_action')) {
    function add_action(string $hook, callable $callback, int $priority = 10): void
    {
        HookManager::getInstance()->addAction($hook, $callback, $priority);
    }
}

if (!function_exists('do_action')) {
    function do_action(string $hook, ...$args): void
    {
        HookManager::getInstance()->doAction($hook, ...$args);
    }
}

if (!function_exists('add_filter')) {
    function add_filter(string $hook, callable $callback, int $priority = 10): void
    {
        HookManager::getInstance()->addFilter($hook, $callback, $priority);
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters(string $hook, mixed $value, ...$args): mixed
    {
        return HookManager::getInstance()->applyFilters($hook, $value, ...$args);
    }
}

if (!function_exists('boot_active_modules')) {
    function boot_active_modules(): void
    {
        static $booted = false;
        if ($booted) {
            return;
        }
        $booted = true;
        try {
            $extensionModel = new \App\Models\ExtensionModel();
            $activeModules = $extensionModel->find_all('modules', '', [['is_active', '=', 1]]);
            if ($activeModules) {
                foreach ($activeModules as $module) {
                    $moduleName = is_array($module) ? ($module['name'] ?? '') : ($module->name ?? '');
                    if (!empty($moduleName)) {
                        $initFile = APPPATH . 'Modules/' . $moduleName . '/init.php';
                        if (file_exists($initFile)) {
                            require_once $initFile;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore during installation or if table missing
        }
    }
}

