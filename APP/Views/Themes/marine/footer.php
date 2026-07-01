<?php
$site_title        = $site_title ?? '';
$site_description  = $site_description ?? '';
$site_email        = $site_email ?? '';
$footer_menu_items = $footer_menu_items ?? [];
$custom_js_footer  = $custom_js_footer ?? '';
?>
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <h5 class="text-white font-weight-bold mb-3">{{ $site_title }}</h5>
                    <p class="small text-muted">{{ $site_description }}</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="text-white font-weight-bold mb-3">Links</h6>
                    <ul class="list-unstyled small">
                        <?php if (empty($footer_menu_items)) { ?>
                            <li><a href="{{ pathto('') }}">Home</a></li>
                            <li><a href="{{ pathto('login') }}">Admin Login</a></li>
                        <?php } else { ?>
                            <?php foreach ($footer_menu_items as $item) { 
                                $href = $item['type'] === 'custom' ? $item['url'] : ($item['type'] === 'page' ? pathto('page/' . (new \App\Models\PostModel())->getPostById((int)$item['object_id'])->slug) : pathto('category/' . (new \App\Models\TaxonomyModel())->getTaxonomyById((int)$item['object_id'])->slug));
                                ?>
                                <li class="mb-2"><a href="{{ $href }}">{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}</a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="text-white font-weight-bold mb-3">Contact</h6>
                    <p class="small mb-0"><i class="fa-solid fa-envelope me-2 text-info"></i> {{ $site_email }}</p>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start small text-muted">
                    &copy; <?= date('Y') ?> {{ $site_title }}. All rights reserved.
                </div>
                <div class="col-md-6 text-center text-md-end small">
                    <span class="text-muted">Powered by <a href="#" class="text-white">CMsys</a> &amp; ICTM Framework</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Core Scripts -->
    <script src="{{ pathto('js/jquery3.7.1.min.js') }}"></script>
    <script src="{{ pathto('js/bootstrap5.3.8.bundle.min.js') }}"></script>
    
    {{ $custom_js_footer }}
</body>
</html>
