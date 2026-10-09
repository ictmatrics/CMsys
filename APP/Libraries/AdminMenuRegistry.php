<?php
declare(strict_types=1);

namespace App\Libraries;

class AdminMenuRegistry
{
    private static ?self $instance = null;
    private array $menus = [];
    private bool $booted = false;
    private bool $coreRegistered = false;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function hasMenu(string $id): bool
    {
        return isset($this->menus[$id]);
    }

    public function addMenu(
        string $id,
        string $title,
        string $url,
        string $icon = '',
        int $position = 50,
        string $role = 'admin'
    ): self {
        if (!isset($this->menus[$id])) {
            $this->menus[$id] = [
                'id' => $id,
                'title' => $title,
                'url' => $url,
                'icon' => $icon,
                'position' => $position,
                'role' => $role,
                'submenus' => [],
            ];
        } else {
            $this->menus[$id]['title'] = $title;
            $this->menus[$id]['url'] = $url;
            if (!empty($icon)) {
                $this->menus[$id]['icon'] = $icon;
            }
            $this->menus[$id]['position'] = $position;
            $this->menus[$id]['role'] = $role;
        }

        return $this;
    }

    public function addSubmenu(
        string $parentId,
        string $id,
        string $title,
        string $url,
        int $position = 50,
        string $role = 'admin',
        string $icon = ''
    ): self {
        if (!isset($this->menus[$parentId])) {
            $parentTitle = ucwords(str_replace(['_', '-'], ' ', $parentId));
            $this->addMenu($parentId, $parentTitle, '#', 'fa-folder', 50, $role);
        }

        $this->menus[$parentId]['submenus'][$id] = [
            'id' => $id,
            'parentId' => $parentId,
            'title' => $title,
            'url' => $url,
            'icon' => $icon,
            'position' => $position,
            'role' => $role,
        ];

        return $this;
    }

    public function removeMenu(string $id): self
    {
        unset($this->menus[$id]);
        return $this;
    }

    public function removeSubmenu(string $parentId, string $id): self
    {
        if (isset($this->menus[$parentId]['submenus'][$id])) {
            unset($this->menus[$parentId]['submenus'][$id]);
        }
        return $this;
    }

    public function registerCoreMenus(): void
    {
        if ($this->coreRegistered) {
            return;
        }
        $this->coreRegistered = true;

        // 1. Dashboard (top-level, pos: 10, role: editor)
        $this->addMenu('dashboard', 'Dashboard', 'admin/dashboard', 'fa-gauge', 10, 'editor');

        // 2. Content (parent, pos: 20, role: editor)
        $this->addMenu('content', 'Content', '#', 'fa-file-lines', 20, 'editor');
        $this->addSubmenu('content', 'posts', 'Posts', 'admin/posts', 10, 'editor', 'fa-file-lines');
        $this->addSubmenu('content', 'pages', 'Pages', 'admin/pages', 20, 'editor', 'fa-file');
        $this->addSubmenu('content', 'categories', 'Categories', 'admin/categories', 30, 'editor', 'fa-folder');
        $this->addSubmenu('content', 'tags', 'Tags', 'admin/tags', 40, 'editor', 'fa-tags');
        $this->addSubmenu('content', 'media', 'Media Library', 'admin/media', 50, 'editor', 'fa-images');

        // 3. Appearance: Design & Layout (parent, pos: 30, role: editor)
        $this->addMenu('appearance', 'Design & Layout', '#', 'fa-palette', 30, 'editor');
        $this->addSubmenu('appearance', 'themes', 'Themes', 'admin/themes', 10, 'admin', 'fa-palette');
        $this->addSubmenu('appearance', 'menus', 'Navigation Menus', 'admin/menus', 20, 'editor', 'fa-bars');
        $this->addSubmenu('appearance', 'widgets', 'Widgets & Layouts', 'admin/widgets', 30, 'editor', 'fa-shapes');

        // 4. Custom Post Types (parent, pos: 40, role: editor)
        $this->addMenu('cpts', 'Custom Post Types', '#', 'fa-layer-group', 40, 'editor');
        $this->addSubmenu('cpts', 'cpts_manage', 'All Post Types', 'admin/cpts', 5, 'admin', 'fa-gear');

        // Seed dynamic CPT submenus from OptionModel if available
        try {
            if (class_exists('App\Models\OptionModel')) {
                $cptsJson = (new \App\Models\OptionModel())->getOption('custom_post_types', '[]');
                $cptsList = json_decode((string)$cptsJson, true);
                if (!empty($cptsList) && is_array($cptsList)) {
                    $pos = 10;
                    foreach ($cptsList as $slug => $cpt) {
                        $label = is_array($cpt) ? ($cpt['label'] ?? ucfirst((string)$slug)) : (string)$slug;
                        $this->addSubmenu('cpts', 'cpt_' . $slug, $label, 'admin/cpt/entries/' . $slug, $pos++, 'editor', 'fa-hashtag');
                    }
                }
            }
        } catch (\Throwable $e) {
            // Non-blocking if table or model not ready
        }

        // 5. Module Manager (standalone top-level, pos: 60, role: admin)
        $this->addMenu('modules', 'Module Manager', 'admin/modules', 'fa-puzzle-piece', 60, 'admin');

        // 6. Settings: System & Tools (parent, pos: 70, role: admin)
        $this->addMenu('settings', 'System & Tools', '#', 'fa-sliders', 70, 'admin');
        $this->addSubmenu('settings', 'settings_options', 'Site Options', 'admin/settings', 10, 'admin', 'fa-sliders');
        $this->addSubmenu('settings', 'settings_users', 'Users', 'admin/users', 20, 'admin', 'fa-users');
        $this->addSubmenu('settings', 'settings_notifications', 'Notifications', 'admin/notifications', 30, 'admin', 'fa-envelope');
        $this->addSubmenu('settings', 'settings_backup', 'Backup & Sync', 'admin/import-export', 40, 'admin', 'fa-file-export');
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->registerCoreMenus();

        if (function_exists('do_action')) {
            do_action('admin_menu');
        }

        $this->booted = true;
    }

    public function getMenus(): array
    {
        $this->boot();

        $userRole = $_SESSION['ICTM_Auth']['role'] ?? '';

        $filteredMenus = [];

        foreach ($this->menus as $menuId => $menu) {
            $filteredSubmenus = [];
            if (!empty($menu['submenus'])) {
                foreach ($menu['submenus'] as $subId => $submenu) {
                    if ($this->userCanAccess($userRole, $submenu['role'] ?? 'admin')) {
                        $filteredSubmenus[$subId] = $submenu;
                    }
                }

                uasort($filteredSubmenus, function ($a, $b) {
                    return ($a['position'] ?? 50) <=> ($b['position'] ?? 50);
                });
            }

            $parentAccessible = $this->userCanAccess($userRole, $menu['role'] ?? 'admin');

            if (($menu['url'] === '#' || empty($menu['url'])) && !empty($menu['submenus'])) {
                if (empty($filteredSubmenus)) {
                    continue;
                }
            } elseif (!$parentAccessible) {
                continue;
            }

            $menu['submenus'] = array_values($filteredSubmenus);
            $filteredMenus[$menuId] = $menu;
        }

        uasort($filteredMenus, function ($a, $b) {
            return ($a['position'] ?? 50) <=> ($b['position'] ?? 50);
        });

        return array_values($filteredMenus);
    }

    public function reset(): void
    {
        $this->menus = [];
        $this->booted = false;
        $this->coreRegistered = false;
    }

    private function userCanAccess(string $userRole, string $requiredRole): bool
    {
        if (empty($userRole)) {
            return false;
        }

        if ($userRole === 'admin') {
            return true;
        }

        if ($requiredRole === 'admin') {
            return false;
        }

        if ($requiredRole === 'editor' && in_array($userRole, ['admin', 'editor'], true)) {
            return true;
        }

        if ($requiredRole === 'author' && in_array($userRole, ['admin', 'editor', 'author'], true)) {
            return true;
        }

        if ($requiredRole === 'subscriber' || $requiredRole === 'all' || $requiredRole === '') {
            return true;
        }

        return $userRole === $requiredRole;
    }
}
