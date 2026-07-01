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
            background: #111417; /* Darker sidebar for contrast */
            color: #fff;
            transition: all 0.3s;
            min-height: 100vh;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: #0d1012;
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
            background: #e82a5c; /* Neon pink accent for dark theme */
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
        
        /* ----------------------------------------------------- */
        /* DARK THEME OVERRIDES */
        /* ----------------------------------------------------- */
        body {
            background-color: #121212 !important;
            color: #e0e0e0 !important;
        }
        .navbar-admin, .card-custom, .bg-white {
            background: #1e1e1e !important;
            color: #e0e0e0 !important;
            box-shadow: none !important;
            border: 1px solid #333 !important;
        }
        .text-dark {
            color: #e0e0e0 !important;
        }
        .text-muted {
            color: #8a929a !important;
        }
        .card-custom-header, .table th, .border-bottom {
            border-bottom-color: #333 !important;
            color: #e0e0e0 !important;
        }
        td, th, .table {
            color: #e0e0e0 !important;
            background-color: transparent !important;
            border-color: #333 !important;
        }
        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        input, select, textarea, .form-control {
            background-color: #2c2c2c !important;
            color: #e0e0e0 !important;
            border-color: #444 !important;
        }
        input:focus, select:focus, textarea:focus, .form-control:focus {
            background-color: #333 !important;
            color: #fff !important;
            border-color: #e82a5c !important;
            box-shadow: 0 0 0 0.2rem rgba(232, 42, 92, 0.25) !important;
        }
        .btn-primary {
            background-color: #e82a5c !important;
            border-color: #e82a5c !important;
        }
        .btn-outline-primary {
            color: #e82a5c !important;
            border-color: #e82a5c !important;
        }
        .btn-outline-primary:hover {
            background-color: #e82a5c !important;
            color: #fff !important;
        }
        .nav-tabs .nav-link {
            color: #a3aab4 !important;
        }
        .nav-tabs .nav-link.active {
            background-color: #1e1e1e !important;
            border-color: #333 #333 #1e1e1e !important;
            color: #e82a5c !important;
        }
        .list-group-item {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
            color: #e0e0e0 !important;
        }
    </style>
</head>
<body>
    <div class="wrapper animate__animated animate__fadeIn">
        <!-- Sidebar Navigation -->
        <nav id="sidebar">
            <div class="sidebar-header text-center">
                <h3 class="logo-text font-weight-bold mb-0" style="background: linear-gradient(135deg, #e82a5c, #ff7e5f); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">CMsys</h3>
                <small class="text-muted">Dark Control</small>
            </div>
            
            <ul class="list-unstyled components">
                <li class="{{ $title === 'Dashboard' ? 'active' : '' }}">
                    <a href="{{ pathto('admin/dashboard') }}"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
                </li>
                <li class="{{ str_contains($title, 'Post') ? 'active' : '' }}">
                    <a href="{{ pathto('admin/posts') }}"><i class="fa-solid fa-file-lines me-2"></i> Posts</a>
                </li>
                <li class="{{ str_contains($title, 'Page') ? 'active' : '' }}">
                    <a href="{{ pathto('admin/pages') }}"><i class="fa-solid fa-file me-2"></i> Pages</a>
                </li>
                <li class="{{ $title === 'Categories' ? 'active' : '' }}">
                    <a href="{{ pathto('admin/categories') }}"><i class="fa-solid fa-folder me-2"></i> Categories</a>
                </li>
                <li class="{{ $title === 'Tags' ? 'active' : '' }}">
                    <a href="{{ pathto('admin/tags') }}"><i class="fa-solid fa-tags me-2"></i> Tags</a>
                </li>
                <li class="{{ $title === 'Media Library' ? 'active' : '' }}">
                    <a href="{{ pathto('admin/media') }}"><i class="fa-solid fa-images me-2"></i> Media Library</a>
                </li>
                <li class="{{ $title === 'Menu Manager' ? 'active' : '' }}">
                    <a href="{{ pathto('admin/menus') }}"><i class="fa-solid fa-bars me-2"></i> Navigation Menus</a>
                </li>
                <li class="{{ $title === 'Widgets & Sidebars' ? 'active' : '' }}">
                    <a href="{{ pathto('admin/widgets') }}"><i class="fa-solid fa-shapes me-2"></i> Widgets & Layouts</a>
                </li>
                
                <!-- Custom Post Types section -->
                <?php
                $cptsJson = (new \App\Models\OptionModel())->getOption('custom_post_types', '[]');
                $cptsList = json_decode($cptsJson, true);
                if (!empty($cptsList)) {
                    foreach ($cptsList as $slug => $cpt) {
                        $isActive = ($title === $cpt['label']);
                        ?>
                        <li class="{{ $isActive ? 'active' : '' }}">
                            <a href="{{ pathto('admin/cpt/entries/' . $slug) }}"><i class="fa-solid fa-hashtag me-2"></i> {{ $cpt['label'] }}</a>
                        </li>
                        <?php
                    }
                }
                ?>

                <!-- Admin Only Menus -->
                <?php if ($_SESSION['ICTM_Auth']['role'] === 'admin') { ?>
                    <li class="{{ $title === 'Custom Post Types' ? 'active' : '' ?>">
                        <a href="{{ pathto('admin/cpts') }}"><i class="fa-solid fa-gear me-2"></i> Custom Post Types</a>
                    </li>
                    <li class="{{ $title === 'Theme Manager' ? 'active' : '' ?>">
                        <a href="{{ pathto('admin/themes') }}"><i class="fa-solid fa-palette me-2"></i> Theme Manager</a>
                    </li>
                    <li class="{{ $title === 'Module Manager' ? 'active' : '' ?>">
                        <a href="{{ pathto('admin/modules') }}"><i class="fa-solid fa-puzzle-piece me-2"></i> Module Manager</a>
                    </li>
                    <li class="{{ $title === 'User Management' ? 'active' : '' }}">
                        <a href="{{ pathto('admin/users') }}"><i class="fa-solid fa-users me-2"></i> User Management</a>
                    </li>
                    <li class="{{ $title === 'Email SMTP Configurations' ? 'active' : '' }}">
                        <a href="{{ pathto('admin/notifications') }}"><i class="fa-solid fa-envelope me-2"></i> Notifications Settings</a>
                    </li>
                    <li class="{{ $title === 'Import & Export' ? 'active' : '' }}">
                        <a href="{{ pathto('admin/import-export') }}"><i class="fa-solid fa-file-export me-2"></i> Backup & Sync</a>
                    </li>
                    <li class="{{ $title === 'System Settings' ? 'active' : '' ?>">
                        <a href="{{ pathto('admin/settings') }}"><i class="fa-solid fa-sliders me-2"></i> Site Options</a>
                    </li>
                <?php } ?>
            </ul>
        </nav>

        <!-- Main Content Area -->
        <div id="content">
            <nav class="navbar navbar-admin d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 font-weight-bold text-white">{{ $title }}</h4>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3 text-muted">Welcome, <strong class="text-white">{{ $_SESSION['ICTM_Auth']['username'] }}</strong> ({{ ucwords($_SESSION['ICTM_Auth']['role']) }})</span>
                    <a href="{{ pathto('') }}" class="btn btn-sm btn-outline-secondary me-2 text-white border-secondary" target="_blank"><i class="fa-solid fa-globe"></i> View Site</a>
                    <a href="{{ pathto('logout') }}" class="btn btn-sm btn-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </div>
            </nav>
            
            <!-- Alert messaging wrapper -->
            <div class="mb-4">
                <?php flash('success_msg'); ?>
                <?php flash('error_msg'); ?>
                <div id="flash-message" style="display:none; position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
            </div>
