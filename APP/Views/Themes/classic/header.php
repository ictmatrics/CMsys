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
    
    <?php if (!empty($site_favicon)) { ?>
        <link rel="icon" href="{{ pathto($site_favicon) }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ pathto($site_favicon) }}" type="image/x-icon">
    <?php } ?>
    
    <!-- Core Bootstrap 5.3 -->
    <link href="{{ pathto('css/bootstrap5.3.8.min.css') }}" rel="stylesheet">
    <!-- FontAwesome & Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Load dynamic Classic theme stylesheet */
        <?php
        $classic_css = file_get_contents(APPPATH . 'Views/Themes/classic/classic.css');
        echo str_replace(
            ['{{ $primary_color }}', '{{ $secondary_color }}'],
            [$primary_color, $secondary_color],
            $classic_css
        );
        ?>

        /* Custom SEO scripts & header tags style injection */
        {{ $custom_css }}
        
        /* Theme Customizer CSS */
        {{ $theme_custom_css }}
    </style>
    
    {{ $custom_js_header }}
    <?php do_action('wp_head'); ?>
</head>
<body>
    <?php if (!empty($site_loader)) { ?>
        <!-- Preloader -->
        <div id="preloader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #ffffff; z-index: 999999; display: flex; align-items: center; justify-content: center; transition: opacity 0.5s ease, visibility 0.5s ease;">
            <div class="preloader-inner" style="text-align: center;">
                <img src="{{ pathto($site_loader) }}" alt="Loading..." style="max-height: 100px; animation: wowZoomIn 10s ease-in-out infinite;">
            </div>
        </div>
        <script>
            window.addEventListener('load', function() {
                var preloader = document.getElementById('preloader');
                if (preloader) {
                    preloader.style.opacity = '0';
                    preloader.style.visibility = 'hidden';
                    setTimeout(function() {
                        preloader.remove();
                    }, 500);
                }
            });
        </script>
        <style>
            @keyframes wowZoomIn {
                0% {
                    opacity: 0;
                    transform: scale3d(0.3, 0.3, 0.3);
                }
                50% {
                    opacity: 1;
                    transform: scale3d(1.1, 1.1, 1.1);
                }
                100% {
                    opacity: 0.9;
                    transform: scale3d(1, 1, 1);
                }
            }
        </style>
    <?php } ?>
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
                            $href = $item['type'] === 'custom' ? $item['url'] : ($item['type'] === 'page' ? pathto((new \App\Models\PostModel())->getPostById((int)$item['object_id'])->slug) : pathto('category/' . (new \App\Models\TaxonomyModel())->getTaxonomyById((int)$item['object_id'])->slug));
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
