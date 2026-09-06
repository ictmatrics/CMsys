<?php
$siteTitle = $site_title ?? 'N-Tech Enterprise';
$nConfig = array_merge([
    'footer_layout' => 'style1',
    'contact_email' => 'contact@ntech.com',
    'contact_phone' => '+1 (800) 555-0199',
    'contact_address' => '100 Innovation Way, Tech District, CA 94016',
    'newsletter_title' => 'Subscribe to Tech Insights',
    'newsletter_subtitle' => 'Get the latest trends in software engineering and cloud transformation.',
], $theme_config ?? []);
?>

    <?php if ($nConfig['footer_layout'] === 'style3') { ?>
        <!-- FOOTER STYLE 3: Newsletter Focused Header -->
        <section class="bg-primary text-white py-5">
            <div class="container text-center">
                <h3 class="fw-bold mb-2">{{ $nConfig['newsletter_title'] }}</h3>
                <p class="mb-4 text-white-50" style="max-width: 600px; margin: 0 auto;">{{ $nConfig['newsletter_subtitle'] }}</p>
                <form class="d-flex justify-content-center mx-auto" style="max-width: 500px;" action="#" method="POST">
                    <input type="email" class="form-control me-2 py-3 rounded-pill" placeholder="Enter your email address...">
                    <button class="btn btn-dark px-4 py-3 rounded-pill fw-bold" type="submit">Subscribe</button>
                </form>
            </div>
        </section>
    <?php } ?>

    <!-- MAIN FOOTER SECTION (with Dynamic Project Menu Configuration) -->
    <footer class="bg-dark text-white pt-5 pb-4 border-top border-secondary">
        <div class="container">
            <?php if ($nConfig['footer_layout'] === 'style1') { ?>
                <!-- STYLE 1: Multi-Column Detailed -->
                <div class="row g-4 mb-5">
                    <div class="col-lg-4 col-md-6">
                        <h4 class="text-primary fw-bold mb-3"><i class="fa-solid fa-microchip me-2"></i>N-Tech</h4>
                        <p class="text-white-50 mb-3">Pioneering software development, enterprise AI, cloud integration, and cybersecurity services for forward-thinking organizations globally.</p>
                        <div class="text-white-50 small">
                            <p class="mb-1"><i class="fa-solid fa-location-dot me-2 text-primary"></i>{{ $nConfig['contact_address'] }}</p>
                            <p class="mb-1"><i class="fa-solid fa-phone me-2 text-primary"></i>{{ $nConfig['contact_phone'] }}</p>
                            <p class="mb-0"><i class="fa-solid fa-envelope me-2 text-primary"></i>{{ $nConfig['contact_email'] }}</p>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <h5 class="fw-bold mb-3">Navigation</h5>
                        <ul class="list-unstyled text-white-50">
                            <?php 
                            $fMenuItems = $footer_menu_items ?? ($footerMenuItems ?? []);
                            if (!empty($fMenuItems)) {
                                foreach ($fMenuItems as $fItem) {
                                    $fObj = is_object($fItem) ? $fItem : (object)$fItem;
                                    $fTitle = $fObj->title ?? ($fObj->label ?? '');
                                    $fUrl = $fObj->url ?? ($fObj->link ?? '#');
                                    if (empty($fTitle)) continue;
                                    $fHref = ntech_normalize_menu_url((string)$fUrl);
                            ?>
                                    <li class="mb-2"><a href="{{ $fHref }}" class="text-white-50 text-decoration-none">{{ htmlspecialchars($fTitle, ENT_QUOTES, 'UTF-8') }}</a></li>
                            <?php 
                                } 
                            } else { 
                            ?>
                                <li class="mb-2"><a href="{{ pathto('about') }}" class="text-white-50 text-decoration-none">About Us</a></li>
                                <li class="mb-2"><a href="{{ pathto('services') }}" class="text-white-50 text-decoration-none">Services</a></li>
                                <li class="mb-2"><a href="{{ pathto('products') }}" class="text-white-50 text-decoration-none">Products</a></li>
                                <li class="mb-2"><a href="{{ pathto('faq') }}" class="text-white-50 text-decoration-none">FAQ</a></li>
                                <li class="mb-2"><a href="{{ pathto('contact') }}" class="text-white-50 text-decoration-none">Contact Us</a></li>
                            <?php } ?>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <h5 class="fw-bold mb-3">Recent Posts</h5>
                        <ul class="list-unstyled text-white-50">
                            <?php 
                            $recentPosts = $recent_posts ?? [];
                            $count = 0;
                            foreach ($recentPosts as $p) {
                                if ($count++ >= 3) break;
                            ?>
                                <li class="mb-2">
                                    <a href="{{ pathto($p['slug']) }}" class="text-white-50 text-decoration-none d-block text-truncate">
                                        <i class="fa-solid fa-angle-right me-1 text-primary"></i>{{ htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8') }}
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <h5 class="fw-bold mb-3">Newsletter</h5>
                        <p class="text-white-50 small mb-3">Subscribe to receive technical whitepapers and release updates.</p>
                        <form action="#" method="POST">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Your email...">
                                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } else { ?>
                <!-- STYLE 2: Minimal Grid with Dynamic Footer Navigation -->
                <div class="row g-4 mb-4 align-items-center">
                    <div class="col-md-6">
                        <h4 class="text-primary fw-bold mb-1"><i class="fa-solid fa-microchip me-2"></i>N-Tech</h4>
                        <p class="text-white-50 mb-0">Empowering business with enterprise software solutions.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <?php 
                        $fMenuItems = $footer_menu_items ?? ($footerMenuItems ?? []);
                        if (!empty($fMenuItems)) {
                            foreach ($fMenuItems as $fItem) {
                                $fObj = is_object($fItem) ? $fItem : (object)$fItem;
                                $fTitle = $fObj->title ?? ($fObj->label ?? '');
                                $fUrl = $fObj->url ?? ($fObj->link ?? '#');
                                if (empty($fTitle)) continue;
                                $fHref = ntech_normalize_menu_url((string)$fUrl);
                        ?>
                                <a href="{{ $fHref }}" class="text-white-50 me-3 text-decoration-none">{{ htmlspecialchars($fTitle, ENT_QUOTES, 'UTF-8') }}</a>
                        <?php 
                            } 
                        } else { 
                        ?>
                            <a href="{{ pathto('') }}" class="text-white-50 me-3 text-decoration-none">Home</a>
                            <a href="{{ pathto('about') }}" class="text-white-50 me-3 text-decoration-none">About</a>
                            <a href="{{ pathto('services') }}" class="text-white-50 me-3 text-decoration-none">Services</a>
                            <a href="{{ pathto('contact') }}" class="text-white-50 text-decoration-none">Contact</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <div class="border-top border-secondary pt-4 text-center text-white-50 small">
                <p class="mb-0">&copy; {{ date('Y') }} {{ $siteTitle }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Vendor Scripts (Migrated to Themes/n-tech/js Hierarchy) -->
    <script src="{{ pathto('js/jquery3.7.1.min.js') }}"></script>
    <script src="{{ pathto('Themes/n-tech/js/bootstrap.min.js') }}"></script>
    <script src="{{ pathto('Themes/n-tech/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ pathto('Themes/n-tech/js/owl.min.js') }}"></script>
    <script src="{{ pathto('Themes/n-tech/js/aos.js') }}"></script>
    <script src="{{ pathto('Themes/n-tech/js/wow.min.js') }}"></script>
    <script src="{{ pathto('Themes/n-tech/js/gsap.min.js') }}"></script>
    <script src="{{ pathto('Themes/n-tech/js/ScrollTrigger.min.js') }}"></script>
    <script src="{{ pathto('Themes/n-tech/js/main.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof WOW !== 'undefined') {
                new WOW().init();
            }
            if (typeof AOS !== 'undefined') {
                AOS.init({ duration: 800, once: true });
            }
        });
    </script>
</body>
</html>
