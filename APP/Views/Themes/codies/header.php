<?php
require_once APPPATH . 'Views/Themes/codies/functions.php';

// Ensure layout variables are defined with safe defaults
$title = $title ?? '';
$site_title = $site_title ?? '';
$site_description = $site_description ?? '';
$site_logo = $site_logo ?? '';
$site_favicon = $site_favicon ?? '';
$site_loader = $site_loader ?? '';
$custom_css = $custom_css ?? '';
$custom_js_header = $custom_js_header ?? '';
$main_menu_items = $main_menu_items ?? [];
$theme_config = $theme_config ?? [];

// Decode serialized Codies configs from custom_css comment block
$raw_css_config = $theme_config['custom_css'] ?? '';
$codies_config = [];
if (preg_match('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', $raw_css_config, $matches)) {
    $codies_config = json_decode($matches[1], true) ?: [];
}
$theme_config = array_merge($theme_config, $codies_config);

// Load custom styling variables
$primary_color = !empty($theme_config['primary_color']) ? $theme_config['primary_color'] : '#3b82f6';
$secondary_color = !empty($theme_config['secondary_color']) ? $theme_config['secondary_color'] : '#1e293b';
$bg_color = !empty($theme_config['bg_color']) ? $theme_config['bg_color'] : '#f8fafc';
$card_bg_color = !empty($theme_config['card_bg_color']) ? $theme_config['card_bg_color'] : '#ffffff';
$border_color = !empty($theme_config['border_color']) ? $theme_config['border_color'] : '#e2e8f0';

$heading_font = !empty($theme_config['heading_font']) ? $theme_config['heading_font'] : 'Plus Jakarta Sans';
$body_font = !empty($theme_config['body_font']) ? $theme_config['body_font'] : 'Plus Jakarta Sans';

// Expose clean custom CSS code (excluding the JSON comment block)
$theme_custom_css = trim(preg_replace('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', '', $raw_css_config));
$custom_css_code = $theme_config['custom_css_code'] ?? '';
$header_scripts = $theme_config['header_scripts'] ?? '';

$default_color_mode = $theme_config['default_color_mode'] ?? 'light';
$is_sticky = !empty($theme_config['sticky_header']) ? 'sticky-top' : '';

$header_layout = $theme_config['header_layout'] ?? 'layout1';
$reading_progress_enabled = !empty($theme_config['reading_progress']);

// New extended config values
$header_bg         = !empty($theme_config['header_bg'])           ? $theme_config['header_bg']           : $card_bg_color;
$header_text_color = !empty($theme_config['header_text_color'])   ? $theme_config['header_text_color']   : $secondary_color;
$header_border_col = !empty($theme_config['header_border_color']) ? $theme_config['header_border_color'] : $border_color;
$footer_bg         = !empty($theme_config['footer_bg'])           ? $theme_config['footer_bg']           : '#0f172a';
$footer_text_color = !empty($theme_config['footer_text_color'])   ? $theme_config['footer_text_color']   : '#94a3b8';
$footer_heading_color = !empty($theme_config['footer_heading_color']) ? $theme_config['footer_heading_color'] : '#f8fafc';
$footer_link_color = !empty($theme_config['footer_link_color'])   ? $theme_config['footer_link_color']   : '#94a3b8';
$menu_font_size    = !empty($theme_config['menu_font_size'])      ? (int)$theme_config['menu_font_size'] : 15;
$menu_font_weight  = !empty($theme_config['menu_font_weight'])    ? $theme_config['menu_font_weight']    : '600';
$footer_font_size  = !empty($theme_config['footer_font_size'])    ? (int)$theme_config['footer_font_size'] : 14;
$h1_size = !empty($theme_config['h1_size']) ? (float)$theme_config['h1_size'] : 2.5;
$h2_size = !empty($theme_config['h2_size']) ? (float)$theme_config['h2_size'] : 2.0;
$h3_size = !empty($theme_config['h3_size']) ? (float)$theme_config['h3_size'] : 1.6;
$h4_size = !empty($theme_config['h4_size']) ? (float)$theme_config['h4_size'] : 1.3;
$h5_size = !empty($theme_config['h5_size']) ? (float)$theme_config['h5_size'] : 1.1;
$h6_size = !empty($theme_config['h6_size']) ? (float)$theme_config['h6_size'] : 1.0;

// Helper: pick a random banner from pipe-separated multi-img list
if (!function_exists('resolve_ad_img')) {
    function resolve_ad_img(array $config, string $slot): string {
        $imgs_raw = $config['ad_' . $slot . '_imgs'] ?? $config['ad_' . $slot . '_img'] ?? '';
        $imgs = array_values(array_filter(array_map('trim', explode('|', $imgs_raw))));
        return !empty($imgs) ? $imgs[array_rand($imgs)] : '';
    }
}
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

    <!-- Bootstrap 5 CSS -->
    <link href="{{ pathto('css/bootstrap5.3.8.min.css') }}" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Animate.css for WOW transitions -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Prism.js Syntax Highlighting & Line Numbers -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.css">

    <!-- Custom Theme Stylesheet -->
    <link rel="stylesheet" href="{{ pathto('css/style.css') }}">

    <!-- Load Google Fonts dynamically -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($heading_font) }}:wght@400;500;600;700;800&family={{ urlencode($body_font) }}:wght@400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">

    <style>
        /* Modern design tokens mapping */
        :root {
            --bg-primary: {{ $bg_color }};
            --bg-card: {{ $card_bg_color }};
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --border-color: {{ $border_color }};
            --accent-color: {{ $primary_color }};
            --accent-rgb: {{ implode(',', sscanf($primary_color, "#%02x%02x%02x")) }};
            --accent-hover: {{ $primary_color }}dd;
            --navbar-bg: {{ $header_bg }};
            --navbar-text: {{ $header_text_color }};
            --navbar-border: {{ $header_border_col }};
            --footer-bg: {{ $footer_bg }};
            --footer-text: {{ $footer_text_color }};
            --footer-heading: {{ $footer_heading_color }};
            --footer-link: {{ $footer_link_color }};
            --footer-font-size: {{ $footer_font_size }}px;
            --menu-font-size: {{ $menu_font_size }}px;
            --menu-font-weight: {{ $menu_font_weight }};
            --h1-size: {{ $h1_size }}rem;
            --h2-size: {{ $h2_size }}rem;
            --h3-size: {{ $h3_size }}rem;
            --h4-size: {{ $h4_size }}rem;
            --h5-size: {{ $h5_size }}rem;
            --h6-size: {{ $h6_size }}rem;
            --ad-bg: rgba(var(--accent-rgb), 0.04);
            --code-block-bg: #1e293b;
            --toc-active-bg: rgba(var(--accent-rgb), 0.08);
            --highlight-box: rgba(var(--accent-rgb), 0.04);
        }

        /* Dark Mode Override */
        body.theme-dark {
            --bg-primary: #0f172a;
            --bg-card: #1e293b;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --border-color: #334155;
            --accent-color: #38bdf8;
            --accent-rgb: 56, 189, 248;
            --accent-hover: #7dd3fc;
            --navbar-bg: #1e293b;
            --navbar-text: #cbd5e1;
            --navbar-border: #334155;
            --ad-bg: #151e2e;
            --code-block-bg: #0f172a;
            --toc-active-bg: rgba(56, 189, 248, 0.12);
            --highlight-box: #1e293b;
        }

        /* Sepia Mode Override */
        body.theme-sepia {
            --bg-primary: #f4ecd8;
            --bg-card: #fdf6e3;
            --text-primary: #5b4636;
            --text-secondary: #8f725b;
            --border-color: #e4d4b3;
            --accent-color: #b58900;
            --accent-rgb: 181, 137, 0;
            --accent-hover: #cb4b16;
            --navbar-bg: #fdf6e3;
            --navbar-text: #5b4636;
            --navbar-border: #e4d4b3;
            --ad-bg: #eedfbe;
            --code-block-bg: #2d241c;
            --toc-active-bg: rgba(181, 137, 0, 0.1);
            --highlight-box: #f9f5eb;
        }

        /* Dynamic Typography Scale */
        h1 { font-size: var(--h1-size) !important; }
        h2 { font-size: var(--h2-size) !important; }
        h3 { font-size: var(--h3-size) !important; }
        h4 { font-size: var(--h4-size) !important; }
        h5 { font-size: var(--h5-size) !important; }
        h6 { font-size: var(--h6-size) !important; }

        /* Load dynamic Codies theme stylesheet */
        <?php
        $codies_css = file_get_contents(APPPATH . 'Views/Themes/codies/codies.css');
        echo str_replace(
            ['{{ $body_font }}', '{{ $heading_font }}'],
            [$body_font, $heading_font],
            $codies_css
        );
        ?>

        /* Apply dynamic overrides */
        {{ $custom_css }}
        {{ $theme_custom_css }}
        {{ $custom_css_code }}
    </style>

    {{ $custom_js_header }}
    {{ $header_scripts }}
</head>
<body class="theme-{{ $default_color_mode }} font-md">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('codies_theme_mode');
            if (savedTheme) {
                document.body.classList.remove('theme-light', 'theme-dark', 'theme-sepia');
                document.body.classList.add('theme-' + savedTheme);
            }
        })();
    </script>

    <?php if ($reading_progress_enabled) { ?>
        <!-- Reading Progress Visual Indicator -->
        <div class="reading-progress-container">
            <div class="reading-progress-bar" id="readingProgress"></div>
        </div>
    <?php } ?>

    <?php if (!empty($site_loader)) { ?>
        <!-- Preloader -->
        <div id="preloader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--bg-primary); z-index: 999999; display: flex; align-items: center; justify-content: center; transition: opacity 0.5s ease, visibility 0.5s ease;">
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
    <?php } ?>

    <!-- ========================================== -->
    <!-- HEADER LAYOUT SWITCHER                     -->
    <!-- ========================================== -->

    <?php if ($header_layout === 'layout1') { ?>
        <!-- HEADER LAYOUT 1: Logo Left, Leaderboard Sponsor Right -->
        <header class="py-3 border-bottom" style="background-color: var(--bg-card); transition: background-color 0.3s ease;">
            <div class="container">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-4 text-center text-md-start">
                        <a href="{{ pathto('') }}" class="d-inline-flex align-items-center text-decoration-none">
                            <?php if (!empty($site_logo)) { ?>
                                <img src="{{ pathto($site_logo) }}" alt="{{ htmlspecialchars($site_title, ENT_QUOTES, 'UTF-8') }}" style="max-height: 40px; object-fit: contain;">
                            <?php } else { ?>
                                <span class="fs-2 logo-font">
                                    <span class="logo-bracket">&lt;</span>Codies<span class="logo-bracket">/&gt;</span>
                                </span>
                            <?php } ?>
                        </a>
                        
                    </div>
                    <div class="col-12 col-md-8 d-flex justify-content-center justify-content-md-end">
                        <?php if (!empty($theme_config['ad_leaderboard_enable'])) {
                            $lb_img = resolve_ad_img($theme_config, 'leaderboard');
                        ?>
                            <div class="ad-leaderboard">
                                <span class="ad-tag text-muted">Sponsor</span>
                                <?php if (!empty($theme_config['ad_leaderboard_html'])) { ?>
                                    {{ htmlspecialchars_decode($theme_config['ad_leaderboard_html']) }}
                                <?php } else { ?>
                                    <a href="{{ htmlspecialchars($theme_config['ad_leaderboard_link'] ?? '#', ENT_QUOTES, 'UTF-8') }}" target="_blank">
                                        <?php if (!empty($lb_img)) { ?>
                                            <img src="{{ pathto($lb_img) }}" alt="{{ htmlspecialchars($theme_config['ad_leaderboard_alt'] ?? 'Ad', ENT_QUOTES, 'UTF-8') }}" class="w-100 h-100" style="object-fit: cover;">
                                        <?php } else { ?>
                                            <div class="p-3 text-center d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-bolt text-warning fa-2x animate__animated animate__pulse animate__infinite"></i>
                                                <div>
                                                    <strong>Accelerate with Vercel Pro</strong>
                                                    <span class="d-none d-sm-block text-muted small">Unlock Edge Caching and optimize performance.</span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </header>

        <nav class="navbar navbar-expand-lg sticky-navbar py-2 {{ $is_sticky }}">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa-solid fa-bars" style="color: var(--text-primary);"></i>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ pathto('') }}"><i class="fa-solid fa-house me-2"></i>Home</a>
                        </li>
                        <?php foreach ($main_menu_items as $item) { 
                            $href = $item['type'] === 'custom' ? $item['url'] : ($item['type'] === 'page' ? pathto((new \App\Models\PostModel())->getPostById((int)$item['object_id'])->slug) : pathto('category/' . (new \App\Models\TaxonomyModel())->getTaxonomyById((int)$item['object_id'])->slug));
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $href }}">{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="config-btn-group ms-auto ms-lg-3">
                    <button class="config-btn {{ $default_color_mode === 'light' ? 'active' : '' }}" data-theme="light" title="Light Mode">
                        <i class="fa-solid fa-sun"></i><span class="d-none d-sm-inline ms-1">Light</span>
                    </button>
                    <button class="config-btn {{ $default_color_mode === 'dark' ? 'active' : '' }}" data-theme="dark" title="Dark Mode">
                        <i class="fa-solid fa-moon"></i><span class="d-none d-sm-inline ms-1">Dark</span>
                    </button>
                    <button class="config-btn {{ $default_color_mode === 'sepia' ? 'active' : '' }}" data-theme="sepia" title="Sepia Mode">
                        <i class="fa-solid fa-palette"></i><span class="d-none d-sm-inline ms-1">Sepia</span>
                    </button>
                </div>
            </div>
        </nav>

    <?php } elseif ($header_layout === 'layout2') { ?>
        <!-- HEADER LAYOUT 2: Centered Logo + Tagline + Navigation Row Below -->
        <header class="py-4 border-bottom text-center" style="background-color: var(--bg-card); transition: background-color 0.3s ease;">
            <div class="container position-relative">
                <div class="d-inline-block">
                    <a href="{{ pathto('') }}" class="text-decoration-none">
                        <?php if (!empty($site_logo)) { ?>
                            <img src="{{ pathto($site_logo) }}" alt="{{ htmlspecialchars($site_title, ENT_QUOTES, 'UTF-8') }}" style="max-height: 50px; object-fit: contain;">
                        <?php } else { ?>
                            <span class="fs-1 logo-font">
                                <span class="logo-bracket">&lt;</span>Codies<span class="logo-bracket">/&gt;</span>
                            </span>
                        <?php } ?>
                    </a>
                    
                </div>
            </div>
        </header>

        <nav class="navbar navbar-expand-lg sticky-navbar py-2 {{ $is_sticky }}">
            <div class="container justify-content-center position-relative">
                <button class="navbar-toggler mx-auto mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#centeredNavbar" aria-controls="centeredNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa-solid fa-bars" style="color: var(--text-primary);"></i> Menu
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="centeredNavbar">
                    <ul class="navbar-nav gap-2">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ pathto('') }}"><i class="fa-solid fa-house me-2"></i>Home</a>
                        </li>
                        <?php foreach ($main_menu_items as $item) { 
                            $href = $item['type'] === 'custom' ? $item['url'] : ($item['type'] === 'page' ? pathto((new \App\Models\PostModel())->getPostById((int)$item['object_id'])->slug) : pathto('category/' . (new \App\Models\TaxonomyModel())->getTaxonomyById((int)$item['object_id'])->slug));
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $href }}">{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="config-btn-group position-absolute end-0 me-3 d-none d-lg-flex">
                    <button class="config-btn {{ $default_color_mode === 'light' ? 'active' : '' }}" data-theme="light" title="Light Mode">
                        <i class="fa-solid fa-sun"></i><span class="d-none d-sm-inline ms-1">Light</span>
                    </button>
                    <button class="config-btn {{ $default_color_mode === 'dark' ? 'active' : '' }}" data-theme="dark" title="Dark Mode">
                        <i class="fa-solid fa-moon"></i><span class="d-none d-sm-inline ms-1">Dark</span>
                    </button>
                    <button class="config-btn {{ $default_color_mode === 'sepia' ? 'active' : '' }}" data-theme="sepia" title="Sepia Mode">
                        <i class="fa-solid fa-palette"></i><span class="d-none d-sm-inline ms-1">Sepia</span>
                    </button>
                </div>
            </div>
        </nav>

    <?php } else { ?>
        <!-- HEADER LAYOUT 3: Compact Inline Layout with Search -->
        <nav class="navbar navbar-expand-lg sticky-navbar py-3 {{ $is_sticky }}">
            <div class="container">
                <a href="{{ pathto('') }}" class="navbar-brand d-flex align-items-center text-decoration-none me-4">
                    <?php if (!empty($site_logo)) { ?>
                        <img src="{{ pathto($site_logo) }}" alt="{{ htmlspecialchars($site_title, ENT_QUOTES, 'UTF-8') }}" style="max-height: 30px; object-fit: contain;">
                    <?php } else { ?>
                        <span class="fs-3 logo-font m-0">
                            <span class="logo-bracket">&lt;</span>Codies<span class="logo-bracket">/&gt;</span>
                        </span>
                    <?php } ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#compactNavbar" aria-controls="compactNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa-solid fa-bars" style="color: var(--text-primary);"></i>
                </button>
                <div class="collapse navbar-collapse" id="compactNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ pathto('') }}">Home</a>
                        </li>
                        <?php foreach ($main_menu_items as $item) { 
                            $href = $item['type'] === 'custom' ? $item['url'] : ($item['type'] === 'page' ? pathto((new \App\Models\PostModel())->getPostById((int)$item['object_id'])->slug) : pathto('category/' . (new \App\Models\TaxonomyModel())->getTaxonomyById((int)$item['object_id'])->slug));
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $href }}">{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}</a>
                            </li>
                        <?php } ?>
                    </ul>
                    <form class="d-flex me-lg-3" action="{{ pathto('') }}" method="GET">
                        <div class="input-group">
                            <input class="form-control form-control-sm border-end-0" type="search" name="s" placeholder="Search ..." aria-label="Search">
                            <button class="btn btn-outline-secondary border-start-0 btn-sm bg-white" type="submit">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="config-btn-group ms-auto ms-lg-0">
                    <button class="config-btn {{ $default_color_mode === 'light' ? 'active' : '' }}" data-theme="light" title="Light Mode">
                        <i class="fa-solid fa-sun"></i><span class="d-none d-sm-inline ms-1">Light</span>
                    </button>
                    <button class="config-btn {{ $default_color_mode === 'dark' ? 'active' : '' }}" data-theme="dark" title="Dark Mode">
                        <i class="fa-solid fa-moon"></i><span class="d-none d-sm-inline ms-1">Dark</span>
                    </button>
                    <button class="config-btn {{ $default_color_mode === 'sepia' ? 'active' : '' }}" data-theme="sepia" title="Sepia Mode">
                        <i class="fa-solid fa-palette"></i><span class="d-none d-sm-inline ms-1">Sepia</span>
                    </button>
                </div>
            </div>
        </nav>
    <?php } ?>
