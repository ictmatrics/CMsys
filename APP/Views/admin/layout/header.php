<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | CMsys Control Panel</title>
    <!-- Core CSS -->
    <link href="{{ pathto('css/bootstrap5.3.8.min.css') }}" rel="stylesheet">
    <link href="{{ pathto('css/style.css') }}" rel="stylesheet">
    <!-- Third-party CDN libraries -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #1e2229;
            color: #fff;
            transition: all 0.3s;
            min-height: 100vh;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: #1a1d24;
            border-bottom: 1px solid #2d323e;
        }
        #sidebar ul.components {
            padding: 20px 0;
            border-bottom: 1px solid #2d323e;
        }
        #sidebar ul p {
            color: #fff;
            padding: 10px;
        }
        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 1.1em;
            display: block;
            color: #a3aab4;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        #sidebar ul li a:hover {
            color: #fff;
            background: #2d323e;
        }
        #sidebar ul li.active > a {
            color: #fff;
            background: #2b7bf5;
        }
        #sidebar a::after,
        #sidebar .dropdown-toggle::after {
            display: none !important;
        }
        #sidebar .arrow-icon {
            font-size: 0.75rem;
            transition: transform 0.25s ease-in-out;
        }
        #sidebar a[data-bs-toggle="collapse"][aria-expanded="true"] .arrow-icon,
        #sidebar a[data-bs-toggle="collapse"]:not(.collapsed) .arrow-icon {
            transform: rotate(180deg);
        }
        #sidebar ul.submenu {
            background: #16191f;
            padding: 4px 0;
            list-style: none;
        }
        #sidebar ul.submenu li a {
            padding: 8px 20px 8px 42px;
            font-size: 0.92rem;
            color: #8c95a3;
            display: block;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        #sidebar ul.submenu li a:hover {
            color: #fff;
            background: #252b37;
            padding-left: 46px;
        }
        #sidebar ul.submenu li.active > a {
            color: #fff;
            background: #2b7bf5;
            font-weight: 600;
        }
        #content {
            width: 100%;
            padding: 30px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        .navbar-admin {
            background: #fff;
            border: none;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 15px 20px;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            background: #fff;
        }
        .card-custom-header {
            background: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px 30px;
            font-weight: 700;
        }
        .card-custom-body {
            padding: 30px;
        }
    </style>
    <?php do_action('admin_head'); ?>
</head>
<body>
    <div class="wrapper animate__animated animate__fadeIn">
        <!-- Sidebar Navigation -->
        <nav id="sidebar">
            <div class="sidebar-header text-center">
                <h3 class="logo-text font-weight-bold mb-0" style="background: linear-gradient(135deg, #2b7bf5, #7d2ae8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">CMsys</h3>
                <small class="text-muted">Control Panel</small>
            </div>
            
            <?php
            require_once APPPATH . 'Helpers/admin_menu_helper.php';
            $adminMenus = get_admin_menus();

            $currentUri = trim((string)parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
            $basePath = trim((string)parse_url(BASE_URL, PHP_URL_PATH), '/');
            if ($basePath !== '' && str_starts_with($currentUri, $basePath)) {
                $currentUri = trim(substr($currentUri, strlen($basePath)), '/');
            }

            $isItemActive = function (array $item, string $pageTitle, string $currentPath): bool {
                if (!empty($item['title']) && strcasecmp($item['title'], $pageTitle) === 0) {
                    return true;
                }

                $itemUrl = trim($item['url'] ?? '', '/');
                if ($itemUrl !== '' && $itemUrl !== '#') {
                    if ($currentPath === $itemUrl || str_starts_with($currentPath, $itemUrl . '/')) {
                        return true;
                    }
                }

                $titleLower = strtolower($pageTitle);
                $itemId = $item['id'] ?? '';

                if ($itemId === 'dashboard' && $titleLower === 'dashboard') {
                    return true;
                }
                if ($itemId === 'posts' && str_contains($titleLower, 'post') && !str_contains($titleLower, 'custom post')) {
                    return true;
                }
                if ($itemId === 'pages' && str_contains($titleLower, 'page') && !str_contains($titleLower, 'custom')) {
                    return true;
                }
                if ($itemId === 'categories' && $titleLower === 'categories') {
                    return true;
                }
                if ($itemId === 'tags' && $titleLower === 'tags') {
                    return true;
                }
                if ($itemId === 'media' && ($titleLower === 'media library' || str_contains($titleLower, 'media'))) {
                    return true;
                }
                if ($itemId === 'themes' && ($titleLower === 'theme manager' || str_contains($titleLower, 'theme'))) {
                    return true;
                }
                if ($itemId === 'menus' && ($titleLower === 'menu manager' || str_contains($titleLower, 'menu'))) {
                    return true;
                }
                if ($itemId === 'widgets' && ($titleLower === 'widgets & sidebars' || str_contains($titleLower, 'widget'))) {
                    return true;
                }
                if ($itemId === 'modules' && ($titleLower === 'module manager' || str_contains($titleLower, 'module'))) {
                    return true;
                }
                if ($itemId === 'cpts_manage' && ($titleLower === 'custom post types' || str_contains($titleLower, 'cpt'))) {
                    return true;
                }
                if ($itemId === 'settings_options' && ($titleLower === 'system settings' || $titleLower === 'site options')) {
                    return true;
                }
                if ($itemId === 'settings_users' && ($titleLower === 'user management' || str_contains($titleLower, 'user'))) {
                    return true;
                }
                if ($itemId === 'settings_notifications' && ($titleLower === 'email smtp configurations' || str_contains($titleLower, 'notification'))) {
                    return true;
                }
                if ($itemId === 'settings_backup' && ($titleLower === 'import & export' || str_contains($titleLower, 'backup'))) {
                    return true;
                }

                return false;
            };
            ?>

            <ul class="list-unstyled components">
                <?php foreach ($adminMenus as $menu) { ?>
                    <?php
                    $hasSubmenus = !empty($menu['submenus']);
                    $isParentActive = false;
                    $evaluatedSubmenus = [];

                    if ($hasSubmenus) {
                        foreach ($menu['submenus'] as $subItem) {
                            $subActive = $isItemActive($subItem, $title, $currentUri);
                            if ($subActive) {
                                $isParentActive = true;
                            }
                            $subItem['is_active'] = $subActive;
                            $evaluatedSubmenus[] = $subItem;
                        }
                    } else {
                        $isParentActive = $isItemActive($menu, $title, $currentUri);
                    }
                    ?>

                    <?php if ($hasSubmenus) { ?>
                        <li class="sidebar-item {{ $isParentActive ? 'active' : '' }}">
                            <a href="#collapse-{{ $menu['id'] }}" 
                               data-bs-toggle="collapse" 
                               aria-expanded="{{ $isParentActive ? 'true' : 'false' }}" 
                               class="d-flex align-items-center justify-content-between {{ $isParentActive ? '' : 'collapsed' }}">
                                <span>
                                    <?php if (!empty($menu['icon'])) { ?>
                                        <i class="fa-solid {{ $menu['icon'] }} me-2"></i>
                                    <?php } ?>
                                    {{ $menu['title'] }}
                                </span>
                                <i class="fa-solid fa-chevron-down arrow-icon"></i>
                            </a>
                            <ul class="collapse list-unstyled submenu {{ $isParentActive ? 'show' : '' }}" 
                                id="collapse-{{ $menu['id'] }}">
                                <?php foreach ($evaluatedSubmenus as $sub) { ?>
                                    <li class="{{ $sub['is_active'] ? 'active' : '' }}">
                                        <a href="{{ pathto($sub['url']) }}">
                                            <?php if (!empty($sub['icon'])) { ?>
                                                <i class="fa-solid {{ $sub['icon'] }} me-2"></i>
                                            <?php } ?>
                                            {{ $sub['title'] }}
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } else { ?>
                        <li class="{{ $isParentActive ? 'active' : '' }}">
                            <a href="{{ pathto($menu['url']) }}">
                                <?php if (!empty($menu['icon'])) { ?>
                                    <i class="fa-solid {{ $menu['icon'] }} me-2"></i>
                                <?php } ?>
                                {{ $menu['title'] }}
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </nav>

        <!-- Main Content Area -->
        <div id="content">
            <nav class="navbar navbar-admin d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 font-weight-bold">{{ $title }}</h4>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3 text-muted">Welcome, <strong>{{ $_SESSION['ICTM_Auth']['username'] }}</strong> ({{ ucwords($_SESSION['ICTM_Auth']['role']) }})</span>
                    <a href="{{ pathto('') }}" class="btn btn-sm btn-outline-secondary me-2" target="_blank"><i class="fa-solid fa-globe"></i> View Site</a>
                    <a href="{{ pathto('logout') }}" class="btn btn-sm btn-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </div>
            </nav>
            
            <!-- Alert messaging wrapper -->
            <div class="mb-4">
                <?php flash('success_msg'); ?>
                <?php flash('error_msg'); ?>
                <div id="flash-message" style="display:none; position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
            </div>
