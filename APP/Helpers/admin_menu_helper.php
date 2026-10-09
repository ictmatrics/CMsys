<?php
declare(strict_types=1);

use App\Libraries\AdminMenuRegistry;

if (!function_exists('add_admin_menu')) {
    /**
     * Register a top-level admin sidebar menu item.
     * Enclosure Enforcement: Theme-specific menus automatically route under 'appearance'.
     */
    function add_admin_menu(
        string $id,
        string $title,
        string $url,
        string $icon = '',
        int $position = 50,
        string $role = 'admin'
    ): void {
        $cleanId = strtolower(trim($id));
        $cleanUrl = strtolower(trim($url));

        // Enclosure Enforcement: Route theme-specific options into 'appearance'
        $isThemeMenu = str_starts_with($cleanId, 'theme_')
            || (str_contains($cleanId, 'theme') && !in_array($cleanId, ['appearance', 'themes', 'theme'], true))
            || (str_contains($cleanUrl, 'theme') && !in_array($cleanUrl, ['admin/themes', 'themes'], true));

        if ($isThemeMenu) {
            add_admin_submenu('appearance', $id, $title, $url, $position, $role);
            return;
        }

        AdminMenuRegistry::getInstance()->addMenu($id, $title, $url, $icon, $position, $role);
    }
}

if (!function_exists('add_admin_submenu')) {
    /**
     * Register a nested submenu item under a parent menu item.
     */
    function add_admin_submenu(
        string $parentId,
        string $id,
        string $title,
        string $url,
        int $position = 50,
        string $role = 'admin'
    ): void {
        AdminMenuRegistry::getInstance()->addSubmenu($parentId, $id, $title, $url, $position, $role);
    }
}

if (!function_exists('get_admin_menus')) {
    /**
     * Retrieve the sorted and filtered admin sidebar menus for current session.
     */
    function get_admin_menus(): array
    {
        return AdminMenuRegistry::getInstance()->getMenus();
    }
}
