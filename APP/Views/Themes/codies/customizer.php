{{ $this->view('admin/layout/header', ['title' => 'Codies Theme Customizer']) }}

<?php
$theme_name = $theme_name ?? 'codies';
$config = $config ?? [];

$raw_css = $config['custom_css'] ?? '';
$codies_config = [];
if (preg_match('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', $raw_css, $matches)) {
    $codies_config = json_decode($matches[1], true) ?: [];
}
$clean_css = trim(preg_replace('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', '', $raw_css));

// Default Fallbacks
$cConfig = array_merge([
    'header_layout' => 'layout1',
    'footer_layout' => 'layout1',
    'primary_color' => '#3b82f6',
    'secondary_color' => '#1e293b',
    'bg_color' => '#f8fafc',
    'card_bg_color' => '#ffffff',
    'border_color' => '#e2e8f0',
    'default_color_mode' => 'light',
    'sticky_header' => '1',
    'parallax_hero' => '1',
    'wow_animations' => '1',
    'reading_progress' => '1',
    'table_of_contents' => '1',
    // Header color profile
    'header_bg' => '#ffffff',
    'header_text_color' => '#1e293b',
    'header_border_color' => '#e2e8f0',
    // Footer color profile
    'footer_bg' => '#0f172a',
    'footer_text_color' => '#94a3b8',
    'footer_heading_color' => '#f8fafc',
    'footer_link_color' => '#94a3b8',
    // Footer custom texts and social links
    'footer_about_text' => 'An elegant modern technical catalog constructed to help developers and designers bridge core logic and interface design paradigms seamlessly.',
    'footer_copyright' => 'ICT Matrics Private Limited. All rights reserved globally.',
    'footer_community_title' => 'Community Support',
    'footer_community_desc' => 'Engage directly with core maintainers and framework leads over discord and github discussion pools.',
    'footer_github' => '',
    'footer_discord' => '',
    'footer_twitter' => '',
    'footer_facebook' => '',
    'footer_youtube' => '',
    'footer_tiktok' => '',
    'body_font' => 'Plus Jakarta Sans',
    'menu_font_size' => '15',
    'menu_font_weight' => '600',
    'h1_size' => '2.5',
    'h2_size' => '2.0',
    'h3_size' => '1.6',
    'h4_size' => '1.3',
    'h5_size' => '1.1',
    'h6_size' => '1.0',
    'footer_font_size' => '14',
    'carousel_mode' => 'recent',
    'carousel_category' => '0',
    'home_layout' => 'grid',
    'archive_layout' => 'grid',
    'slide1_img' => '',
    'slide1_title' => '',
    'slide1_badge' => '',
    'slide1_lead' => '',
    'slide1_link' => '',
    'slide1_readtime' => '',
    'slide2_img' => '',
    'slide2_title' => '',
    'slide2_badge' => '',
    'slide2_lead' => '',
    'slide2_link' => '',
    'slide2_readtime' => '',
    'slide3_img' => '',
    'slide3_title' => '',
    'slide3_badge' => '',
    'slide3_lead' => '',
    'slide3_link' => '',
    'slide3_readtime' => '',
    'ad_leaderboard_enable' => '0',
    'ad_leaderboard_imgs' => '',
    'ad_leaderboard_link' => '',
    'ad_leaderboard_alt' => '',
    'ad_leaderboard_html' => '',
    'ad_sidebar_enable' => '0',
    'ad_sidebar_imgs' => '',
    'ad_sidebar_link' => '',
    'ad_sidebar_alt' => '',
    'ad_sidebar_html' => '',
    'ad_footer_enable' => '0',
    'ad_footer_imgs' => '',
    'ad_footer_link' => '',
    'ad_footer_alt' => '',
    'ad_footer_html' => '',
    'ad_infeed_enable' => '0',
    'ad_infeed_imgs' => '',
    'ad_infeed_link' => '',
    'ad_infeed_alt' => '',
    'ad_infeed_html' => '',
    'infeed_after_count' => '4',
    'featured_post_id' => '0'
], $codies_config);

$postModel = new \App\Models\PostModel();
$taxonomyModel = new \App\Models\TaxonomyModel();
$allPosts = $postModel->getAllPosts('post');
$allCategories = $taxonomyModel->getAllTaxonomies('category');

$googleFontsList = [
    'Inter' => 'Inter',
    'Plus Jakarta Sans' => 'Plus Jakarta Sans',
    ' Outfit' => 'Outfit',
    'Fira Code' => 'Fira Code',
    'Roboto' => 'Roboto',
    'Montserrat' => 'Montserrat',
    'Playfair Display' => 'Playfair Display'
];
?>

<div class="container-fluid px-4 py-3">
    <!-- Header Page title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Codies Customizer Panel</h1>
            <p class="text-muted mb-0 small fw-medium">Tune dynamic visual settings, typography, advertising banners, and featured posts.</p>
        </div>
        <div>
            <a href="{{ pathto('admin/themes') }}" class="btn btn-secondary px-4 py-2 fw-semibold shadow-sm">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Themes
            </a>
        </div>
    </div>

    <!-- Flash Alerts -->
    <div class="row">
        <div class="col-12">
            {{ flash('success_msg') }}
            {{ flash('error_msg') }}
        </div>
    </div>

    <!-- Main Customizer Form -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <form id="codiesCustomizerForm" action="{{ pathto('admin/theme/customize/' . $theme_name) }}" method="POST">

                <!-- Hidden outputs saved to standard system fields -->
                <input type="hidden" name="primary_color" id="primary_color_hidden" value="{{ $cConfig['primary_color'] }}">
                <input type="hidden" name="secondary_color" id="secondary_color_hidden" value="{{ $cConfig['secondary_color'] }}">
                <input type="hidden" name="sticky_header" id="sticky_header_hidden" value="{{ $cConfig['sticky_header'] }}">
                <input type="hidden" name="custom_css" id="custom_css">

                <!-- Settings Navigation tabs -->
                <ul class="nav nav-tabs mb-4" id="codiesTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="layout-tab" data-bs-toggle="tab" data-bs-target="#layout-pane" type="button" role="tab"><i class="fa-solid fa-layer-group me-1 text-primary"></i> Layout &amp; Colors</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="hf-tab" data-bs-toggle="tab" data-bs-target="#hf-pane" type="button" role="tab"><i class="fa-solid fa-swatchbook me-1 text-danger"></i> Header &amp; Footer</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="typo-tab" data-bs-toggle="tab" data-bs-target="#typo-pane" type="button" role="tab"><i class="fa-solid fa-font me-1 text-success"></i> Typography</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="carousel-tab" data-bs-toggle="tab" data-bs-target="#carousel-pane" type="button" role="tab"><i class="fa-solid fa-images me-1 text-warning"></i> Carousel Slides</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="ads-tab" data-bs-toggle="tab" data-bs-target="#ads-pane" type="button" role="tab"><i class="fa-solid fa-rectangle-ad me-1 text-danger"></i> Banner Ads</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="featured-tab" data-bs-toggle="tab" data-bs-target="#featured-pane" type="button" role="tab"><i class="fa-solid fa-thumbtack me-1 text-info"></i> Featured Post</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="advanced-tab" data-bs-toggle="tab" data-bs-target="#advanced-pane" type="button" role="tab"><i class="fa-solid fa-code me-1 text-secondary"></i> Advanced Styles</button>
                    </li>
                </ul>

                <div class="tab-content" id="codiesTabContent">
                    <!-- Tab Pane 1: Layout & Colors -->
                    <div class="tab-pane fade show active" id="layout-pane" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="header_layout" class="form-label fw-bold">Header Navigation Design</label>
                                <select class="form-select" id="header_layout">
                                    <option value="layout1" {{ $cConfig['header_layout'] === 'layout1' ? 'selected' : '' }}>Layout 1: Logo Left + Leaderboard Ad Right</option>
                                    <option value="layout2" {{ $cConfig['header_layout'] === 'layout2' ? 'selected' : '' }}>Layout 2: Center Logo + Minimal Navigation</option>
                                    <option value="layout3" {{ $cConfig['header_layout'] === 'layout3' ? 'selected' : '' }}>Layout 3: Compact Side-by-Side</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="footer_layout" class="form-label fw-bold">Footer Layout Design</label>
                                <select class="form-select" id="footer_layout">
                                    <option value="layout1" {{ $cConfig['footer_layout'] === 'layout1' ? 'selected' : '' }}>Layout 1: 4-Column Directory Grid</option>
                                    <option value="layout2" {{ $cConfig['footer_layout'] === 'layout2' ? 'selected' : '' }}>Layout 2: Centered Minimalist Row</option>
                                    <option value="layout3" {{ $cConfig['footer_layout'] === 'layout3' ? 'selected' : '' }}>Layout 3: Compact Side-by-Side Logo + Links</option>
                                </select>
                            </div>

                            <hr class="my-4">
                            <h5 class="fw-bold mb-3"><i class="fa-solid fa-palette me-1 text-primary"></i>Color Profiles</h5>

                            <div class="col-md-4">
                                <label for="primary_color" class="form-label fw-bold">Primary Accent Color</label>
                                <input type="color" class="form-control form-control-color w-100" id="primary_color" value="{{ $cConfig['primary_color'] }}">
                            </div>
                            <div class="col-md-4">
                                <label for="secondary_color" class="form-label fw-bold">Secondary Accent Color</label>
                                <input type="color" class="form-control form-control-color w-100" id="secondary_color" value="{{ $cConfig['secondary_color'] }}">
                            </div>
                            <div class="col-md-4">
                                <label for="bg_color" class="form-label fw-bold">Background Color</label>
                                <input type="color" class="form-control form-control-color w-100" id="bg_color" value="{{ $cConfig['bg_color'] }}">
                            </div>
                            <div class="col-md-6">
                                <label for="card_bg_color" class="form-label fw-bold">Card Component Background</label>
                                <input type="color" class="form-control form-control-color w-100" id="card_bg_color" value="{{ $cConfig['card_bg_color'] }}">
                            </div>
                            <div class="col-md-6">
                                <label for="border_color" class="form-label fw-bold">Border/Line Color</label>
                                <input type="color" class="form-control form-control-color w-100" id="border_color" value="{{ $cConfig['border_color'] }}">
                            </div>
                            <div class="col-md-6">
                                <label for="default_color_mode" class="form-label fw-bold">Default Mode</label>
                                <select class="form-select" id="default_color_mode">
                                    <option value="light" {{ $cConfig['default_color_mode'] === 'light' ? 'selected' : '' }}>Workspace Light</option>
                                    <option value="dark" {{ $cConfig['default_color_mode'] === 'dark' ? 'selected' : '' }}>Workspace Dark</option>
                                    <option value="sepia" {{ $cConfig['default_color_mode'] === 'sepia' ? 'selected' : '' }}>Workspace Sepia</option>
                                </select>
                            </div>

                            <hr class="my-4">
                            <h5 class="fw-bold mb-3"><i class="fa-solid fa-sliders me-1 text-primary"></i>Feature Switches</h5>

                            <div class="col-md-4">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="sticky_header" value="1" {{ $cConfig['sticky_header'] === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="sticky_header">Sticky Navigation</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="parallax_hero" value="1" {{ $cConfig['parallax_hero'] === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="parallax_hero">Parallax Visual Effects</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="wow_animations" value="1" {{ $cConfig['wow_animations'] === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="wow_animations">WOW Scroll Animations</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="reading_progress" value="1" {{ $cConfig['reading_progress'] === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="reading_progress">Reading Progress Bar Indicator</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="table_of_contents" value="1" {{ $cConfig['table_of_contents'] === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="table_of_contents">Dynamic Table of Contents Scroll-Spy</label>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="fw-bold mb-3"><i class="fa-solid fa-table-cells-large me-1 text-primary"></i>Section Post Layouts</h5>
                            <p class="text-muted small mb-3">Define the default display layout for post feeds on each section. These settings are applied site-wide and visitors will see this layout without any toggle controls.</p>

                            <!-- Home Page Layout -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Home Page Feed Layout</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="layout-pick-card border rounded-3 p-3 text-center cursor-pointer {{ $cConfig['home_layout'] === 'grid' ? 'border-primary bg-primary bg-opacity-10' : '' }}" onclick="selectLayoutOption('home_layout', 'grid', this)">
                                            <i class="fa-solid fa-table-cells-large fa-2x mb-2 {{ $cConfig['home_layout'] === 'grid' ? 'text-primary' : 'text-muted' }}"></i>
                                            <div class="fw-semibold small">Grid View</div>
                                            <div class="text-muted" style="font-size:0.72rem;">Multi-column cards</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="layout-pick-card border rounded-3 p-3 text-center cursor-pointer {{ $cConfig['home_layout'] === 'list' ? 'border-primary bg-primary bg-opacity-10' : '' }}" onclick="selectLayoutOption('home_layout', 'list', this)">
                                            <i class="fa-solid fa-list fa-2x mb-2 {{ $cConfig['home_layout'] === 'list' ? 'text-primary' : 'text-muted' }}"></i>
                                            <div class="fw-semibold small">List View</div>
                                            <div class="text-muted" style="font-size:0.72rem;">Full-width rows</div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="home_layout" value="{{ $cConfig['home_layout'] }}">
                            </div>

                            <!-- Archive / Category Layout -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Archive / Category Feed Layout</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="layout-pick-card border rounded-3 p-3 text-center cursor-pointer {{ $cConfig['archive_layout'] === 'grid' ? 'border-primary bg-primary bg-opacity-10' : '' }}" onclick="selectLayoutOption('archive_layout', 'grid', this)">
                                            <i class="fa-solid fa-table-cells-large fa-2x mb-2 {{ $cConfig['archive_layout'] === 'grid' ? 'text-primary' : 'text-muted' }}"></i>
                                            <div class="fw-semibold small">Grid View</div>
                                            <div class="text-muted" style="font-size:0.72rem;">Multi-column cards</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="layout-pick-card border rounded-3 p-3 text-center cursor-pointer {{ $cConfig['archive_layout'] === 'list' ? 'border-primary bg-primary bg-opacity-10' : '' }}" onclick="selectLayoutOption('archive_layout', 'list', this)">
                                            <i class="fa-solid fa-list fa-2x mb-2 {{ $cConfig['archive_layout'] === 'list' ? 'text-primary' : 'text-muted' }}"></i>
                                            <div class="fw-semibold small">List View</div>
                                            <div class="text-muted" style="font-size:0.72rem;">Full-width rows</div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="archive_layout" value="{{ $cConfig['archive_layout'] }}">
                            </div>

                        </div>
                    </div>

                    <!-- Tab Pane 2: Header & Footer Color Profiles -->
                    <div class="tab-pane fade" id="hf-pane" role="tabpanel">
                        <div class="row g-3">

                            <!-- Header Colors -->
                            <div class="col-12">
                                <div class="alert alert-light border d-flex align-items-center gap-2 mb-2 py-2" role="alert">
                                    <i class="fa-solid fa-heading text-danger fa-lg"></i>
                                    <div><strong>Header Color Profile</strong> — Controls background, text, and border colors of the top navigation header area.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="header_bg" class="form-label fw-bold">Header Background</label>
                                <input type="color" class="form-control form-control-color w-100" id="header_bg" value="{{ $cConfig['header_bg'] }}">
                                <div class="form-text">Background of the header/nav bar.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="header_text_color" class="form-label fw-bold">Header Text &amp; Nav Links</label>
                                <input type="color" class="form-control form-control-color w-100" id="header_text_color" value="{{ $cConfig['header_text_color'] }}">
                                <div class="form-text">Primary text color in the header.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="header_border_color" class="form-label fw-bold">Header Border / Separator</label>
                                <input type="color" class="form-control form-control-color w-100" id="header_border_color" value="{{ $cConfig['header_border_color'] }}">
                                <div class="form-text">Bottom border line of the header.</div>
                            </div>

                            <!-- Preview swatch -->
                            <div class="col-12">
                                <div class="rounded-3 p-3 d-flex align-items-center justify-content-between" id="headerPreview" style="background: {{ $cConfig['header_bg'] }}; border: 2px solid {{ $cConfig['header_border_color'] }}; border-bottom-width: 3px; transition: all 0.3s;">
                                    <span class="fw-bold" style="color: {{ $cConfig['header_text_color'] }}; font-size: 1.1rem;">&#x3C;Codies/&#x3E;</span>
                                    <div class="d-flex gap-3">
                                        <span style="color: {{ $cConfig['header_text_color'] }}; font-size: 0.85rem; opacity: 0.85;">Home</span>
                                        <span style="color: {{ $cConfig['header_text_color'] }}; font-size: 0.85rem; opacity: 0.85;">Blog</span>
                                        <span style="color: {{ $cConfig['header_text_color'] }}; font-size: 0.85rem; opacity: 0.85;">About</span>
                                    </div>
                                    <span class="badge" style="background: {{ $cConfig['primary_color'] }}; color: #fff; font-size: 0.7rem;">&#9788; Light</span>
                                </div>
                                <div class="form-text"><i class="fa-solid fa-eye me-1"></i>Live header preview — updates as you pick colors above.</div>
                            </div>

                            <hr class="my-4">

                            <!-- Footer Colors -->
                            <div class="col-12">
                                <div class="alert alert-light border d-flex align-items-center gap-2 mb-2 py-2" role="alert">
                                    <i class="fa-solid fa-shoe-prints text-primary fa-lg"></i>
                                    <div><strong>Footer Color Profile</strong> — Controls background and all text/link colors inside the footer section.</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="footer_bg" class="form-label fw-bold">Footer Background</label>
                                <input type="color" class="form-control form-control-color w-100" id="footer_bg" value="{{ $cConfig['footer_bg'] }}">
                            </div>
                            <div class="col-md-3">
                                <label for="footer_text_color" class="form-label fw-bold">Body Text &amp; Muted</label>
                                <input type="color" class="form-control form-control-color w-100" id="footer_text_color" value="{{ $cConfig['footer_text_color'] }}">
                            </div>
                            <div class="col-md-3">
                                <label for="footer_heading_color" class="form-label fw-bold">Section Headings</label>
                                <input type="color" class="form-control form-control-color w-100" id="footer_heading_color" value="{{ $cConfig['footer_heading_color'] }}">
                            </div>
                            <div class="col-md-3">
                                <label for="footer_link_color" class="form-label fw-bold">Link / Icon Color</label>
                                <input type="color" class="form-control form-control-color w-100" id="footer_link_color" value="{{ $cConfig['footer_link_color'] }}">
                            </div>

                            <!-- Footer preview swatch -->
                            <div class="col-12">
                                <div class="rounded-3 p-4" id="footerPreview" style="background: {{ $cConfig['footer_bg'] }}; border-top: 3px solid rgba(255,255,255,0.08); transition: all 0.3s;">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                                        <div>
                                            <div class="fw-bold mb-1" style="color: {{ $cConfig['footer_heading_color'] }}; font-size: 1rem;">&#x3C;Codies/&#x3E;</div>
                                            <div style="color: {{ $cConfig['footer_text_color'] }}; font-size: 0.78rem;">Elegant modern technical catalog for developers.</div>
                                        </div>
                                        <div>
                                            <div class="fw-bold mb-1" style="color: {{ $cConfig['footer_heading_color'] }}; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px;">Core Menu</div>
                                            <div class="d-flex flex-column gap-1">
                                                <a href="#" style="color: {{ $cConfig['footer_link_color'] }}; font-size: 0.78rem; text-decoration: none;">Home</a>
                                                <a href="#" style="color: {{ $cConfig['footer_link_color'] }}; font-size: 0.78rem; text-decoration: none;">Blog</a>
                                            </div>
                                        </div>
                                        <div style="color: {{ $cConfig['footer_text_color'] }}; font-size: 0.75rem; align-self: flex-end;">&copy; {{ date('Y') }} ICT Matrics Private Limited.</div>
                                    </div>
                                </div>
                                <div class="form-text"><i class="fa-solid fa-eye me-1"></i>Live footer preview — updates as you pick colors above.</div>
                            </div>

                            <hr class="my-4">
                            <h5 class="fw-bold mb-3"><i class="fa-solid fa-pen-to-square me-1 text-primary"></i>Footer Texts &amp; Social Links</h5>

                            <div class="col-md-6">
                                <label for="footer_about_text" class="form-label fw-bold">Footer About / Bio Text</label>
                                <textarea class="form-control form-control-sm" name="footer_about_text" id="footer_about_text" rows="2">{{ htmlspecialchars($cConfig['footer_about_text'], ENT_QUOTES, 'UTF-8') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="footer_copyright" class="form-label fw-bold">Copyright Statement</label>
                                <input type="text" class="form-control form-control-sm" name="footer_copyright" id="footer_copyright" value="{{ htmlspecialchars($cConfig['footer_copyright'], ENT_QUOTES, 'UTF-8') }}">
                                <div class="form-text">Year will automatically prepend: &copy; {{ date('Y') }}</div>
                            </div>

                            <div class="col-md-6">
                                <label for="footer_community_title" class="form-label fw-bold">Community Column Title</label>
                                <input type="text" class="form-control form-control-sm" name="footer_community_title" id="footer_community_title" value="{{ htmlspecialchars($cConfig['footer_community_title'], ENT_QUOTES, 'UTF-8') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="footer_community_desc" class="form-label fw-bold">Community Description</label>
                                <input type="text" class="form-control form-control-sm" name="footer_community_desc" id="footer_community_desc" value="{{ htmlspecialchars($cConfig['footer_community_desc'], ENT_QUOTES, 'UTF-8') }}">
                            </div>

                            <div class="col-md-4">
                                <label for="footer_github" class="form-label fw-bold"><i class="fa-brands fa-github me-1"></i> GitHub Link</label>
                                <input type="text" class="form-control form-control-sm" name="footer_github" id="footer_github" value="{{ htmlspecialchars($cConfig['footer_github'], ENT_QUOTES, 'UTF-8') }}" placeholder="https://github.com/your-repo">
                            </div>
                            <div class="col-md-4">
                                <label for="footer_discord" class="form-label fw-bold"><i class="fa-brands fa-discord me-1"></i> Discord Link</label>
                                <input type="text" class="form-control form-control-sm" name="footer_discord" id="footer_discord" value="{{ htmlspecialchars($cConfig['footer_discord'], ENT_QUOTES, 'UTF-8') }}" placeholder="https://discord.gg/your-invite">
                            </div>
                            <div class="col-md-4">
                                <label for="footer_twitter" class="form-label fw-bold"><i class="fa-brands fa-twitter me-1"></i> Twitter / X Link</label>
                                <input type="text" class="form-control form-control-sm" name="footer_twitter" id="footer_twitter" value="{{ htmlspecialchars($cConfig['footer_twitter'], ENT_QUOTES, 'UTF-8') }}" placeholder="https://twitter.com/your-handle">
                            </div>
                            <div class="col-md-4">
                                <label for="footer_facebook" class="form-label fw-bold"><i class="fa-brands fa-facebook me-1"></i> Facebook Link</label>
                                <input type="text" class="form-control form-control-sm" name="footer_facebook" id="footer_facebook" value="{{ htmlspecialchars($cConfig['footer_facebook'], ENT_QUOTES, 'UTF-8') }}" placeholder="https://facebook.com/your-page">
                            </div>
                            <div class="col-md-4">
                                <label for="footer_youtube" class="form-label fw-bold"><i class="fa-brands fa-youtube me-1"></i> YouTube Link</label>
                                <input type="text" class="form-control form-control-sm" name="footer_youtube" id="footer_youtube" value="{{ htmlspecialchars($cConfig['footer_youtube'], ENT_QUOTES, 'UTF-8') }}" placeholder="https://youtube.com/@your-channel">
                            </div>
                            <div class="col-md-4">
                                <label for="footer_tiktok" class="form-label fw-bold"><i class="fa-brands fa-tiktok me-1"></i> TikTok Link</label>
                                <input type="text" class="form-control form-control-sm" name="footer_tiktok" id="footer_tiktok" value="{{ htmlspecialchars($cConfig['footer_tiktok'], ENT_QUOTES, 'UTF-8') }}" placeholder="https://tiktok.com/@your-handle">
                            </div>

                        </div>
                    </div>

                    <!-- Tab Pane 3: Typography -->
                    <div class="tab-pane fade" id="typo-pane" role="tabpanel">
                        <div class="row g-3">
                            <!-- Font Family -->
                            <div class="col-12">
                                <div class="alert alert-light border d-flex align-items-center gap-2 mb-1 py-2">
                                    <i class="fa-solid fa-font text-success fa-lg"></i>
                                    <div><strong>Font Families</strong> — Google Fonts for headings and body text.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="heading_font" class="form-label fw-bold">Heading Typographic Profile</label>
                                <select class="form-select" id="heading_font">
                                    <?php foreach ($googleFontsList as $nameFont => $labelFont) { ?>
                                        <option value="{{ $nameFont }}" {{ $cConfig['heading_font'] === $nameFont ? 'selected' : '' }}>{{ $labelFont }}</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="body_font" class="form-label fw-bold">Paragraph Typographic Profile</label>
                                <select class="form-select" id="body_font">
                                    <?php foreach ($googleFontsList as $nameFont => $labelFont) { ?>
                                        <option value="{{ $nameFont }}" {{ $cConfig['body_font'] === $nameFont ? 'selected' : '' }}>{{ $labelFont }}</option>
                                    <?php } ?>
                                </select>
                            </div>

                            <hr class="my-3">

                            <!-- Navigation / Menu Typography -->
                            <div class="col-12">
                                <div class="alert alert-light border d-flex align-items-center gap-2 mb-1 py-2">
                                    <i class="fa-solid fa-bars text-primary fa-lg"></i>
                                    <div><strong>Navigation Menu Typography</strong> — Font size and weight for main navigation links.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="menu_font_size" class="form-label fw-bold">Menu Font Size <small class="text-muted fw-normal">(px)</small></label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="range" class="form-range flex-grow-1" id="menu_font_size" min="12" max="22" step="1" value="{{ $cConfig['menu_font_size'] }}" oninput="document.getElementById('menu_font_size_val').textContent = this.value + 'px'">
                                    <span class="badge bg-secondary" id="menu_font_size_val" style="min-width: 42px;">{{ $cConfig['menu_font_size'] }}px</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="menu_font_weight" class="form-label fw-bold">Menu Font Weight</label>
                                <select class="form-select" id="menu_font_weight">
                                    <option value="400" {{ $cConfig['menu_font_weight'] === '400' ? 'selected' : '' }}>400 — Regular</option>
                                    <option value="500" {{ $cConfig['menu_font_weight'] === '500' ? 'selected' : '' }}>500 — Medium</option>
                                    <option value="600" {{ $cConfig['menu_font_weight'] === '600' ? 'selected' : '' }}>600 — SemiBold</option>
                                    <option value="700" {{ $cConfig['menu_font_weight'] === '700' ? 'selected' : '' }}>700 — Bold</option>
                                    <option value="800" {{ $cConfig['menu_font_weight'] === '800' ? 'selected' : '' }}>800 — ExtraBold</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="footer_font_size" class="form-label fw-bold">Footer Text Size <small class="text-muted fw-normal">(px)</small></label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="range" class="form-range flex-grow-1" id="footer_font_size" min="11" max="18" step="1" value="{{ $cConfig['footer_font_size'] }}" oninput="document.getElementById('footer_font_size_val').textContent = this.value + 'px'">
                                    <span class="badge bg-secondary" id="footer_font_size_val" style="min-width: 42px;">{{ $cConfig['footer_font_size'] }}px</span>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Heading Scale -->
                            <div class="col-12">
                                <div class="alert alert-light border d-flex align-items-center gap-2 mb-1 py-2">
                                    <i class="fa-solid fa-heading text-warning fa-lg"></i>
                                    <div><strong>Heading Type Scale</strong> — Control the rem size for each heading level (H1–H6) site-wide.</div>
                                </div>
                            </div>
                            <?php foreach (['h1' => ['2.5', '1.5', '4.0'], 'h2' => ['2.0', '1.2', '3.5'], 'h3' => ['1.6', '1.0', '3.0'], 'h4' => ['1.3', '0.9', '2.5'], 'h5' => ['1.1', '0.8', '2.0'], 'h6' => ['1.0', '0.75', '1.8']] as $tag => [$default, $min, $max]) { ?>
                                <div class="col-md-4">
                                    <label for="{{ $tag }}_size" class="form-label fw-bold">{{ strtoupper($tag) }} Size <small class="text-muted fw-normal">(rem)</small></label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="range" class="form-range flex-grow-1" id="{{ $tag }}_size" min="{{ $min }}" max="{{ $max }}" step="0.05" value="{{ $cConfig[$tag . '_size'] }}" oninput="document.getElementById('{{ $tag }}_size_val').textContent = parseFloat(this.value).toFixed(2) + 'rem'; document.querySelector('.typo-preview-{{ $tag }}').style.fontSize = this.value + 'rem';">
                                        <span class="badge bg-secondary" id="{{ $tag }}_size_val" style="min-width: 52px;">{{ number_format((float)$cConfig[$tag . '_size'], 2) }}rem</span>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Live Heading Preview -->
                            <div class="col-12 mt-2">
                                <div class="p-4 border rounded-3 bg-light">
                                    <div class="fw-bold text-muted small mb-3"><i class="fa-solid fa-eye me-1"></i>Live Heading Scale Preview</div>
                                    <?php foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $tag) { ?>
                                        <{{ $tag }} class="typo-preview-{{ $tag }} mb-1" style="font-size: {{ $cConfig[$tag . '_size'] }}rem; font-family: '{{ $cConfig['heading_font'] }}', sans-serif; font-weight: 700; line-height: 1.2;">{{ strtoupper($tag) }} — The Quick Brown Fox</{{ $tag }}>
                                    <?php } ?>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Tab Pane 3: Carousel Slides -->
                    <div class="tab-pane fade" id="carousel-pane" role="tabpanel">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="carousel_mode" class="form-label fw-bold">Carousel Loading Strategy</label>
                                <select class="form-select" id="carousel_mode" onchange="toggleCarouselStrategy(this.value)">
                                    <option value="recent" {{ $cConfig['carousel_mode'] === 'recent' ? 'selected' : '' }}>Load Recent Published</option>
                                    <option value="category" {{ $cConfig['carousel_mode'] === 'category' ? 'selected' : '' }}>Load from Category</option>
                                    <option value="custom" {{ $cConfig['carousel_mode'] === 'custom' ? 'selected' : '' }}>Load Custom Slides Below</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="carouselCategoryContainer" style="{{ $cConfig['carousel_mode'] === 'category' ? '' : 'display:none;' }}">
                                <label for="carousel_category" class="form-label fw-bold">Source Category</label>
                                <select class="form-select" id="carousel_category">
                                    <option value="0">Select Category...</option>
                                    <?php foreach ($allCategories as $cat) { ?>
                                        <option value="{{ $cat['id'] }}" {{ (int)$cConfig['carousel_category'] === (int)$cat['id'] ? 'selected' : '' }}>{{ htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') }}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <!-- Custom Carousel configuration sliders -->
                        <div id="customSlidesFields" style="{{ $cConfig['carousel_mode'] === 'custom' ? '' : 'display:none;' }}">
                            <?php for ($s = 1; $s <= 3; $s++) { ?>
                                <div class="card p-3 mb-3 border">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-image me-1"></i>Custom Carousel Banner: Slide {{ $s }}</h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Slide Background Media Image</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm" id="slide{{ $s }}_img" value="{{ htmlspecialchars($cConfig['slide' . $s . '_img'], ENT_QUOTES, 'UTF-8') }}">
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-select-media" data-target="slide{{ $s }}_img">Select Media</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <label for="slide{{ $s }}_title" class="form-label fw-bold">Slide Banner Title</label>
                                            <input type="text" class="form-control form-control-sm" id="slide{{ $s }}_title" value="{{ htmlspecialchars($cConfig['slide' . $s . '_title'], ENT_QUOTES, 'UTF-8') }}">
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <label for="slide{{ $s }}_badge" class="form-label fw-bold">Category Badge Pill</label>
                                            <input type="text" class="form-control form-control-sm" id="slide{{ $s }}_badge" value="{{ htmlspecialchars($cConfig['slide' . $s . '_badge'], ENT_QUOTES, 'UTF-8') }}">
                                        </div>
                                        <div class="col-12">
                                            <label for="slide{{ $s }}_lead" class="form-label fw-bold">Lead Intro description text</label>
                                            <textarea class="form-control form-control-sm" id="slide{{ $s }}_lead" rows="2">{{ htmlspecialchars($cConfig['slide' . $s . '_lead'], ENT_QUOTES, 'UTF-8') }}</textarea>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <label for="slide{{ $s }}_link" class="form-label fw-bold">Click Destination URL Link</label>
                                            <input type="text" class="form-control form-control-sm" id="slide{{ $s }}_link" value="{{ htmlspecialchars($cConfig['slide' . $s . '_link'], ENT_QUOTES, 'UTF-8') }}">
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <label for="slide{{ $s }}_readtime" class="form-label fw-bold">Read Duration (e.g. 5 min read)</label>
                                            <input type="text" class="form-control form-control-sm" id="slide{{ $s }}_readtime" value="{{ htmlspecialchars($cConfig['slide' . $s . '_readtime'], ENT_QUOTES, 'UTF-8') }}">
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Tab Pane 4: Banner Ads -->
                    <div class="tab-pane fade" id="ads-pane" role="tabpanel">
                        <div class="alert alert-light border d-flex gap-2 align-items-start mb-4 py-3">
                            <i class="fa-solid fa-circle-info text-primary mt-1"></i>
                            <div class="small"><strong>Multi-Banner Rotation:</strong> Enter multiple image paths separated by a pipe <code>|</code> character in the Image URLs field. The frontend will randomly pick one on each page load. Example: <code>uploads/ad1.jpg|uploads/ad2.jpg|uploads/ad3.jpg</code></div>
                        </div>

                        <?php
                        $ad_slots = [
                            'leaderboard' => ['Top Header Leaderboard',        '728×90',  'fa-banner', 'text-primary'],
                            'sidebar'     => ['Right Sidebar Square Box',       '300×250', 'fa-sidebar', 'text-success'],
                            'infeed'      => ['In-Feed Post Injection Ad',      '728×90',  'fa-newspaper', 'text-warning'],
                            'footer'      => ['Bottom Footer Billboard Banner', '970×250', 'fa-rectangle-ad', 'text-danger'],
                        ];
                        foreach ($ad_slots as $slot => [$titleSlot, $dimSlot, $iconSlot, $colorSlot]) {
                            $imgs_val = $cConfig['ad_' . $slot . '_imgs'] ?? $cConfig['ad_' . $slot . '_img'] ?? '';
                            $imgs_arr = array_values(array_filter(array_map('trim', explode('|', $imgs_val))));
                        ?>
                            <div class="card border mb-4" id="ad_card_{{ $slot }}">
                                <div class="card-header d-flex align-items-center justify-content-between py-2 bg-transparent border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid {{ $iconSlot }} {{ $colorSlot }}"></i>
                                        <span class="fw-bold">{{ $titleSlot }}</span>
                                        <span class="badge bg-secondary" style="font-size:0.68rem;">{{ $dimSlot }}</span>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="ad_{{ $slot }}_enable" value="1" {{ $cConfig['ad_' . $slot . '_enable'] === '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold small" for="ad_{{ $slot }}_enable">Enabled</label>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">

                                        <?php if ($slot === 'infeed') { ?>
                                            <!-- In-feed injection position -->
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Inject After Post #</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="range" class="form-range flex-grow-1" id="infeed_after_count" min="1" max="10" step="1" value="{{ $cConfig['infeed_after_count'] }}" oninput="document.getElementById('infeed_after_count_val').textContent = this.value">
                                                    <span class="badge bg-secondary" id="infeed_after_count_val" style="min-width:32px;">{{ $cConfig['infeed_after_count'] }}</span>
                                                </div>
                                                <div class="form-text">Ad banner appears after this many post cards.</div>
                                            </div>
                                        <?php } ?>

                                        <!-- Multi-image URLs -->
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Image Banner URLs <small class="text-muted fw-normal">(pipe-separated for rotation)</small></label>
                                            <input type="text" class="form-control form-control-sm font-monospace" id="ad_{{ $slot }}_imgs" value="{{ htmlspecialchars($imgs_val, ENT_QUOTES, 'UTF-8') }}" oninput="refreshAdPreviews('{{ $slot }}', this.value)" placeholder="uploads/ad1.jpg|uploads/ad2.jpg">
                                            <div class="form-text">Use a pipe <code>|</code> to add multiple banners — one will be selected randomly on each page load.</div>
                                        </div>

                                        <!-- Image preview thumbnails -->
                                        <div class="col-12">
                                            <div class="d-flex flex-wrap gap-2 align-items-start" id="ad_{{ $slot }}_preview_strip">
                                                <?php if (!empty($imgs_arr)) {
                                                    foreach ($imgs_arr as $idx => $img_path) { ?>
                                                        <div class="ad-thumb-wrap position-relative border rounded overflow-hidden" style="width:120px; height:70px; background:#f0f0f0;">
                                                            <img src="{{ pathto($img_path) }}" alt="Banner {{ $idx + 1 }}" style="width:100%; height:100%; object-fit:cover;" onerror="this.closest('.ad-thumb-wrap').style.background='#fee2e2'; this.remove();">
                                                            <span class="position-absolute bottom-0 start-0 w-100 text-center" style="background:rgba(0,0,0,0.55); color:#fff; font-size:0.6rem; padding:1px 0;">{{ $idx + 1 }}</span>
                                                        </div>
                                                <?php }
                                                } ?>
                                                <?php if (empty($imgs_arr)) { ?>
                                                    <div class="text-muted small fst-italic"><i class="fa-regular fa-image me-1"></i>No images configured — enter paths above.</div>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Destination URL</label>
                                            <input type="text" class="form-control form-control-sm" name="ad_{{ $slot }}_link" value="{{ htmlspecialchars($cConfig['ad_' . $slot . '_link'], ENT_QUOTES, 'UTF-8') }}" placeholder="https://advertiser.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Image Alt Text</label>
                                            <input type="text" class="form-control form-control-sm" name="ad_{{ $slot }}_alt" value="{{ htmlspecialchars($cConfig['ad_' . $slot . '_alt'], ENT_QUOTES, 'UTF-8') }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">OR: Embed HTML / Script <small class="text-muted fw-normal">(overrides image fields above)</small></label>
                                            <textarea class="form-control form-control-sm font-monospace" name="ad_{{ $slot }}_html" rows="2">{{ htmlspecialchars($cConfig['ad_' . $slot . '_html'], ENT_QUOTES, 'UTF-8') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Tab Pane 5: Featured Post -->
                    <div class="tab-pane fade" id="featured-pane" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="featured_post_id" class="form-label fw-bold">Select Featured Tutorial</label>
                                <select class="form-select" id="featured_post_id">
                                    <option value="0">Select Featured Blueprint...</option>
                                    <?php foreach ($allPosts as $p) { ?>
                                        <option value="{{ $p['id'] }}" {{ (int)$cConfig['featured_post_id'] === (int)$p['id'] ? 'selected' : '' }}>
                                            [ID: {{ $p['id'] }}] {{ htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8') }}
                                        </option>
                                    <?php } ?>
                                </select>
                                <div class="form-text">This tutorial will be featured in a gorgeous highlight banner on the homepage.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Pane 6: Advanced -->
                    <div class="tab-pane fade" id="advanced-pane" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="codies_raw_custom_css" class="form-label fw-bold">Custom CSS Layout Overrides</label>
                                <textarea class="form-control font-monospace" name="codies_raw_custom_css" id="codies_raw_custom_css" rows="4">{{ htmlspecialchars($clean_css, ENT_QUOTES, 'UTF-8') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="header_scripts" class="form-label fw-bold">Header Scripts (Injects script tags in head block)</label>
                                <textarea class="form-control font-monospace" name="header_scripts" id="header_scripts" rows="3">{{ htmlspecialchars($config['header_scripts'] ?? '', ENT_QUOTES, 'UTF-8') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="footer_scripts" class="form-label fw-bold">Footer Scripts (Injects custom elements just before body close)</label>
                                <textarea class="form-control font-monospace" name="footer_scripts" id="footer_scripts" rows="3">{{ htmlspecialchars($config['footer_scripts'] ?? '', ENT_QUOTES, 'UTF-8') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 border-top pt-3 text-end">
                    <button type="submit" class="btn btn-primary px-5 py-2.5 fw-semibold shadow-sm rounded-pill"><i class="fa-solid fa-circle-check me-2"></i>Save Codies Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    function toggleCarouselStrategy(mode) {
        if (mode === 'category') {
            $('#carouselCategoryContainer').slideDown(200);
            $('#customSlidesFields').slideUp(200);
        } else if (mode === 'custom') {
            $('#carouselCategoryContainer').slideUp(200);
            $('#customSlidesFields').slideDown(200);
        } else {
            $('#carouselCategoryContainer').slideUp(200);
            $('#customSlidesFields').slideUp(200);
        }
    }

    // Layout picker card selector
    function selectLayoutOption(fieldId, value, clickedCard) {
        // Update hidden input
        document.getElementById(fieldId).value = value;

        // Reset sibling cards in the same row
        const row = clickedCard.closest('.row');
        row.querySelectorAll('.layout-pick-card').forEach(function(card) {
            card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
            card.querySelector('i').classList.remove('text-primary');
            card.querySelector('i').classList.add('text-muted');
        });

        // Activate clicked card
        clickedCard.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        clickedCard.querySelector('i').classList.remove('text-muted');
        clickedCard.querySelector('i').classList.add('text-primary');
    }

    // Capture Codies form submit events and serialize configs
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function() {
            $('#codiesCustomizerForm').on('submit', function(e) {
                // Push values of general layouts to the core system fields
                $('#primary_color_hidden').val($('#primary_color').val());
                $('#secondary_color_hidden').val($('#secondary_color').val());
                $('#sticky_header_hidden').val($('#sticky_header').is(':checked') ? '1' : '0');

                const configObject = {
                    header_layout: $('#header_layout').val(),
                    footer_layout: $('#footer_layout').val(),
                    primary_color: $('#primary_color').val(),
                    secondary_color: $('#secondary_color').val(),
                    bg_color: $('#bg_color').val(),
                    card_bg_color: $('#card_bg_color').val(),
                    border_color: $('#border_color').val(),
                    default_color_mode: $('#default_color_mode').val(),
                    sticky_header: $('#sticky_header').is(':checked') ? '1' : '0',
                    parallax_hero: $('#parallax_hero').is(':checked') ? '1' : '0',
                    wow_animations: $('#wow_animations').is(':checked') ? '1' : '0',
                    reading_progress: $('#reading_progress').is(':checked') ? '1' : '0',
                    table_of_contents: $('#table_of_contents').is(':checked') ? '1' : '0',
                    heading_font: $('#heading_font').val(),
                    body_font: $('#body_font').val(),
                    menu_font_size: $('#menu_font_size').val(),
                    menu_font_weight: $('#menu_font_weight').val(),
                    footer_font_size: $('#footer_font_size').val(),
                    h1_size: $('#h1_size').val(),
                    h2_size: $('#h2_size').val(),
                    h3_size: $('#h3_size').val(),
                    h4_size: $('#h4_size').val(),
                    h5_size: $('#h5_size').val(),
                    h6_size: $('#h6_size').val(),
                    // Header colors
                    header_bg: $('#header_bg').val(),
                    header_text_color: $('#header_text_color').val(),
                    header_border_color: $('#header_border_color').val(),
                    // Footer colors
                    footer_bg: $('#footer_bg').val(),
                    footer_text_color: $('#footer_text_color').val(),
                    footer_heading_color: $('#footer_heading_color').val(),
                    footer_link_color: $('#footer_link_color').val(),
                    carousel_mode: $('#carousel_mode').val(),
                    carousel_category: $('#carousel_category').val(),
                    home_layout: $('#home_layout').val(),
                    archive_layout: $('#archive_layout').val(),
                    featured_post_id: $('#featured_post_id').val(),

                    // Slides
                    slide1_img: $('#slide1_img').val(),
                    slide1_title: $('#slide1_title').val(),
                    slide1_badge: $('#slide1_badge').val(),
                    slide1_lead: $('#slide1_lead').val(),
                    slide1_link: $('#slide1_link').val(),
                    slide1_readtime: $('#slide1_readtime').val(),

                    slide2_img: $('#slide2_img').val(),
                    slide2_title: $('#slide2_title').val(),
                    slide2_badge: $('#slide2_badge').val(),
                    slide2_lead: $('#slide2_lead').val(),
                    slide2_link: $('#slide2_link').val(),
                    slide2_readtime: $('#slide2_readtime').val(),

                    slide3_img: $('#slide3_img').val(),
                    slide3_title: $('#slide3_title').val(),
                    slide3_badge: $('#slide3_badge').val(),
                    slide3_lead: $('#slide3_lead').val(),
                    slide3_link: $('#slide3_link').val(),
                    slide3_readtime: $('#slide3_readtime').val(),

                    // Ads
                    ad_leaderboard_enable: $('#ad_leaderboard_enable').is(':checked') ? '1' : '0',
                    ad_leaderboard_imgs: $('#ad_leaderboard_imgs').val(),
                    ad_leaderboard_link: $('[name="ad_leaderboard_link"]').val(),
                    ad_leaderboard_alt: $('[name="ad_leaderboard_alt"]').val(),
                    ad_leaderboard_html: $('[name="ad_leaderboard_html"]').val(),

                    ad_sidebar_enable: $('#ad_sidebar_enable').is(':checked') ? '1' : '0',
                    ad_sidebar_imgs: $('#ad_sidebar_imgs').val(),
                    ad_sidebar_link: $('[name="ad_sidebar_link"]').val(),
                    ad_sidebar_alt: $('[name="ad_sidebar_alt"]').val(),
                    ad_sidebar_html: $('[name="ad_sidebar_html"]').val(),

                    ad_infeed_enable: $('#ad_infeed_enable').is(':checked') ? '1' : '0',
                    ad_infeed_imgs: $('#ad_infeed_imgs').val(),
                    ad_infeed_link: $('[name="ad_infeed_link"]').val(),
                    ad_infeed_alt: $('[name="ad_infeed_alt"]').val(),
                    ad_infeed_html: $('[name="ad_infeed_html"]').val(),
                    infeed_after_count: $('#infeed_after_count').val(),

                    ad_footer_enable: $('#ad_footer_enable').is(':checked') ? '1' : '0',
                    ad_footer_imgs: $('#ad_footer_imgs').val(),
                    ad_footer_link: $('[name="ad_footer_link"]').val(),
                    ad_footer_alt: $('[name="ad_footer_alt"]').val(),
                    ad_footer_html: $('[name="ad_footer_html"]').val(),

                    // Footer custom texts & links
                    footer_about_text: $('#footer_about_text').val(),
                    footer_copyright: $('#footer_copyright').val(),
                    footer_community_title: $('#footer_community_title').val(),
                    footer_community_desc: $('#footer_community_desc').val(),
                    footer_github: $('#footer_github').val(),
                    footer_discord: $('#footer_discord').val(),
                    footer_twitter: $('#footer_twitter').val(),
                    footer_facebook: $('#footer_facebook').val(),
                    footer_youtube: $('#footer_youtube').val(),
                    footer_tiktok: $('#footer_tiktok').val(),
                };

                const jsonComment = '/*CODIES_CONFIG:' + JSON.stringify(configObject) + ':END_CODIES_CONFIG*/';
                const userCss = $('#codies_raw_custom_css').val();

                // Set custom_css hidden input with both user raw CSS and serialized JSON comment blob
                $('#custom_css').val(userCss + "\n" + jsonComment);
            });
        });
    });
    // Live header preview updater
    document.addEventListener('DOMContentLoaded', function() {
        ['header_bg', 'header_text_color', 'header_border_color'].forEach(function(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('input', function() {
                const preview = document.getElementById('headerPreview');
                if (!preview) return;
                const bg = document.getElementById('header_bg').value;
                const text = document.getElementById('header_text_color').value;
                const border = document.getElementById('header_border_color').value;
                preview.style.background = bg;
                preview.style.borderColor = border;
                preview.querySelectorAll('span, div').forEach(function(s) {
                    if (s.style.color !== undefined && s !== preview) s.style.color = text;
                });
            });
        });
        ['footer_bg', 'footer_text_color', 'footer_heading_color', 'footer_link_color'].forEach(function(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('input', function() {
                const preview = document.getElementById('footerPreview');
                if (!preview) return;
                preview.style.background = document.getElementById('footer_bg').value;
            });
        });
    });
    // Live ad preview image strip refresh
    function refreshAdPreviews(slot, raw) {
        const strip = document.getElementById('ad_' + slot + '_preview_strip');
        if (!strip) return;
        const paths = raw.split('|').map(s => s.trim()).filter(s => s.length > 0);
        if (paths.length === 0) {
            strip.innerHTML = '<div class="text-muted small fst-italic"><i class="fa-regular fa-image me-1"></i>No images configured — enter paths above.</div>';
            return;
        }
        strip.innerHTML = paths.map((p, i) => `
            <div class="ad-thumb-wrap position-relative border rounded overflow-hidden" style="width:120px; height:70px; background:#f0f0f0;">
                <img src="/${p}" alt="Banner ${i+1}" style="width:100%; height:100%; object-fit:cover;" onerror="this.closest('.ad-thumb-wrap').style.background='#fee2e2'; this.remove();">
                <span class="position-absolute bottom-0 start-0 w-100 text-center" style="background:rgba(0,0,0,0.55); color:#fff; font-size:0.6rem; padding:1px 0;">${i+1}</span>
            </div>`).join('');
    }
</script>

{{ $this->view('admin/layout/footer') }}