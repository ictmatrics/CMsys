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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f0f4f8;
            color: #1e293b;
        }
        .navbar-frontend {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            padding: 20px 0;
            box-shadow: 0 4px 20px rgba(15, 32, 39, 0.15);
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 26px;
            color: #fff !important;
            letter-spacing: -0.5px;
        }
        .nav-link {
            font-weight: 500;
            color: #cbd5e1 !important;
            padding: 8px 18px !important;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #38bdf8 !important;
            transform: translateY(-1px);
        }
        .hero-banner {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 100%);
            color: #fff;
            padding: 120px 0;
            margin-bottom: 60px;
            box-shadow: inset 0 -10px 20px rgba(0,0,0,0.1);
        }
        .card-post {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(15, 32, 39, 0.05);
            transition: all 0.3s ease;
            background: #fff;
            margin-bottom: 40px;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .card-post:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 35px rgba(15, 32, 39, 0.1);
        }
        .widget-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(15, 32, 39, 0.05);
            background: #fff;
            padding: 26px;
            margin-bottom: 30px;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .widget-title {
            font-weight: 700;
            font-size: 19px;
            border-bottom: 3px solid #0ea5e9;
            padding-bottom: 12px;
            margin-bottom: 22px;
            color: #0f2027;
        }
        footer {
            background-color: #0f2027;
            color: #94a3b8;
            padding: 70px 0 30px 0;
            margin-top: 100px;
        }
        footer a {
            color: #cbd5e1;
            text-decoration: none;
        }
        footer a:hover {
            color: #38bdf8;
        }
        {{ $custom_css }}
    </style>
    {{ $custom_js_header }}
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-frontend sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ pathto('') }}">
                <?php if (!empty($site_logo)) { ?>
                    <img src="{{ pathto($site_logo) }}" height="42" class="me-2">
                <?php } else { ?>
                    <i class="fa-solid fa-anchor text-info me-2 animate__animated animate__pulse animate__infinite"></i> {{ $site_title }}
                <?php } ?>
            </a>
            
            <button class="navbar-toggler border-light text-light" type="button" data-bs-toggle="collapse" data-bs-target="#frontendNavbar">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
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
