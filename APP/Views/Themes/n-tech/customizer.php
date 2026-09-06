{{ $this->view('admin/layout/header', ['title' => 'N-Tech Theme Customizer']) }}

<?php
$theme_name = $theme_name ?? 'n-tech';
$config = $config ?? [];

$raw_css = $config['custom_css'] ?? '';
$ntech_config = [];
if (preg_match('/\*NTECH_CONFIG:(.*?):END_NTECH_CONFIG\*/s', $raw_css, $matches)) {
    $ntech_config = json_decode($matches[1], true) ?: [];
}
$clean_css = trim(preg_replace('/\*NTECH_CONFIG:(.*?):END_NTECH_CONFIG\*/s', '', $raw_css));

// Fallback Defaults
$nConfig = array_merge([
    'header_layout' => 'style1',
    'footer_layout' => 'style1',
    'get_in_touch_layout' => 'option1',
    'primary_color' => '#0d6efd',
    'secondary_color' => '#0b5ed7',
    'bg_color' => '#ffffff',
    'heading_font' => 'Plus Jakarta Sans',
    'body_font' => 'Inter',
    
    // Hero Banner Defaults
    'hero_title_1' => 'Transforming Business Through Technology & Innovation',
    'hero_subtitle_1' => 'We engineer scalable enterprise solutions, cloud architectures, and AI systems.',
    'hero_btn_text_1' => 'Explore Solutions',
    'hero_btn_link_1' => pathto('services'),
    'hero_image_1' => pathto('Themes/n-tech/images/hero/hero-1.png'),

    'hero_title_2' => 'Next-Gen IT Consultancy & Digital Engineering',
    'hero_subtitle_2' => 'Empowering modern organizations with secure digital infrastructure.',
    'hero_btn_text_2' => 'Our Services',
    'hero_btn_link_2' => pathto('services'),
    'hero_image_2' => pathto('Themes/n-tech/images/hero/hero-2.png'),

    'hero_title_3' => 'Accelerate Enterprise Growth with N-Tech',
    'hero_subtitle_3' => 'Dedicated engineering teams delivering rapid time-to-market.',
    'hero_btn_text_3' => 'Contact Team',
    'hero_btn_link_3' => pathto('contact'),
    'hero_image_3' => pathto('Themes/n-tech/images/hero/hero-3.png'),

    // About Page Section Defaults
    'about_heading' => 'Empowering Digital Evolution Through High-Performance Engineering',
    'about_lead' => 'N-Tech is a premier technology consulting firm specializing in high-throughput enterprise architectures, cloud migration, AI deployment, and robust cybersecurity.',
    'about_description' => 'Founded by veteran software architects and system engineers, our team partners with mid-market enterprises and tech innovators to turn complex challenges into competitive advantages.',
    'about_mission' => 'To deliver resilient, scalable, and secure software platforms that accelerate enterprise growth and safeguard mission-critical data.',
    'about_stat1_num' => '99.99%',
    'about_stat1_label' => 'Uptime & Service Reliability',
    'about_stat2_num' => '150+',
    'about_stat2_label' => 'Enterprise Deployments',

    // Services Slider Defaults
    'services_title' => 'Our Core Services',
    'services_subtitle' => 'End-to-End Enterprise Tech Solutions',
    'services_limit' => '6',

    // Logo Slider Defaults
    'logo_slider_title' => 'Trusted by Industry Leaders Worldwide',
    'logo_slider_enable' => '1',
    'logo_1' => pathto('Themes/n-tech/images/brand/brand-1.png'),
    'logo_2' => pathto('Themes/n-tech/images/brand/brand-2.png'),
    'logo_3' => pathto('Themes/n-tech/images/brand/brand-3.png'),
    'logo_4' => pathto('Themes/n-tech/images/brand/brand-4.png'),

    // Contact Defaults
    'contact_email' => 'contact@ntech.com',
    'contact_phone' => '+1 (800) 555-0199',
    'contact_address' => '100 Innovation Way, Tech District, CA 94016',
    'contact_map_url' => 'https://maps.google.com/maps?q=California&t=&z=13&ie=UTF8&iwloc=&output=embed',

    // Newsletter & Global Settings
    'newsletter_title' => 'Subscribe to Tech Insights',
    'newsletter_subtitle' => 'Get the latest trends in software engineering and cloud transformation.',
    'newsletter_action' => '#',
    'show_breadcrumbs' => '1',
    'wow_animations' => '1',
    'parallax_hero' => '1',
], $ntech_config, $config);
?>

<div class="card card-custom wow animate__animated animate__fadeInUp">
    <div class="card-header card-custom-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fa-solid fa-wand-magic-sparkles me-2"></i> N-Tech Theme Customizer Dashboard</h5>
        <a href="{{ pathto('') }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye me-1"></i> Preview Front-end</a>
    </div>

    <div class="card-body card-custom-body">
        <form action="{{ pathto('admin/theme/customize/' . $theme_name) }}" method="POST" id="ntech-customizer-form">
            <input type="hidden" name="ntech_raw_custom_css" id="ntech_raw_custom_css" value="{{ htmlspecialchars($clean_css, ENT_QUOTES, 'UTF-8') }}">

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs mb-4" id="customizerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="header-footer-tab" data-bs-toggle="tab" data-bs-target="#header-footer" type="button" role="tab"><i class="fa-solid fa-layer-group me-1"></i> Header & Footer</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="styling-fonts-tab" data-bs-toggle="tab" data-bs-target="#styling-fonts" type="button" role="tab"><i class="fa-solid fa-palette me-1"></i> Typography & Styling</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="hero-slider-tab" data-bs-toggle="tab" data-bs-target="#hero-slider" type="button" role="tab"><i class="fa-solid fa-image me-1"></i> Hero Banners</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about-section-tab" type="button" role="tab"><i class="fa-solid fa-building me-1"></i> About Page</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sections-tab" data-bs-toggle="tab" data-bs-target="#sections" type="button" role="tab"><i class="fa-solid fa-boxes-stacked me-1"></i> Sections & Sliders</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-section" type="button" role="tab"><i class="fa-solid fa-envelope me-1"></i> Contact & Get in Touch</button>
                </li>
            </ul>

            <div class="tab-content" id="customizerTabContent">
                
                <!-- TAB 1: HEADER & FOOTER -->
                <div class="tab-pane fade show active" id="header-footer" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6><i class="fa-solid fa-heading me-1"></i> Selectable Header Styles (3 Options)</h6>
                                <hr>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="header_layout" id="header_style1" value="style1" {{ $nConfig['header_layout'] === 'style1' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="header_style1">
                                        Header Style 1 (Standard Tech & Navbar)
                                    </label>
                                    <small class="text-muted d-block">Clean navigation bar with top quick stats and main navigation container.</small>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="header_layout" id="header_style2" value="style2" {{ $nConfig['header_layout'] === 'style2' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="header_style2">
                                        Header Style 2 (Transparent Overlay & Hero Integration)
                                    </label>
                                    <small class="text-muted d-block">Sleek transparent navigation header designed for dark/hero backgrounds.</small>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="header_layout" id="header_style3" value="style3" {{ $nConfig['header_layout'] === 'style3' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="header_style3">
                                        Header Style 3 (Corporate Topbar & Extended Contact)
                                    </label>
                                    <small class="text-muted d-block">Includes top notification bar, social icons, and phone / email direct links.</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6><i class="fa-solid fa-shoe-prints me-1"></i> Selectable Footer Styles (3 Options)</h6>
                                <hr>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="footer_layout" id="footer_style1" value="style1" {{ $nConfig['footer_layout'] === 'style1' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="footer_style1">
                                        Footer Style 1 (Multi-Column Detailed Corporate)
                                    </label>
                                    <small class="text-muted d-block">Full 4-column layout with corporate bio, quick links, recent posts, and newsletter.</small>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="footer_layout" id="footer_style2" value="style2" {{ $nConfig['footer_layout'] === 'style2' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="footer_style2">
                                        Footer Style 2 (Minimal Tech Grid)
                                    </label>
                                    <small class="text-muted d-block">Clean 2-column layout focused on branding, quick sitemap, and social links.</small>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="footer_layout" id="footer_style3" value="style3" {{ $nConfig['footer_layout'] === 'style3' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="footer_style3">
                                        Footer Style 3 (Newsletter Focus & CTA Banner)
                                    </label>
                                    <small class="text-muted d-block">Highlights a newsletter callout header with copyright & legal policies.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: STYLING & GOOGLE FONTS -->
                <div class="tab-pane fade" id="styling-fonts" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6><i class="fa-solid fa-font me-1"></i> Google Fonts Picker Tool</h6>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Heading Font Family</label>
                                    <select class="form-select" name="heading_font">
                                        <?php
                                        $fonts = ['Plus Jakarta Sans', 'Outfit', 'Inter', 'Roboto', 'Poppins', 'Montserrat', 'Open Sans'];
                                        foreach ($fonts as $font) {
                                            $selected = ($nConfig['heading_font'] === $font) ? 'selected' : '';
                                            echo "<option value=\"$font\" $selected>$font</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Body Font Family</label>
                                    <select class="form-select" name="body_font">
                                        <?php
                                        foreach ($fonts as $font) {
                                            $selected = ($nConfig['body_font'] === $font) ? 'selected' : '';
                                            echo "<option value=\"$font\" $selected>$font</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6><i class="fa-solid fa-palette me-1"></i> Theme Color Palette</h6>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Primary Accent Color</label>
                                    <input type="color" class="form-control form-control-color w-100" name="primary_color" value="{{ $nConfig['primary_color'] }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Secondary Accent Color</label>
                                    <input type="color" class="form-control form-control-color w-100" name="secondary_color" value="{{ $nConfig['secondary_color'] }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: HERO BANNERS -->
                <div class="tab-pane fade" id="hero-slider" role="tabpanel">
                    <div class="accordion" id="heroAccordion">
                        <?php for ($i = 1; $i <= 3; $i++) { ?>
                            <div class="accordion-item mb-2">
                                <h2 class="accordion-header" id="heroHeading{{ $i }}">
                                    <button class="accordion-button {{ $i > 1 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#heroCollapse{{ $i }}">
                                        <i class="fa-solid fa-sliders me-2"></i> Hero Slide {{ $i }} Settings
                                    </button>
                                </h2>
                                <div id="heroCollapse{{ $i }}" class="accordion-collapse collapse {{ $i === 1 ? 'show' : '' }}" data-bs-parent="#heroAccordion">
                                    <div class="accordion-body bg-light">
                                        <div class="mb-3">
                                            <label class="form-label font-weight-bold">Slide {{ $i }} Main Title</label>
                                            <input type="text" class="form-control" name="hero_title_{{ $i }}" value="{{ htmlspecialchars($nConfig['hero_title_' . $i], ENT_QUOTES, 'UTF-8') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label font-weight-bold">Slide {{ $i }} Subtitle / Lead Text</label>
                                            <textarea class="form-control" name="hero_subtitle_{{ $i }}" rows="2">{{ htmlspecialchars($nConfig['hero_subtitle_' . $i], ENT_QUOTES, 'UTF-8') }}</textarea>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label font-weight-bold">Button Text</label>
                                                <input type="text" class="form-control" name="hero_btn_text_{{ $i }}" value="{{ htmlspecialchars($nConfig['hero_btn_text_' . $i], ENT_QUOTES, 'UTF-8') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label font-weight-bold">Button Link URL</label>
                                                <input type="text" class="form-control" name="hero_btn_link_{{ $i }}" value="{{ htmlspecialchars($nConfig['hero_btn_link_' . $i], ENT_QUOTES, 'UTF-8') }}">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label font-weight-bold">Slide {{ $i }} Image URL</label>
                                            <input type="text" class="form-control" name="hero_image_{{ $i }}" value="{{ htmlspecialchars($nConfig['hero_image_' . $i], ENT_QUOTES, 'UTF-8') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- TAB 4: ABOUT PAGE CUSTOMIZATION -->
                <div class="tab-pane fade" id="about-section-tab" role="tabpanel">
                    <div class="p-3 border rounded bg-light mb-4">
                        <h6><i class="fa-solid fa-building me-1"></i> About Page Section Customizations</h6>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">About Page Main Heading</label>
                            <input type="text" class="form-control" name="about_heading" value="{{ htmlspecialchars($nConfig['about_heading'], ENT_QUOTES, 'UTF-8') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Lead Summary Paragraph</label>
                            <textarea class="form-control" name="about_lead" rows="2">{{ htmlspecialchars($nConfig['about_lead'], ENT_QUOTES, 'UTF-8') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Detailed Overview Text</label>
                            <textarea class="form-control" name="about_description" rows="3">{{ htmlspecialchars($nConfig['about_description'], ENT_QUOTES, 'UTF-8') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Core Mission Statement</label>
                            <textarea class="form-control" name="about_mission" rows="2">{{ htmlspecialchars($nConfig['about_mission'], ENT_QUOTES, 'UTF-8') }}</textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Stat 1 Number / Value</label>
                                <input type="text" class="form-control" name="about_stat1_num" value="{{ htmlspecialchars($nConfig['about_stat1_num'], ENT_QUOTES, 'UTF-8') }}">
                                <label class="form-label font-weight-bold mt-2">Stat 1 Label</label>
                                <input type="text" class="form-control" name="about_stat1_label" value="{{ htmlspecialchars($nConfig['about_stat1_label'], ENT_QUOTES, 'UTF-8') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Stat 2 Number / Value</label>
                                <input type="text" class="form-control" name="about_stat2_num" value="{{ htmlspecialchars($nConfig['about_stat2_num'], ENT_QUOTES, 'UTF-8') }}">
                                <label class="form-label font-weight-bold mt-2">Stat 2 Label</label>
                                <input type="text" class="form-control" name="about_stat2_label" value="{{ htmlspecialchars($nConfig['about_stat2_label'], ENT_QUOTES, 'UTF-8') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: SECTIONS & SLIDERS -->
                <div class="tab-pane fade" id="sections" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6><i class="fa-solid fa-list-check me-1"></i> Services Section Settings</h6>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Section Title</label>
                                    <input type="text" class="form-control" name="services_title" value="{{ htmlspecialchars($nConfig['services_title'], ENT_QUOTES, 'UTF-8') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Section Subtitle</label>
                                    <input type="text" class="form-control" name="services_subtitle" value="{{ htmlspecialchars($nConfig['services_subtitle'], ENT_QUOTES, 'UTF-8') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Maximum Items Displayed</label>
                                    <input type="number" class="form-control" name="services_limit" value="{{ $nConfig['services_limit'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6><i class="fa-solid fa-handshake me-1"></i> Corporate Logo Slider Panel</h6>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Logo Slider Header Title</label>
                                    <input type="text" class="form-control" name="logo_slider_title" value="{{ htmlspecialchars($nConfig['logo_slider_title'], ENT_QUOTES, 'UTF-8') }}">
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="logo_slider_enable" value="1" id="logo_slider_enable" {{ $nConfig['logo_slider_enable'] === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="logo_slider_enable">Enable Corporate Logo Slider</label>
                                </div>
                                <?php for ($l = 1; $l <= 4; $l++) { ?>
                                    <div class="mb-2">
                                        <label class="form-label small">Partner Logo {{ $l }} URL</label>
                                        <input type="text" class="form-control form-control-sm" name="logo_{{ $l }}" value="{{ htmlspecialchars($nConfig['logo_' . $l], ENT_QUOTES, 'UTF-8') }}">
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 6: CONTACT & GET IN TOUCH -->
                <div class="tab-pane fade" id="contact-section" role="tabpanel">
                    <div class="p-3 border rounded bg-light mb-4">
                        <h6><i class="fa-solid fa-address-book me-1"></i> "Get in Touch" Section Layout (3 Options)</h6>
                        <hr>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-check card p-3 h-100">
                                    <input class="form-check-input" type="radio" name="get_in_touch_layout" id="git_opt1" value="option1" {{ $nConfig['get_in_touch_layout'] === 'option1' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold ms-2" for="git_opt1">Option 1: Modern Card Box</label>
                                    <small class="text-muted d-block mt-2">Elevated contact cards with interactive icon buttons and direct email form.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check card p-3 h-100">
                                    <input class="form-check-input" type="radio" name="get_in_touch_layout" id="git_opt2" value="option2" {{ $nConfig['get_in_touch_layout'] === 'option2' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold ms-2" for="git_opt2">Option 2: Side Form & Embedded Map</label>
                                    <small class="text-muted d-block mt-2">Side-by-side interactive contact form with integrated Google Map.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check card p-3 h-100">
                                    <input class="form-check-input" type="radio" name="get_in_touch_layout" id="git_opt3" value="option3" {{ $nConfig['get_in_touch_layout'] === 'option3' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold ms-2" for="git_opt3">Option 3: Split Dark/Light Panel</label>
                                    <small class="text-muted d-block mt-2">High-contrast split panel layout with corporate office details.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6><i class="fa-solid fa-circle-info me-1"></i> Corporate Contact Information</h6>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Primary Email</label>
                                    <input type="email" class="form-control" name="contact_email" value="{{ htmlspecialchars($nConfig['contact_email'], ENT_QUOTES, 'UTF-8') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Primary Phone</label>
                                    <input type="text" class="form-control" name="contact_phone" value="{{ htmlspecialchars($nConfig['contact_phone'], ENT_QUOTES, 'UTF-8') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Physical Address</label>
                                    <input type="text" class="form-control" name="contact_address" value="{{ htmlspecialchars($nConfig['contact_address'], ENT_QUOTES, 'UTF-8') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Google Map Embed URL</label>
                                    <input type="text" class="form-control" name="contact_map_url" value="{{ htmlspecialchars($nConfig['contact_map_url'], ENT_QUOTES, 'UTF-8') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h6><i class="fa-solid fa-paper-plane me-1"></i> Newsletter Module Settings</h6>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Newsletter Title</label>
                                    <input type="text" class="form-control" name="newsletter_title" value="{{ htmlspecialchars($nConfig['newsletter_title'], ENT_QUOTES, 'UTF-8') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Newsletter Subtitle</label>
                                    <textarea class="form-control" name="newsletter_subtitle" rows="2">{{ htmlspecialchars($nConfig['newsletter_subtitle'], ENT_QUOTES, 'UTF-8') }}</textarea>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_breadcrumbs" value="1" id="show_breadcrumbs" {{ $nConfig['show_breadcrumbs'] === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="show_breadcrumbs">Enable Page Breadcrumbs</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="wow_animations" value="1" id="wow_animations" {{ $nConfig['wow_animations'] === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="wow_animations">Enable WOW.js & AOS Entrance Animations</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-floppy-disk me-1"></i> Save N-Tech Customization Settings</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('ntech-customizer-form');
        if (form) {
            form.addEventListener('submit', function() {
                const configData = {
                    header_layout: form.querySelector('[name="header_layout"]:checked')?.value || 'style1',
                    footer_layout: form.querySelector('[name="footer_layout"]:checked')?.value || 'style1',
                    get_in_touch_layout: form.querySelector('[name="get_in_touch_layout"]:checked')?.value || 'option1',
                    heading_font: form.querySelector('[name="heading_font"]')?.value || 'Plus Jakarta Sans',
                    body_font: form.querySelector('[name="body_font"]')?.value || 'Inter',
                    primary_color: form.querySelector('[name="primary_color"]')?.value || '#0d6efd',
                    secondary_color: form.querySelector('[name="secondary_color"]')?.value || '#0b5ed7',
                    
                    hero_title_1: form.querySelector('[name="hero_title_1"]')?.value || '',
                    hero_subtitle_1: form.querySelector('[name="hero_subtitle_1"]')?.value || '',
                    hero_btn_text_1: form.querySelector('[name="hero_btn_text_1"]')?.value || '',
                    hero_btn_link_1: form.querySelector('[name="hero_btn_link_1"]')?.value || '',
                    hero_image_1: form.querySelector('[name="hero_image_1"]')?.value || '',

                    hero_title_2: form.querySelector('[name="hero_title_2"]')?.value || '',
                    hero_subtitle_2: form.querySelector('[name="hero_subtitle_2"]')?.value || '',
                    hero_btn_text_2: form.querySelector('[name="hero_btn_text_2"]')?.value || '',
                    hero_btn_link_2: form.querySelector('[name="hero_btn_link_2"]')?.value || '',
                    hero_image_2: form.querySelector('[name="hero_image_2"]')?.value || '',

                    hero_title_3: form.querySelector('[name="hero_title_3"]')?.value || '',
                    hero_subtitle_3: form.querySelector('[name="hero_subtitle_3"]')?.value || '',
                    hero_btn_text_3: form.querySelector('[name="hero_btn_text_3"]')?.value || '',
                    hero_btn_link_3: form.querySelector('[name="hero_btn_link_3"]')?.value || '',
                    hero_image_3: form.querySelector('[name="hero_image_3"]')?.value || '',

                    about_heading: form.querySelector('[name="about_heading"]')?.value || '',
                    about_lead: form.querySelector('[name="about_lead"]')?.value || '',
                    about_description: form.querySelector('[name="about_description"]')?.value || '',
                    about_mission: form.querySelector('[name="about_mission"]')?.value || '',
                    about_stat1_num: form.querySelector('[name="about_stat1_num"]')?.value || '',
                    about_stat1_label: form.querySelector('[name="about_stat1_label"]')?.value || '',
                    about_stat2_num: form.querySelector('[name="about_stat2_num"]')?.value || '',
                    about_stat2_label: form.querySelector('[name="about_stat2_label"]')?.value || '',

                    services_title: form.querySelector('[name="services_title"]')?.value || '',
                    services_subtitle: form.querySelector('[name="services_subtitle"]')?.value || '',
                    services_limit: form.querySelector('[name="services_limit"]')?.value || '6',

                    logo_slider_title: form.querySelector('[name="logo_slider_title"]')?.value || '',
                    logo_slider_enable: form.querySelector('[name="logo_slider_enable"]')?.checked ? '1' : '0',
                    logo_1: form.querySelector('[name="logo_1"]')?.value || '',
                    logo_2: form.querySelector('[name="logo_2"]')?.value || '',
                    logo_3: form.querySelector('[name="logo_3"]')?.value || '',
                    logo_4: form.querySelector('[name="logo_4"]')?.value || '',

                    contact_email: form.querySelector('[name="contact_email"]')?.value || '',
                    contact_phone: form.querySelector('[name="contact_phone"]')?.value || '',
                    contact_address: form.querySelector('[name="contact_address"]')?.value || '',
                    contact_map_url: form.querySelector('[name="contact_map_url"]')?.value || '',

                    newsletter_title: form.querySelector('[name="newsletter_title"]')?.value || '',
                    newsletter_subtitle: form.querySelector('[name="newsletter_subtitle"]')?.value || '',
                    show_breadcrumbs: form.querySelector('[name="show_breadcrumbs"]')?.checked ? '1' : '0',
                    wow_animations: form.querySelector('[name="wow_animations"]')?.checked ? '1' : '0',
                };

                const rawCssEl = document.getElementById('ntech_raw_custom_css');
                const userCss = rawCssEl ? rawCssEl.value : '';
                const jsonBlob = '/*NTECH_CONFIG:' + JSON.stringify(configData) + ':END_NTECH_CONFIG*/';
                
                // Append serialized blob
                let customCssField = form.querySelector('[name="custom_css"]');
                if (!customCssField) {
                    customCssField = document.createElement('input');
                    customCssField.type = 'hidden';
                    customCssField.name = 'custom_css';
                    form.appendChild(customCssField);
                }
                customCssField.value = userCss + (userCss ? "\n" : "") + jsonBlob;
            });
        }
    });
</script>

{{ $this->view('admin/layout/footer') }}
