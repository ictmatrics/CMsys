<?php
// Ensure layout variables are defined with safe defaults
$title         = $title ?? '';
$site_title    = $site_title ?? '';
$site_description = $site_description ?? '';
$site_logo     = $site_logo ?? '';
$site_favicon  = $site_favicon ?? '';
$site_loader   = $site_loader ?? '';
$custom_css    = $custom_css ?? '';
$custom_js_header = $custom_js_header ?? '';
$custom_js_footer = $custom_js_footer ?? '';
$main_menu_items  = $main_menu_items ?? [];
$footer_menu_items = $footer_menu_items ?? [];
$widget_settings   = $widget_settings ?? ['sidebar_layout'=>'right','sidebar_widgets'=>[]];
$widget_categories = $widget_categories ?? [];
$widget_tags       = $widget_tags ?? [];
$widget_recent_posts = $widget_recent_posts ?? [];
$widget_pages      = $widget_pages ?? [];
$site_email        = $site_email ?? '';
$theme_config      = $theme_config ?? [];

$primary_color = !empty($theme_config['primary_color']) ? $theme_config['primary_color'] : '#2b7bf5';
$secondary_color = !empty($theme_config['secondary_color']) ? $theme_config['secondary_color'] : '#1a202c';
$theme_custom_css = !empty($theme_config['custom_css']) ? $theme_config['custom_css'] : '';
$is_sticky = !empty($theme_config['sticky_header']) ? 'sticky-top' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}{{ $title ? ' | ' : '' }}{{ $site_title }}</title>
    <meta name="description" content="{{ htmlspecialchars($site_description, ENT_QUOTES, 'UTF-8') }}">
    
    <!-- Core Bootstrap 5.3 -->
    <link href="{{ pathto('css/bootstrap5.3.8.min.css') }}" rel="stylesheet">
    <!-- FontAwesome & Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fafbfe;
            color: #2d3748;
        }
        .text-primary {
            color: {{ $primary_color }} !important;
        }
        .btn-primary {
            background-color: {{ $primary_color }} !important;
            border-color: {{ $primary_color }} !important;
        }
        .btn-outline-primary {
            color: {{ $primary_color }} !important;
            border-color: {{ $primary_color }} !important;
        }
        .btn-outline-primary:hover {
            background-color: {{ $primary_color }} !important;
            color: #fff !important;
        }
        .navbar-frontend {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #f0f0f0;
            padding: 18px 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.01);
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 24px;
            color: {{ $secondary_color }} !important;
        }
        .nav-link {
            font-weight: 600;
            color: #4a5568 !important;
            padding: 8px 16px !important;
        }
        .nav-link:hover {
            color: {{ $primary_color }} !important;
        }
        .hero-banner {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 100px 0;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 60px;
        }
        .card-post {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
            background: #fff;
            margin-bottom: 40px;
        }
        .card-post:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }
        .widget-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            background: #fff;
            padding: 24px;
            margin-bottom: 30px;
        }
        .widget-title {
            font-weight: 700;
            font-size: 18px;
            border-bottom: 2px solid #2b7bf5;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        footer {
            background-color: #1a202c;
            color: #a0aec0;
            padding: 60px 0 30px 0;
            margin-top: 80px;
        }
        footer a {
            color: #cbd5e0;
            text-decoration: none;
        }
        footer a:hover {
            color: #fff;
        }
        /* Custom SEO scripts & header tags style injection */
        {{ $custom_css }}
        
        /* Theme Customizer CSS */
        {{ $theme_custom_css }}
    </style>
    
    {{ $custom_js_header }}
</head>
<body>
    <!-- Topbar Navigation -->
    <nav class="navbar navbar-expand-lg navbar-frontend {{ $is_sticky }}">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ pathto('') }}">
                <?php if (!empty($site_logo)) { ?>
                    <img src="{{ pathto($site_logo) }}" height="40" class="me-2">
                <?php } else { ?>
                    <i class="fa-solid fa-shapes text-primary me-2"></i> {{ $site_title }}
                <?php } ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#frontendNavbar" aria-controls="frontendNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="frontendNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if (empty($main_menu_items)) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ pathto('') }}">Home</a>
                        </li>
                    <?php } else { ?>
                        <?php foreach ($main_menu_items as $item) { 
                            $href = $item['type'] === 'custom' ? $item['url'] : ($item['type'] === 'page' ? pathto('page/' . (new \App\Models\PostModel())->getPostById((int)$item['object_id'])->slug) : pathto('category/' . (new \App\Models\TaxonomyModel())->getTaxonomyById((int)$item['object_id'])->slug));
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $href }}">{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}</a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
