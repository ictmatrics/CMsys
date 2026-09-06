<?php
$theme_config = $theme_config ?? [];
$raw_css_config = $theme_config['custom_css'] ?? '';
$codies_config = [];
if (preg_match('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', $raw_css_config, $matches)) {
    $codies_config = json_decode($matches[1], true) ?: [];
}
$theme_config = array_merge($theme_config, $codies_config);

$footer_layout = $theme_config['footer_layout'] ?? 'layout1';
$footer_menu_items = $footer_menu_items ?? [];
$site_title = $site_title ?? 'Codies';
$site_email = $site_email ?? '';
$site_logo = $site_logo ?? '';

$footer_scripts = $theme_config['footer_scripts'] ?? '';
$custom_js_footer = $custom_js_footer ?? '';
?>

<!-- ========================================== -->
<!-- ADVERTISEMENT: BOTTOM FOOTER BANNER        -->
<!-- ========================================== -->
<?php if (!empty($theme_config['ad_footer_enable'])) { ?>
    <div class="container my-4 text-center d-flex justify-content-center">
        <div class="ad-leaderboard">
            <span class="ad-tag text-muted">Sponsor</span>
            <?php if (!empty($theme_config['ad_footer_html'])) { ?>
                {{ htmlspecialchars_decode($theme_config['ad_footer_html']) }}
            <?php } else { ?>
                <a href="{{ htmlspecialchars($theme_config['ad_footer_link'] ?? '#', ENT_QUOTES, 'UTF-8') }}" target="_blank">
                    <?php if (!empty($theme_config['ad_footer_img'])) { ?>
                        <img src="{{ pathto($theme_config['ad_footer_img']) }}" alt="{{ htmlspecialchars($theme_config['ad_footer_alt'] ?? 'Ad', ENT_QUOTES, 'UTF-8') }}" class="w-100 h-100" style="object-fit: cover;">
                    <?php } ?>
                </a>
            <?php } ?>
        </div>
    </div>
<?php } ?>

<!-- ========================================== -->
<!-- FOOTER LAYOUT SWITCHER                     -->
<!-- ========================================== -->

<?php
$footer_about_text = !empty($theme_config['footer_about_text']) ? $theme_config['footer_about_text'] : 'An elegant modern technical catalog constructed to help developers and designers bridge core logic and interface design paradigms seamlessly.';
$footer_copyright = !empty($theme_config['footer_copyright']) ? $theme_config['footer_copyright'] : 'ICT Matrics Private Limited. All rights reserved globally.';
$footer_community_title = !empty($theme_config['footer_community_title']) ? $theme_config['footer_community_title'] : 'Community Support';
$footer_community_desc = !empty($theme_config['footer_community_desc']) ? $theme_config['footer_community_desc'] : 'Engage directly with core maintainers and framework leads over discord and github discussion pools.';

$footer_github   = trim($theme_config['footer_github'] ?? '');
$footer_discord  = trim($theme_config['footer_discord'] ?? '');
$footer_twitter  = trim($theme_config['footer_twitter'] ?? '');
$footer_facebook = trim($theme_config['footer_facebook'] ?? '');
$footer_youtube  = trim($theme_config['footer_youtube'] ?? '');
$footer_tiktok   = trim($theme_config['footer_tiktok'] ?? '');
?>

<footer class="site-footer py-5 mt-5">
    <div class="container">
        <?php if ($footer_layout === 'layout1') { ?>
            <!-- FOOTER LAYOUT 1: 4-Column Directory Grid -->
            <div class="row g-4">
                <div class="col-lg-4 col-12">
                    <?php if (!empty($site_logo)) { ?>
                        <img src="{{ pathto($site_logo) }}" alt="{{ htmlspecialchars($site_title, ENT_QUOTES, 'UTF-8') }}" style="max-height: 40px; object-fit: contain;">
                    <?php } else { ?>
                        <span class="fs-3 logo-font">
                            <span class="logo-bracket">&lt;</span>Codies<span class="logo-bracket">/&gt;</span>
                        </span>
                    <?php } ?>
                    <p class="mt-3 small">{{ htmlspecialchars($footer_about_text, ENT_QUOTES, 'UTF-8') }}</p>
                    <p class="small">&copy; {{ date('Y') }} {{ htmlspecialchars($footer_copyright, ENT_QUOTES, 'UTF-8') }}</p>
                </div>
                
                <div class="col-md-4 col-6 col-lg-2">
                    <h5 class="fw-bold mb-3">Core Menu</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2 small">
                        <li><a href="{{ pathto('') }}"><i class="fa-solid fa-house me-1"></i> Home</a></li>
                        <?php foreach ($footer_menu_items as $item) { 
                            $href = $item['type'] === 'custom' ? $item['url'] : ($item['type'] === 'page' ? pathto((new \App\Models\PostModel())->getPostById((int)$item['object_id'])->slug) : pathto('category/' . (new \App\Models\TaxonomyModel())->getTaxonomyById((int)$item['object_id'])->slug));
                            ?>
                            <li><a href="{{ $href }}">{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}</a></li>
                        <?php } ?>
                    </ul>
                </div>

                <div class="col-md-4 col-6 col-lg-2">
                    <h5 class="fw-bold mb-3">Social &amp; Support</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2 small">
                        <?php if ($footer_github !== '') { ?>
                            <li><a href="{{ htmlspecialchars($footer_github, ENT_QUOTES, 'UTF-8') }}" target="_blank"><i class="fa-brands fa-github me-1"></i> GitHub</a></li>
                        <?php } ?>
                        <?php if ($footer_discord !== '') { ?>
                            <li><a href="{{ htmlspecialchars($footer_discord, ENT_QUOTES, 'UTF-8') }}" target="_blank"><i class="fa-brands fa-discord me-1"></i> Discord</a></li>
                        <?php } ?>
                        <?php if ($footer_twitter !== '') { ?>
                            <li><a href="{{ htmlspecialchars($footer_twitter, ENT_QUOTES, 'UTF-8') }}" target="_blank"><i class="fa-brands fa-twitter me-1"></i> Twitter / X</a></li>
                        <?php } ?>
                        <?php if ($footer_facebook !== '') { ?>
                            <li><a href="{{ htmlspecialchars($footer_facebook, ENT_QUOTES, 'UTF-8') }}" target="_blank"><i class="fa-brands fa-facebook me-1"></i> Facebook</a></li>
                        <?php } ?>
                        <?php if ($footer_youtube !== '') { ?>
                            <li><a href="{{ htmlspecialchars($footer_youtube, ENT_QUOTES, 'UTF-8') }}" target="_blank"><i class="fa-brands fa-youtube me-1"></i> YouTube</a></li>
                        <?php } ?>
                        <?php if ($footer_tiktok !== '') { ?>
                            <li><a href="{{ htmlspecialchars($footer_tiktok, ENT_QUOTES, 'UTF-8') }}" target="_blank"><i class="fa-brands fa-tiktok me-1"></i> TikTok</a></li>
                        <?php } ?>
                        <?php if (!empty($site_email)) { ?>
                            <li><a href="mailto:{{ $site_email }}"><i class="fa-solid fa-envelope me-1"></i> Contact Admin</a></li>
                        <?php } ?>
                    </ul>
                </div>

                <div class="col-md-4 col-12 col-lg-4">
                    <h5 class="fw-bold mb-3">{{ htmlspecialchars($footer_community_title, ENT_QUOTES, 'UTF-8') }}</h5>
                    <p class="small">{{ htmlspecialchars($footer_community_desc, ENT_QUOTES, 'UTF-8') }}</p>
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <?php if ($footer_github !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_github, ENT_QUOTES, 'UTF-8') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;" title="GitHub"><i class="fa-brands fa-github"></i></a>
                        <?php } ?>
                        <?php if ($footer_discord !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_discord, ENT_QUOTES, 'UTF-8') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;" title="Discord"><i class="fa-brands fa-discord"></i></a>
                        <?php } ?>
                        <?php if ($footer_twitter !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_twitter, ENT_QUOTES, 'UTF-8') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;" title="Twitter"><i class="fa-brands fa-twitter"></i></a>
                        <?php } ?>
                        <?php if ($footer_facebook !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_facebook, ENT_QUOTES, 'UTF-8') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <?php } ?>
                        <?php if ($footer_youtube !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_youtube, ENT_QUOTES, 'UTF-8') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                        <?php } ?>
                        <?php if ($footer_tiktok !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_tiktok, ENT_QUOTES, 'UTF-8') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;" title="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>

        <?php } elseif ($footer_layout === 'layout2') { ?>
            <!-- FOOTER LAYOUT 2: Centered Minimalist -->
            <div class="text-center py-4">
                <?php if (!empty($site_logo)) { ?>
                    <img src="{{ pathto($site_logo) }}" alt="{{ htmlspecialchars($site_title, ENT_QUOTES, 'UTF-8') }}" style="max-height: 45px; object-fit: contain;">
                <?php } else { ?>
                    <span class="fs-2 logo-font">
                        <span class="logo-bracket">&lt;</span>Codies<span class="logo-bracket">/&gt;</span>
                    </span>
                <?php } ?>
                <div class="d-flex justify-content-center gap-4 my-3">
                    <a href="{{ pathto('') }}" class="small">Home</a>
                    <?php foreach ($footer_menu_items as $item) { 
                        $href = $item['type'] === 'custom' ? $item['url'] : ($item['type'] === 'page' ? pathto((new \App\Models\PostModel())->getPostById((int)$item['object_id'])->slug) : pathto('category/' . (new \App\Models\TaxonomyModel())->getTaxonomyById((int)$item['object_id'])->slug));
                        ?>
                        <a href="{{ $href }}" class="small">{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}</a>
                    <?php } ?>
                </div>
                <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
                    <?php if ($footer_github !== '') { ?>
                        <a href="{{ htmlspecialchars($footer_github, ENT_QUOTES, 'UTF-8') }}" target="_blank" title="GitHub"><i class="fa-brands fa-github fa-lg"></i></a>
                    <?php } ?>
                    <?php if ($footer_discord !== '') { ?>
                        <a href="{{ htmlspecialchars($footer_discord, ENT_QUOTES, 'UTF-8') }}" target="_blank" title="Discord"><i class="fa-brands fa-discord fa-lg"></i></a>
                    <?php } ?>
                    <?php if ($footer_twitter !== '') { ?>
                        <a href="{{ htmlspecialchars($footer_twitter, ENT_QUOTES, 'UTF-8') }}" target="_blank" title="Twitter"><i class="fa-brands fa-twitter fa-lg"></i></a>
                    <?php } ?>
                    <?php if ($footer_facebook !== '') { ?>
                        <a href="{{ htmlspecialchars($footer_facebook, ENT_QUOTES, 'UTF-8') }}" target="_blank" title="Facebook"><i class="fa-brands fa-facebook fa-lg"></i></a>
                    <?php } ?>
                    <?php if ($footer_youtube !== '') { ?>
                        <a href="{{ htmlspecialchars($footer_youtube, ENT_QUOTES, 'UTF-8') }}" target="_blank" title="YouTube"><i class="fa-brands fa-youtube fa-lg"></i></a>
                    <?php } ?>
                    <?php if ($footer_tiktok !== '') { ?>
                        <a href="{{ htmlspecialchars($footer_tiktok, ENT_QUOTES, 'UTF-8') }}" target="_blank" title="TikTok"><i class="fa-brands fa-tiktok fa-lg"></i></a>
                    <?php } ?>
                </div>
                <p class="small mb-0">&copy; {{ date('Y') }} {{ htmlspecialchars($footer_copyright, ENT_QUOTES, 'UTF-8') }}</p>
            </div>

        <?php } else { ?>
            <!-- FOOTER LAYOUT 3: Compact Side-by-Side Logo + Links -->
            <div class="row align-items-center g-3">
                <div class="col-md-6 text-center text-md-start">
                    <?php if (!empty($site_logo)) { ?>
                        <img src="{{ pathto($site_logo) }}" alt="{{ htmlspecialchars($site_title, ENT_QUOTES, 'UTF-8') }}" style="max-height: 35px; object-fit: contain;">
                    <?php } else { ?>
                        <span class="fs-4 logo-font">
                            <span class="logo-bracket">&lt;</span>Codies<span class="logo-bracket">/&gt;</span>
                        </span>
                    <?php } ?>
                    <span class="small ms-2">&copy; {{ date('Y') }} {{ htmlspecialchars($footer_copyright, ENT_QUOTES, 'UTF-8') }}</span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="d-inline-flex gap-3 align-items-center flex-wrap justify-content-center justify-content-md-end">
                        <?php foreach ($footer_menu_items as $item) { 
                            $href = $item['type'] === 'custom' ? $item['url'] : ($item['type'] === 'page' ? pathto((new \App\Models\PostModel())->getPostById((int)$item['object_id'])->slug) : pathto('category/' . (new \App\Models\TaxonomyModel())->getTaxonomyById((int)$item['object_id'])->slug));
                            ?>
                            <a href="{{ $href }}" class="small">{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}</a>
                        <?php } ?>
                        <?php if ($footer_github !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_github, ENT_QUOTES, 'UTF-8') }}" target="_blank" class="ms-2" title="GitHub"><i class="fa-brands fa-github"></i></a>
                        <?php } ?>
                        <?php if ($footer_discord !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_discord, ENT_QUOTES, 'UTF-8') }}" target="_blank" title="Discord"><i class="fa-brands fa-discord"></i></a>
                        <?php } ?>
                        <?php if ($footer_twitter !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_twitter, ENT_QUOTES, 'UTF-8') }}" target="_blank" title="Twitter"><i class="fa-brands fa-twitter"></i></a>
                        <?php } ?>
                        <?php if ($footer_facebook !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_facebook, ENT_QUOTES, 'UTF-8') }}" target="_blank" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <?php } ?>
                        <?php if ($footer_youtube !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_youtube, ENT_QUOTES, 'UTF-8') }}" target="_blank" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                        <?php } ?>
                        <?php if ($footer_tiktok !== '') { ?>
                            <a href="{{ htmlspecialchars($footer_tiktok, ENT_QUOTES, 'UTF-8') }}" target="_blank" title="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</footer>

<!-- Floating Dynamic Back to Top Element -->
<button id="backToTop" aria-label="Back to top" style="position: fixed; bottom: 25px; right: 25px; width: 50px; height: 50px; border-radius: 50%; background-color: var(--accent-color); color: white; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.15); z-index: 1050; display: none; align-items: center; justify-content: center; cursor: pointer; transition: background-color 0.2s ease, transform 0.2s ease;">
    <i class="fa-solid fa-arrow-up fa-lg"></i>
</button>

<!-- Notification Center Toast -->
<div class="notif-box" id="globalToast" style="position: fixed; bottom: 20px; left: 20px; background-color: var(--bg-card); border-left: 4px solid var(--accent-color); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); padding: 1rem 1.5rem; border-radius: 4px 10px 10px 4px; z-index: 2000; display: none; align-items: center; gap: 12px; max-width: 350px;">
    <i class="fa-solid fa-circle-info text-primary fa-lg"></i>
    <div>
        <span class="d-block small text-muted">Codies System Interface</span>
        <strong id="toastMessage" class="small">Workspace loaded successfully!</strong>
    </div>
</div>

<!-- JS Dependencies -->
<!-- jQuery Library (v3.7.1) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap Bundle with Popper JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- WOW.js animation engine -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<!-- Prism JS core library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>
<!-- Syntax loaders for dynamic formatting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-python.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>

<!-- Custom Core Mechanics Script -->
<script src="{{ pathto('js/script.js') }}"></script>
<script>
    // Initialize WOW.js Animation framework
    <?php if (!empty($theme_config['wow_animations'])) { ?>
        new WOW().init();
    <?php } ?>

    // Load Codies theme script
    <?php include APPPATH . 'Views/Themes/codies/codies.js'; ?>
</script>

{{ $custom_js_footer }}
{{ $footer_scripts }}
</body>
</html>
