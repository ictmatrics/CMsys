<?php
require_once APPPATH . 'Views/Themes/n-tech/functions.php';

$siteTitle = $site_title ?? 'N-Tech Enterprise';
$siteDescription = $site_description ?? 'Next-Gen Technology Solutions';
$pageTitle = isset($title) ? $title . ' - ' . $siteTitle : $siteTitle;

$nConfig = array_merge([
    'header_layout' => 'style1',
    'primary_color' => '#0d6efd',
    'secondary_color' => '#0b5ed7',
    'heading_font' => 'Plus Jakarta Sans',
    'body_font' => 'Inter',
    'contact_email' => 'contact@ntech.com',
    'contact_phone' => '+1 (800) 555-0199',
    'wow_animations' => '1',
], $theme_config ?? []);

$headingFont = urlencode($nConfig['heading_font']);
$bodyFont = urlencode($nConfig['body_font']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8') }}">

    <?php if (!empty($site_favicon)) { ?>
        <link rel="icon" href="{{ pathto($site_favicon) }}" type="image/x-icon">
    <?php } ?>

    <!-- Dynamic Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ $headingFont }}:wght@400;600;700;800&family={{ $bodyFont }}:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Local Theme Migrated Vendor Styles -->
    <link rel="stylesheet" href="{{ pathto('Themes/n-tech/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ pathto('Themes/n-tech/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ pathto('Themes/n-tech/css/aos-animation.css') }}">
    <link rel="stylesheet" href="{{ pathto('Themes/n-tech/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ pathto('Themes/n-tech/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ pathto('Themes/n-tech/css/owl.min.css') }}">
    <link rel="stylesheet" href="{{ pathto('Themes/n-tech/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary-color: {{ $nConfig['primary_color'] }};
            --secondary-color: {{ $nConfig['secondary_color'] }};
            --heading-font: '{{ $nConfig['heading_font'] }}', sans-serif;
            --body-font: '{{ $nConfig['body_font'] }}', sans-serif;
        }
        body, .ntech-theme {
            font-family: var(--body-font);
            background-color: {{ $nConfig['bg_color'] ?? '#ffffff' }};
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .ntech-theme h1,
        .ntech-theme h2,
        .ntech-theme h3,
        .ntech-theme h4,
        .ntech-theme h5,
        .ntech-theme h6,
        .ntech-theme .heading-font {
            font-family: var(--heading-font);
        }
        .ntech-theme .btn-theme-primary {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            transition: all 0.3s ease;
        }
        .ntech-theme .btn-theme-primary:hover {
            background-color: var(--secondary-color);
            color: #ffffff;
        }
    </style>

    <?php if (!empty($custom_css)) { ?>
        <style>
            {{ $custom_css }}
        </style>
    <?php } ?>
</head>
<body class="ntech-theme">

    <?php if ($nConfig['header_layout'] === 'style3') { ?>
        <!-- HEADER STYLE 3: Topbar Corporate -->
        <div class="bg-dark text-white py-2 border-bottom border-secondary">
            <div class="container d-flex justify-content-between align-items-center text-sm" style="font-size: 13px;">
                <div>
                    <i class="fa-solid fa-envelope me-2 text-primary"></i>{{ $nConfig['contact_email'] }}
                    <span class="mx-3">|</span>
                    <i class="fa-solid fa-phone me-2 text-primary"></i>{{ $nConfig['contact_phone'] }}
                </div>
                <div>
                    <a href="{{ pathto('faq') }}" class="text-white text-decoration-none me-3">FAQ</a>
                    <a href="{{ pathto('contact') }}" class="text-white text-decoration-none">Support</a>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- MAIN NAVBAR (Styles 1, 2, & 3 Support with Dynamic Project Menu Configuration) -->
    <header class="navbar navbar-expand-lg sticky-top shadow-sm {{ $nConfig['header_layout'] === 'style2' ? 'bg-dark navbar-dark' : 'bg-white navbar-light' }}">
        <div class="container">
            <a class="navbar-brand font-weight-bold d-flex align-items-center" href="{{ pathto('') }}">
                <?php if (!empty($site_logo)) { ?>
                    <img src="{{ pathto($site_logo) }}" alt="{{ $siteTitle }}" height="40" class="me-2">
                <?php } else { ?>
                    <span class="h4 mb-0 text-primary fw-bold"><i class="fa-solid fa-microchip me-2"></i>N-Tech</span>
                <?php } ?>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ntechMainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="ntechMainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
                    <?php
                    $menuItems = $main_menu_items ?? ($mainMenuItems ?? []);
                    if (!empty($menuItems)) {
                        foreach ($menuItems as $item) {
                            $itemObj = is_object($item) ? $item : (object)$item;
                            $mTitle = $itemObj->title ?? ($itemObj->label ?? '');
                            $mUrl = $itemObj->url ?? ($itemObj->link ?? '#');
                            if (empty($mTitle)) continue;
                            $targetAttr = !empty($itemObj->target) ? 'target="' . htmlspecialchars($itemObj->target, ENT_QUOTES, 'UTF-8') . '"' : '';
                            $navHref = ntech_normalize_menu_url((string)$mUrl);
                    ?>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $navHref }}" {{ $targetAttr }}>{{ htmlspecialchars($mTitle, ENT_QUOTES, 'UTF-8') }}</a>
                            </li>
                    <?php 
                        } 
                    } else { 
                    ?>
                        <!-- Default Dynamic Fallback Navigation -->
                        <li class="nav-item"><a class="nav-link" href="{{ pathto('') }}">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ pathto('about') }}">About Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ pathto('services') }}">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ pathto('products') }}">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ pathto('faq') }}">FAQ</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ pathto('blog') }}">Blog</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ pathto('contact') }}">Contact</a></li>
                    <?php } ?>
                </ul>

                <div class="d-flex align-items-center">
                    <a href="{{ pathto('contact') }}" class="btn btn-theme-primary px-4 py-2 rounded-pill fw-bold">
                        Get In Touch <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>
