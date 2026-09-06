<?php
if ($theme_name === 'codies') {
    echo $this->view('Themes/codies/customizer', [
        'theme_name' => $theme_name,
        'config' => $config,
        'posts_list' => $posts_list ?? [],
        'categories_list' => $categories_list ?? []
    ]);
    return;
}
?>
{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ $title }}</h1>
            <p class="text-muted mb-0 small fw-medium">Modify the settings for this specific theme.</p>
        </div>
        <div>
            <a href="{{ pathto('admin/themes') }}" class="btn btn-secondary px-4 py-2 fw-semibold shadow-sm">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Themes
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="row">
        <div class="col-12">
            {{ flash('success_msg') }}
            {{ flash('error_msg') }}
        </div>
    </div>

    <!-- Customization Form -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <form action="{{ pathto('admin/theme/customize/' . $theme_name) }}" method="POST">
                        
                        <h5 class="mb-3 border-bottom pb-2 text-primary fw-bold">General Settings</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="primary_color" class="form-label fw-semibold">Primary Color</label>
                                <input type="color" class="form-control form-control-color w-100" id="primary_color" name="primary_color" value="{{ htmlspecialchars($config['primary_color'] ?? '#0d6efd', ENT_QUOTES, 'UTF-8') }}">
                                <div class="form-text">Choose the main accent color for this theme.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="secondary_color" class="form-label fw-semibold">Secondary Color</label>
                                <input type="color" class="form-control form-control-color w-100" id="secondary_color" name="secondary_color" value="{{ htmlspecialchars($config['secondary_color'] ?? '#6c757d', ENT_QUOTES, 'UTF-8') }}">
                                <div class="form-text">Choose a secondary/highlight color.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="sticky_header" name="sticky_header" value="1" {{ !empty($config['sticky_header']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="sticky_header">Enable Sticky Header</label>
                            </div>
                            <div class="form-text">Keep the header fixed at the top when scrolling.</div>
                        </div>


                        <h5 class="mb-3 border-bottom pb-2 text-primary fw-bold mt-5">Advanced Settings</h5>

                        <div class="mb-4">
                            <label for="custom_css" class="form-label fw-semibold">Custom CSS (Theme Specific)</label>
                            <textarea class="form-control font-monospace" id="custom_css" name="custom_css" rows="6" placeholder="/* Add custom CSS styles here */">{{ htmlspecialchars($config['custom_css'] ?? '', ENT_QUOTES, 'UTF-8') }}</textarea>
                            <div class="form-text">These styles will only be applied when this theme is active.</div>
                        </div>

                        <h5 class="mb-3 border-bottom pb-2 text-primary fw-bold mt-5">
                            <i class="fa-solid fa-code me-2"></i>Custom Styling &amp; Scripts
                        </h5>

                        <div class="mb-4">
                            <label for="custom_css_code" class="form-label fw-semibold">Custom CSS Code <span class="text-muted fw-normal small">(Included in Page Header)</span></label>
                            <textarea class="form-control font-monospace" id="custom_css_code" name="custom_css_code" rows="5" placeholder="body { background-color: #000; }">{{ htmlspecialchars($config['custom_css_code'] ?? '', ENT_QUOTES, 'UTF-8') }}</textarea>
                            <div class="form-text">Custom CSS stylesheets embedded directly in the page <code>&lt;head&gt;</code>.</div>
                        </div>

                        <div class="mb-4">
                            <label for="header_scripts" class="form-label fw-semibold">Header Scripts <span class="text-muted fw-normal small">(Google Analytics, Meta tags, etc.)</span></label>
                            <textarea class="form-control font-monospace" id="header_scripts" name="header_scripts" rows="5" placeholder="&lt;script&gt;...&lt;/script&gt;">{{ htmlspecialchars($config['header_scripts'] ?? '', ENT_QUOTES, 'UTF-8') }}</textarea>
                            <div class="form-text">Injected scripts loaded within the page <code>&lt;head&gt;</code> tags.</div>
                        </div>

                        <div class="mb-4">
                            <label for="footer_scripts" class="form-label fw-semibold">Footer Scripts <span class="text-muted fw-normal small">(Dynamic chat support widgets, etc.)</span></label>
                            <textarea class="form-control font-monospace" id="footer_scripts" name="footer_scripts" rows="5" placeholder="&lt;script&gt;...&lt;/script&gt;">{{ htmlspecialchars($config['footer_scripts'] ?? '', ENT_QUOTES, 'UTF-8') }}</textarea>
                            <div class="form-text">Injected scripts loaded before the closing <code>&lt;/body&gt;</code> tag.</div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                <i class="fa-solid fa-save me-2"></i>Save Customizations
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{ $this->view('admin/layout/footer') }}
