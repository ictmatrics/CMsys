{{ $this->view('admin/layout/header', ['title' => $title]) }}

<?php
$timezones = [
    'UTC' => 'UTC (GMT)',
    'Asia/Kathmandu' => 'Asia/Kathmandu (GMT+05:45)',
    'Asia/Kolkata' => 'Asia/Kolkata (GMT+05:30)',
    'Asia/Singapore' => 'Asia/Singapore (GMT+08:00)',
    'Asia/Tokyo' => 'Asia/Tokyo (GMT+09:00)',
    'Asia/Dubai' => 'Asia/Dubai (GMT+04:00)',
    'Europe/London' => 'Europe/London (GMT+00:00 / BST)',
    'Europe/Paris' => 'Europe/Paris (GMT+01:00 / CEST)',
    'America/New_York' => 'America/New_York (EST/EDT)',
    'America/Chicago' => 'America/Chicago (CST/CDT)',
    'America/Denver' => 'America/Denver (MST/MDT)',
    'America/Los_Angeles' => 'America/Los_Angeles (PST/PDT)',
    'Australia/Sydney' => 'Australia/Sydney (AEST/AEDT)'
];

$date_formats = [
    'Y-m-d' => 'Y-m-d (' . date('Y-m-d') . ')',
    'm/d/Y' => 'm/d/Y (' . date('m/d/Y') . ')',
    'd-m-Y' => 'd-m-Y (' . date('d-m-Y') . ')',
    'F j, Y' => 'F j, Y (' . date('F j, Y') . ')'
];

$time_formats = [
    'g:i a' => 'g:i a (' . date('g:i a') . ')',
    'g:i A' => 'g:i A (' . date('g:i A') . ')',
    'H:i' => 'H:i (' . date('H:i') . ')'
];
?>

<div class="row wow animate__animated animate__fadeInUp">
    <div class="col-12">
        <form id="settingsForm" enctype="multipart/form-data">
            <div class="card card-custom">
                <div class="card-header card-custom-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 text-dark font-weight-bold"><i class="fa-solid fa-sliders me-2 text-primary"></i> System Settings</h5>
                </div>
                
                <div class="card-body card-custom-body p-0">
                    <div class="d-flex align-items-stretch min-vh-50">
                        <!-- Vertical Tab Navigation Sidebar -->
                        <div class="nav flex-column nav-pills border-end p-3 bg-light" id="settingsTabs" role="tablist" style="width: 250px; min-width: 250px;">
                            <button class="nav-link active text-start py-3 px-4 mb-2 rounded-3 border-0 d-flex align-items-center" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                                <i class="fa-solid fa-gear me-3 fs-5"></i> <span>General</span>
                            </button>
                            <button class="nav-link text-start py-3 px-4 mb-2 rounded-3 border-0 d-flex align-items-center" id="identity-tab" data-bs-toggle="tab" data-bs-target="#identity" type="button" role="tab" aria-controls="identity" aria-selected="false">
                                <i class="fa-solid fa-image-portrait me-3 fs-5"></i> <span>Site Identity</span>
                            </button>
                            <button class="nav-link text-start py-3 px-4 mb-2 rounded-3 border-0 d-flex align-items-center" id="reading-tab" data-bs-toggle="tab" data-bs-target="#reading" type="button" role="tab" aria-controls="reading" aria-selected="false">
                                <i class="fa-solid fa-book-open me-3 fs-5"></i> <span>Reading</span>
                            </button>
                            <button class="nav-link text-start py-3 px-4 mb-2 rounded-3 border-0 d-flex align-items-center" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab" aria-controls="seo" aria-selected="false">
                                <i class="fa-solid fa-magnifying-glass me-3 fs-5"></i> <span>Global SEO</span>
                            </button>
                            <button class="nav-link text-start py-3 px-4 mb-2 rounded-3 border-0 d-flex align-items-center" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab" aria-controls="analytics" aria-selected="false">
                                <i class="fa-solid fa-chart-line me-3 fs-5"></i> <span>Analytics</span>
                            </button>
                        </div>

                        <!-- Tab Contents -->
                        <div class="tab-content p-4 flex-grow-1" id="settingsTabsContent">
                            
                            <!-- 1. General Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                <div class="border rounded-4 p-4 bg-white shadow-sm mb-4">
                                    <h5 class="border-bottom pb-3 mb-4 font-weight-bold text-dark d-flex align-items-center">
                                        <i class="fa-solid fa-circle-info me-2 text-primary"></i> General Configurations
                                    </h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="site_title" class="form-label font-weight-bold">Site Title</label>
                                            <input type="text" name="site_title" id="site_title" class="form-control" value="{{ htmlspecialchars($site_title, ENT_QUOTES, 'UTF-8') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="site_tagline" class="form-label font-weight-bold">Site Tagline</label>
                                            <input type="text" name="site_tagline" id="site_tagline" class="form-control" value="{{ htmlspecialchars($site_tagline ?? '', ENT_QUOTES, 'UTF-8') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="site_email" class="form-label font-weight-bold">Site Email Address</label>
                                            <input type="email" name="site_email" id="site_email" class="form-control" value="{{ htmlspecialchars($site_email, ENT_QUOTES, 'UTF-8') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="site_timezone" class="form-label font-weight-bold">Timezone</label>
                                            <select name="site_timezone" id="site_timezone" class="form-select">
                                                <?php foreach ($timezones as $tz => $label) { ?>
                                                    <option value="{{ $tz }}" {{ $site_timezone === $tz ? 'selected' : '' }}>{{ $label }}</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="date_format" class="form-label font-weight-bold">Date Format</label>
                                            <select name="date_format" id="date_format" class="form-select">
                                                <?php foreach ($date_formats as $df => $label) { ?>
                                                    <option value="{{ $df }}" {{ $date_format === $df ? 'selected' : '' }}>{{ $label }}</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="time_format" class="form-label font-weight-bold">Time Format</label>
                                            <select name="time_format" id="time_format" class="form-select">
                                                <?php foreach ($time_formats as $tf => $label) { ?>
                                                    <option value="{{ $tf }}" {{ $time_format === $tf ? 'selected' : '' }}>{{ $label }}</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="maintenance_mode" class="form-label font-weight-bold">Maintenance Mode
                                                <span id="maintenance_mode_badge" class="badge ms-2 {{ $maintenance_mode === '1' ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $maintenance_mode === '1' ? 'ACTIVE' : 'INACTIVE' }}
                                                </span>
                                            </label>
                                            <select name="maintenance_mode" id="maintenance_mode" class="form-select">
                                                <option value="0" {{ $maintenance_mode === '0' ? 'selected' : '' }}>Disabled</option>
                                                <option value="1" {{ $maintenance_mode === '1' ? 'selected' : '' }}>Enabled (Front-end Under Maintenance)</option>
                                            </select>
                                            <div id="maintenance_mode_alert" class="alert mt-2 mb-0 py-2 px-3 small {{ $maintenance_mode === '1' ? 'alert-warning' : 'alert-info' }}">
                                                <?php if ($maintenance_mode === '1') { ?>
                                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <strong>Maintenance mode is currently ACTIVE.</strong> Visitors see the maintenance page. Admins and editors can still access the site normally.
                                                <?php } else { ?>
                                                    <i class="fa-solid fa-circle-info me-1"></i> When enabled, all frontend pages will show a maintenance notice to guests. Logged-in admins and editors will bypass this.
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold shadow-sm rounded-3"><i class="fa-solid fa-save me-2"></i> Save General Settings</button>
                                </div>
                            </div>

                            <!-- 2. Site Identity Tab -->
                            <div class="tab-pane fade" id="identity" role="tabpanel" aria-labelledby="identity-tab">
                                <div class="border rounded-4 p-4 bg-white shadow-sm mb-4">
                                    <h5 class="border-bottom pb-3 mb-4 font-weight-bold text-dark d-flex align-items-center">
                                        <i class="fa-solid fa-image me-2 text-primary"></i> Site Identity
                                    </h5>

                                    <!-- Logo Section -->
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark mb-2">Current Site Logo</label>
                                        <div class="d-flex align-items-start gap-4 p-3 border rounded-3 bg-light">
                                            <div class="logo-preview-box border rounded-3 bg-white d-flex align-items-center justify-content-center" style="width: 120px; height: 80px; overflow: hidden;">
                                                <img id="site_logo_preview" src="{{ !empty($logo) ? pathto($logo) : '' }}" class="img-fluid" style="max-height: 100%; object-fit: contain; {{ empty($logo) ? 'display: none;' : '' }}">
                                                <span id="site_logo_placeholder" class="text-muted small" style="{{ !empty($logo) ? 'display: none;' : '' }}">No Logo</span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="input-group">
                                                    <input type="text" name="site_logo" id="site_logo" class="form-control bg-white font-monospace" placeholder="Choose a logo file" value="{{ htmlspecialchars($logo ?? '', ENT_QUOTES, 'UTF-8') }}" readonly>
                                                    <button class="btn btn-primary btn-select-media" type="button" data-target="site_logo"><i class="fa-solid fa-images me-1"></i> Select</button>
                                                    <button class="btn btn-danger btn-clear-media" type="button" data-target="site_logo"><i class="fa-solid fa-trash me-1"></i> Clear</button>
                                                </div>
                                                <div class="form-text mt-1 text-muted">Choose a PNG or SVG logo file from your Media Library.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Favicon Section -->
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark mb-2">Current Site Favicon</label>
                                        <div class="d-flex align-items-start gap-4 p-3 border rounded-3 bg-light">
                                            <div class="logo-preview-box border rounded-3 bg-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; overflow: hidden;">
                                                <img id="site_favicon_preview" src="{{ !empty($favicon) ? pathto($favicon) : '' }}" class="img-fluid" style="max-height: 100%; object-fit: contain; {{ empty($favicon) ? 'display: none;' : '' }}">
                                                <span id="site_favicon_placeholder" class="text-muted small" style="{{ !empty($favicon) ? 'display: none;' : '' }}">No Icon</span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="input-group">
                                                    <input type="text" name="site_favicon" id="site_favicon" class="form-control bg-white font-monospace" placeholder="Choose a favicon file" value="{{ htmlspecialchars($favicon ?? '', ENT_QUOTES, 'UTF-8') }}" readonly>
                                                    <button class="btn btn-primary btn-select-media" type="button" data-target="site_favicon"><i class="fa-solid fa-images me-1"></i> Select</button>
                                                    <button class="btn btn-danger btn-clear-media" type="button" data-target="site_favicon"><i class="fa-solid fa-trash me-1"></i> Clear</button>
                                                </div>
                                                <div class="form-text mt-1 text-muted">Choose a .ico or .png favicon file from your Media Library.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Preloader Section -->
                                    <div class="mb-2">
                                        <label class="form-label font-weight-bold text-dark mb-2">Preloader Image</label>
                                        <div class="d-flex align-items-start gap-4 p-3 border rounded-3 bg-light">
                                            <div class="logo-preview-box border rounded-3 bg-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; overflow: hidden;">
                                                <img id="site_loader_preview" src="{{ !empty($loader) ? pathto($loader) : '' }}" class="img-fluid" style="max-height: 100%; object-fit: contain; {{ empty($loader) ? 'display: none;' : '' }}">
                                                <span id="site_loader_placeholder" class="text-muted small" style="{{ !empty($loader) ? 'display: none;' : '' }}">No Loader</span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="input-group">
                                                    <input type="text" name="site_loader" id="site_loader" class="form-control bg-white font-monospace" placeholder="Choose a loader file" value="{{ htmlspecialchars($loader ?? '', ENT_QUOTES, 'UTF-8') }}" readonly>
                                                    <button class="btn btn-primary btn-select-media" type="button" data-target="site_loader"><i class="fa-solid fa-images me-1"></i> Select</button>
                                                    <button class="btn btn-danger btn-clear-media" type="button" data-target="site_loader"><i class="fa-solid fa-trash me-1"></i> Clear</button>
                                                </div>
                                                <div class="form-text mt-1 text-muted">Choose an animated GIF, PNG or SVG loader file from your Media Library.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold shadow-sm rounded-3"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Save Identity Settings</button>
                                </div>
                            </div>

                            <!-- 3. Reading Settings Tab -->
                            <div class="tab-pane fade" id="reading" role="tabpanel" aria-labelledby="reading-tab">
                                <div class="border rounded-4 p-4 bg-white shadow-sm mb-4">
                                    <h5 class="border-bottom pb-3 mb-4 font-weight-bold text-dark d-flex align-items-center">
                                        <i class="fa-solid fa-book-open me-2 text-primary"></i> Reading Settings
                                    </h5>

                                    <!-- Homepage Displays Toggle -->
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark d-block mb-3">Your homepage displays</label>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="show_on_front" id="show_posts" value="posts" {{ $show_on_front === 'posts' ? 'checked' : '' }}>
                                            <label class="form-check-label font-weight-bold text-dark" for="show_posts">
                                                Your latest posts
                                            </label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="show_on_front" id="show_page" value="page" {{ $show_on_front === 'page' ? 'checked' : '' }}>
                                            <label class="form-check-label font-weight-bold text-dark" for="show_page">
                                                A static page (select below)
                                            </label>
                                        </div>

                                        <!-- Static page selector box -->
                                        <div class="p-3 border rounded-3 bg-light ms-4" id="static_page_options" style="{{ $show_on_front === 'page' ? '' : 'display: none;' }}">
                                            <div class="row align-items-center mb-3">
                                                <div class="col-sm-3">
                                                    <label for="page_on_front" class="form-label font-weight-bold mb-0">Homepage:</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <select name="page_on_front" id="page_on_front" class="form-select">
                                                        <option value="0">-- Select Page --</option>
                                                        <?php foreach ($pages as $p) { ?>
                                                            <option value="{{ $p->id }}" {{ (string)$page_on_front === (string)$p->id ? 'selected' : '' }}>{{ htmlspecialchars($p->title, ENT_QUOTES, 'UTF-8') }}</option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="row align-items-center">
                                                <div class="col-sm-3">
                                                    <label for="page_for_posts" class="form-label font-weight-bold mb-0">Posts page:</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <select name="page_for_posts" id="page_for_posts" class="form-select">
                                                        <option value="0">-- Select Page --</option>
                                                        <?php foreach ($pages as $p) { ?>
                                                            <option value="{{ $p->id }}" {{ (string)$page_for_posts === (string)$p->id ? 'selected' : '' }}>{{ htmlspecialchars($p->title, ENT_QUOTES, 'UTF-8') }}</option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Blog pages post limit -->
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <label for="posts_per_page" class="form-label font-weight-bold mb-0">Blog pages show at most</label>
                                        </div>
                                        <div class="col-auto" style="width: 100px;">
                                            <input type="number" name="posts_per_page" id="posts_per_page" class="form-control text-center" value="{{ htmlspecialchars($posts_per_page, ENT_QUOTES, 'UTF-8') }}" min="1" max="100">
                                        </div>
                                        <div class="col-auto">
                                            <span class="text-muted">posts</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold shadow-sm rounded-3"><i class="fa-solid fa-save me-2"></i> Save Reading Settings</button>
                                </div>
                            </div>

                            <!-- 4. Global SEO Settings Tab -->
                            <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                                <div class="border rounded-4 p-4 bg-white shadow-sm mb-4">
                                    <h5 class="border-bottom pb-3 mb-4 font-weight-bold text-dark d-flex align-items-center">
                                        <i class="fa-solid fa-magnifying-glass me-2 text-primary"></i> Global SEO Settings
                                    </h5>

                                    <div class="mb-4">
                                        <label for="meta_keywords" class="form-label font-weight-bold">Global Meta Keywords</label>
                                        <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" placeholder="Add keywords, press Enter" value="{{ htmlspecialchars($meta_keywords ?? '', ENT_QUOTES, 'UTF-8') }}">
                                        <div class="form-text text-muted mt-1">Meta keyword labels for search indexing.</div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="site_description" class="form-label font-weight-bold">Global Meta Description</label>
                                        <textarea name="site_description" id="site_description" class="form-control" rows="3" placeholder="Summary tag for search result snippets...">{{ htmlspecialchars($site_description, ENT_QUOTES, 'UTF-8') }}</textarea>
                                        <div class="form-text text-muted mt-1">Summary tag for search result snippets.</div>
                                    </div>

                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold shadow-sm rounded-3"><i class="fa-solid fa-save me-2"></i> Save SEO Settings</button>
                                </div>
                            </div>

                            <!-- 5. Analytics Integration Tab -->
                            <div class="tab-pane fade" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
                                <div class="border rounded-4 p-4 bg-white shadow-sm mb-4">
                                    <h5 class="border-bottom pb-3 mb-4 font-weight-bold text-dark d-flex align-items-center">
                                        <i class="fa-solid fa-chart-line me-2 text-primary"></i> Analytics Integration
                                    </h5>

                                    <div class="mb-2">
                                        <label for="analytics_code" class="form-label font-weight-bold">Tracking script headers</label>
                                        <textarea name="analytics_code" id="analytics_code" class="form-control font-monospace" rows="6" placeholder="<script>&#10;  // Google Analytics code here&#10;</script>">{{ htmlspecialchars($analytics_code ?? '', ENT_QUOTES, 'UTF-8') }}</textarea>
                                        <div class="form-text text-muted mt-1">Paste trackers block (Google Analytics, Facebook Pixel, etc.). Will be loaded on public header.</div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold shadow-sm rounded-3"><i class="fa-solid fa-save me-2"></i> Save Analytics Settings</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Media Selection Modal -->
<div class="modal fade" id="mediaSelectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-light border-bottom-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark"><i class="fa-solid fa-images text-primary me-2"></i> Select Asset from Media Library</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 500px; overflow-y: auto;">
                <div class="row row-cols-2 row-cols-md-4 g-3" id="modalMediaList">
                    <!-- Loaded dynamically via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Dynamic show/hide homepage static page configuration
    $('input[name="show_on_front"]').on('change', function() {
        if ($(this).val() === 'page') {
            $('#static_page_options').slideDown(250);
        } else {
            $('#static_page_options').slideUp(250);
        }
    });

    // 1b. Live Maintenance Mode badge + alert update
    $('#maintenance_mode').on('change', function() {
        const isEnabled = $(this).val() === '1';
        const $badge = $('#maintenance_mode_badge');
        const $alert = $('#maintenance_mode_alert');

        if (isEnabled) {
            $badge.removeClass('bg-success').addClass('bg-danger').text('ACTIVE');
            $alert.removeClass('alert-info').addClass('alert-warning').html(
                '<i class="fa-solid fa-triangle-exclamation me-1"></i> <strong>Maintenance mode is currently ACTIVE.</strong> Visitors see the maintenance page. Admins and editors can still access the site normally.'
            );
        } else {
            $badge.removeClass('bg-danger').addClass('bg-success').text('INACTIVE');
            $alert.removeClass('alert-warning').addClass('alert-info').html(
                '<i class="fa-solid fa-circle-info me-1"></i> When enabled, all frontend pages will show a maintenance notice to guests. Logged-in admins and editors will bypass this.'
            );
        }
    });

    // 2. Tagify for Meta Keywords
    const metaKeywordsInput = document.getElementById('meta_keywords');
    if (metaKeywordsInput && typeof Tagify !== 'undefined') {
        new Tagify(metaKeywordsInput, {
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
        });
    }

    // 3. ─── Media Library Modal (robust, freeze-free) ─────────────────────────
    //  • Destroys any stale Bootstrap instance before opening so backdrop never locks.
    //  • Uses 'hidden.bs.modal' to know when it is safe to re-show.
    //  • Card click writes value/preview then hides modal properly via getInstance().
    // ──────────────────────────────────────────────────────────────────────────

    const mediaModalEl = document.getElementById('mediaSelectModal');
    let _mediaTarget    = '';   // id of the input to populate
    let _mediaFetched   = false; // cache flag; reset when modal fully hides
    let _modalBusy      = false; // prevent double-open during animation

    /**
     * Safely open the media modal, fetching assets via AJAX.
     * @param {string} targetFieldId
     */
    function openMediaModal(targetFieldId) {
        if (_modalBusy) return;
        _mediaTarget = targetFieldId;

        // 1. Destroy any leftover Bootstrap instance to prevent backdrop stacking
        const staleInstance = bootstrap.Modal.getInstance(mediaModalEl);
        if (staleInstance) {
            staleInstance.dispose();
        }

        // 2. Remove any lingering backdrop that Bootstrap may have left behind
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow   = '';
        document.body.style.paddingRight = '';

        // 3. Create a fresh instance
        const modalInstance = new bootstrap.Modal(mediaModalEl, {
            backdrop: true,
            keyboard: true
        });

        // 4. Show spinner while loading
        $('#modalMediaList').html(
            '<div class="col-12 text-center py-5">' +
            '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading…</span></div>' +
            '<p class="text-muted mt-2 small">Loading media assets…</p>' +
            '</div>'
        );

        _modalBusy = true;
        modalInstance.show();

        // 5. Fetch media list
        $.ajax({
            url: "{{ pathto('admin/media') }}",
            type: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(data) {
                $('#modalMediaList').empty();

                const images = (data || []).filter(item => item.mime_type && item.mime_type.startsWith('image/'));

                if (images.length === 0) {
                    $('#modalMediaList').html(
                        '<div class="col-12 text-center text-muted py-4">' +
                        '<i class="fa-solid fa-image fa-2x mb-2 d-block opacity-50"></i>' +
                        'No image files found in your Media Library. Upload some first.' +
                        '</div>'
                    );
                    return;
                }

                images.forEach(function(item) {
                    const safeName = $('<span>').text(item.original_name).html();
                    const $card = $(
                        '<div class="col text-center">' +
                            '<div class="card h-100 shadow-sm border select-media-card rounded-3 overflow-hidden"' +
                                ' data-path="' + item.path + '" style="cursor:pointer;" role="button" tabindex="0">' +
                                '<img src="' + item.path + '" class="card-img-top p-1" style="height:100px;object-fit:contain;" loading="lazy">' +
                                '<div class="card-footer p-1 bg-light">' +
                                    '<span class="text-truncate d-block small px-1">' + safeName + '</span>' +
                                '</div>' +
                            '</div>' +
                        '</div>'
                    );
                    $('#modalMediaList').append($card);
                });
            },
            error: function(xhr, status, err) {
                $('#modalMediaList').html(
                    '<div class="col-12 text-center text-danger py-4">' +
                    '<i class="fa-solid fa-circle-exclamation fa-2x mb-2 d-block"></i>' +
                    'Failed to load media assets. Please try again.' +
                    '</div>'
                );
                flash('Failed to fetch media assets: ' + (err || status), 'danger');
            }
        });
    }

    // Release busy lock when modal fully hides
    mediaModalEl.addEventListener('hidden.bs.modal', function() {
        _modalBusy = false;
    });

    // Open modal on "Select" button click
    $(document).on('click', '.btn-select-media', function() {
        openMediaModal($(this).attr('data-target'));
    });

    // ── Card selection inside modal ────────────────────────────────────────────
    $(document).on('click', '.select-media-card', function() {
        const fullPath = $(this).attr('data-path');
        if (!fullPath || !_mediaTarget) return;

        // Strip BASE_URL prefix to store only the relative path
        const baseUrl = "{{ pathto('') }}";
        const cleanedPath = fullPath.startsWith(baseUrl)
            ? fullPath.slice(baseUrl.length).replace(/^\/+/, '')
            : fullPath;

        // Populate the target input and preview
        $('#' + _mediaTarget).val(cleanedPath);
        $('#' + _mediaTarget + '_preview').attr('src', fullPath).show();
        $('#' + _mediaTarget + '_placeholder').hide();

        // Safely close using getInstance (the instance must exist at this point)
        const instance = bootstrap.Modal.getInstance(mediaModalEl);
        if (instance) {
            instance.hide();
        }
    });

    // ── Clear button ───────────────────────────────────────────────────────────
    $(document).on('click', '.btn-clear-media', function() {
        const target = $(this).attr('data-target');
        if (!target) return;
        $('#' + target).val('');
        $('#' + target + '_preview').attr('src', '').hide();
        $('#' + target + '_placeholder').show();
    });

    // 4. AJAX Save Form Submit Handler
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: "{{ pathto('admin/settings') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    flash('Settings saved successfully!', 'success');
                } else {
                    flash(res.message || 'Save failed', 'warning');
                }
            },
            error: function() {
                flash('Network or server error occurred', 'danger');
            }
        });

        return false;
    });

});
</script>

{{ $this->view('admin/layout/footer') }}
