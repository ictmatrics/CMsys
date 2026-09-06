{{ $this->view('Themes/' . $theme_name . '/header', $data) }}

<?php
$nConfig = array_merge([
    'get_in_touch_layout' => 'option1',
    'hero_title_1' => 'We helping 1M+ Digital Business',
    'hero_subtitle_1' => 'INNOVATIVE SOLUTIONS',
    'hero_btn_text_1' => 'Discover More',
    'hero_btn_link_1' => pathto('services'),
    'hero_image_1' => pathto('Themes/n-tech/images/hero/hm6-bg1.webp'),

    'hero_title_2' => 'Next-Gen IT Consultancy & Digital Engineering',
    'hero_subtitle_2' => 'ENTERPRISE TECH SOLUTIONS',
    'hero_btn_text_2' => 'Our Services',
    'hero_btn_link_2' => pathto('services'),
    'hero_image_2' => pathto('Themes/n-tech/images/hero/hm6-bg2.webp'),

    'hero_title_3' => 'Accelerate Enterprise Growth with N-Tech',
    'hero_subtitle_3' => 'HIGH PERFORMANCE PLATFORMS',
    'hero_btn_text_3' => 'Contact Team',
    'hero_btn_link_3' => pathto('contact'),
    'hero_image_3' => pathto('Themes/n-tech/images/hero/hero-3.png'),

    'services_title' => 'Our Core Services',
    'services_subtitle' => 'End-to-End Enterprise Tech Solutions',

    'logo_slider_title' => 'Trusted by Industry Leaders Worldwide',
    'logo_slider_enable' => '1',
    'contact_email' => 'contact@ntech.com',
    'contact_phone' => '+1 (800) 555-0199',
    'contact_address' => '100 Innovation Way, Tech District, CA 94016',
    'contact_map_url' => 'https://maps.google.com/maps?q=California&t=&z=13&ie=UTF8&iwloc=&output=embed',
], $theme_config ?? []);

$pgObj = isset($page) ? (is_object($page) ? $page : (object)$page) : null;
$blocks = $blocks ?? [];
?>

<!--==============================
Hero Section Six (Aligned with /public_html/n-tech/index.html)
==============================-->
<section class="tv-hero-section style-6 overflow-hidden z-2 bg-light">
    <div class="hero-inner position-relative">
        <div class="container-fluid px-0">
            <div id="ntechHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-inner">
                    
                    <!-- Slide 1 -->
                    <div class="carousel-item active">
                        <div class="hero-area position-relative py-5 min-vh-75 d-flex align-items-center" style="background: url('{{ $nConfig['hero_image_1'] }}') no-repeat center center / cover, #0b1136;">
                            <div class="container position-relative py-5" style="z-index: 2;">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="hero-content text-white">
                                            <span class="sub-title d-inline-flex align-items-center gap-2 mb-3 px-3 py-1 bg-white bg-opacity-10 rounded-pill text-uppercase fw-bold text-primary border border-primary border-opacity-25" style="font-size: 13px;">
                                                <i class="fa-solid fa-circle-check text-primary me-1"></i>{{ $nConfig['hero_subtitle_1'] }}
                                            </span>
                                            <h1 class="hero-title text-white display-4 fw-bold mb-4 heading-font">{{ $nConfig['hero_title_1'] }}</h1>
                                            <div class="text-icon position-relative mb-4">
                                                <p class="text text-white-50 fs-5 mb-0" style="max-width: 650px;">Empower enterprise standards through scalable architecture, high availability, and AI infrastructure models.</p>
                                            </div>
                                            <div class="hero-user d-flex flex-wrap align-items-center gap-4 pt-2">
                                                <a href="{{ $nConfig['hero_btn_link_1'] }}" class="theme-btn btn btn-primary btn-lg rounded-pill px-4 fw-bold d-inline-flex align-items-center gap-2 shadow">
                                                    <span>{{ $nConfig['hero_btn_text_1'] }}</span>
                                                    <i class="fa-solid fa-arrow-right ms-2"></i>
                                                </a>                                    
                                                <div class="hero-social-proof d-flex align-items-center gap-3 bg-dark bg-opacity-50 px-3 py-2 rounded-pill border border-white border-opacity-10">
                                                    <div class="happy-customers text-white">
                                                        <div class="fw-bold fs-6">1.6M+</div>
                                                        <div class="rating-viewers text-white-50 small">Active Global Customers</div>
                                                    </div>
                                                </div>                                    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="carousel-item">
                        <div class="hero-area position-relative py-5 min-vh-75 d-flex align-items-center" style="background: url('{{ $nConfig['hero_image_2'] }}') no-repeat center center / cover, #090e2d;">
                            <div class="container position-relative py-5" style="z-index: 2;">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="hero-content text-white">
                                            <span class="sub-title d-inline-flex align-items-center gap-2 mb-3 px-3 py-1 bg-white bg-opacity-10 rounded-pill text-uppercase fw-bold text-info border border-info border-opacity-25" style="font-size: 13px;">
                                                <i class="fa-solid fa-shield-halved text-info me-1"></i>{{ $nConfig['hero_subtitle_2'] }}
                                            </span>
                                            <h1 class="hero-title text-white display-4 fw-bold mb-4 heading-font">{{ $nConfig['hero_title_2'] }}</h1>
                                            <div class="hero-user d-flex flex-wrap align-items-center gap-4 pt-2">
                                                <a href="{{ $nConfig['hero_btn_link_2'] }}" class="theme-btn btn btn-info text-dark btn-lg rounded-pill px-4 fw-bold d-inline-flex align-items-center gap-2 shadow">
                                                    <span>{{ $nConfig['hero_btn_text_2'] }}</span>
                                                    <i class="fa-solid fa-arrow-right ms-2"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#ntechHeroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#ntechHeroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- DYNAMIC PAGE CONTENT & INTEGRATED BLOCK SECTIONS -->
<?php if ($pgObj && (!empty($pgObj->content) || !empty($blocks))) { ?>
<section class="py-5 bg-white border-bottom">
    <div class="container py-3">
        <?php if (!empty($pgObj->content)) { ?>
            <div class="page-main-content mb-4 text-secondary">
                {{ $pgObj->content }}
            </div>
        <?php } ?>

        <?php if (!empty($blocks) && is_array($blocks)) { ?>
            <div class="integrated-blocks row g-4">
                <?php foreach ($blocks as $block) { 
                    $bTitle = $block['title'] ?? ($block['heading'] ?? '');
                    $bBody = $block['content'] ?? ($block['body'] ?? '');
                ?>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm p-4 rounded-4">
                            <?php if (!empty($bTitle)) { ?>
                                <h3 class="fw-bold mb-3 heading-font text-primary">{{ htmlspecialchars($bTitle, ENT_QUOTES, 'UTF-8') }}</h3>
                            <?php } ?>
                            <?php if (!empty($bBody)) { ?>
                                <div class="text-secondary">{{ apply_filters('the_content', $bBody) }}</div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>
<?php } ?>

<!-- CORPORATE LOGO SLIDER -->
<?php if ($nConfig['logo_slider_enable'] === '1') { ?>
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <p class="text-center text-muted fw-bold small uppercase mb-3">{{ $nConfig['logo_slider_title'] }}</p>
        <div class="row align-items-center justify-content-center g-4 text-center opacity-75">
            <div class="col-6 col-md-2 fw-bold text-secondary"><i class="fa-brands fa-aws fa-2x me-1"></i> AWS</div>
            <div class="col-6 col-md-2 fw-bold text-secondary"><i class="fa-brands fa-google fa-2x me-1"></i> Google</div>
            <div class="col-6 col-md-2 fw-bold text-secondary"><i class="fa-brands fa-microsoft fa-2x me-1"></i> Microsoft</div>
            <div class="col-6 col-md-2 fw-bold text-secondary"><i class="fa-brands fa-docker fa-2x me-1"></i> Docker</div>
            <div class="col-6 col-md-2 fw-bold text-secondary"><i class="fa-brands fa-github fa-2x me-1"></i> GitHub</div>
        </div>
    </div>
</section>
<?php } ?>

<!-- CORE SERVICES SECTION -->
<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase tracking-wider small">What We Do</span>
            <h2 class="fw-bold heading-font display-6 mb-2">{{ $nConfig['services_title'] }}</h2>
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">{{ $nConfig['services_subtitle'] }}</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center hover-top">
                    <div class="mb-3 text-primary">
                        <i class="fa-solid fa-code fa-3x"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Custom Software Engineering</h4>
                    <p class="text-muted mb-3">Custom web and mobile applications engineered with PHP MVC, React, and microservices architecture.</p>
                    <a href="{{ pathto('services') }}" class="fw-bold text-primary text-decoration-none">Learn More <i class="fa-solid fa-chevron-right ms-1"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center hover-top">
                    <div class="mb-3 text-primary">
                        <i class="fa-solid fa-cloud-arrow-up fa-3x"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Cloud Integration & DevOps</h4>
                    <p class="text-muted mb-3">Automated CI/CD pipelines, Kubernetes container orchestration, and multi-cloud infrastructure.</p>
                    <a href="{{ pathto('services') }}" class="fw-bold text-primary text-decoration-none">Learn More <i class="fa-solid fa-chevron-right ms-1"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center hover-top">
                    <div class="mb-3 text-primary">
                        <i class="fa-solid fa-brain fa-3x"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Enterprise AI & Analytics</h4>
                    <p class="text-muted mb-3">Leverage machine learning algorithms, predictive analytics, and automated decision engines.</p>
                    <a href="{{ pathto('services') }}" class="fw-bold text-primary text-decoration-none">Learn More <i class="fa-solid fa-chevron-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- "GET IN TOUCH" SECTION (3 Options) -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase small">Contact Us</span>
            <h2 class="fw-bold heading-font display-6">Get In Touch With Our Engineering Team</h2>
        </div>

        <?php if ($nConfig['get_in_touch_layout'] === 'option1') { ?>
            <!-- OPTION 1: Modern Card Box -->
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm p-4 text-center rounded-4 h-100">
                        <i class="fa-solid fa-location-dot fa-3x text-primary mb-3"></i>
                        <h5 class="fw-bold">Headquarters</h5>
                        <p class="text-muted mb-0">{{ $nConfig['contact_address'] }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm p-4 text-center rounded-4 h-100">
                        <i class="fa-solid fa-phone fa-3x text-primary mb-3"></i>
                        <h5 class="fw-bold">Direct Phone</h5>
                        <p class="text-muted mb-0">{{ $nConfig['contact_phone'] }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm p-4 text-center rounded-4 h-100">
                        <i class="fa-solid fa-envelope fa-3x text-primary mb-3"></i>
                        <h5 class="fw-bold">Email Support</h5>
                        <p class="text-muted mb-0">{{ $nConfig['contact_email'] }}</p>
                    </div>
                </div>
            </div>
        <?php } elseif ($nConfig['get_in_touch_layout'] === 'option2') { ?>
            <!-- OPTION 2: Side Form & Embedded Map -->
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="card border-0 shadow p-4 rounded-4">
                        <h4 class="fw-bold mb-3">Send Us a Message</h4>
                        <form action="{{ pathto('contact') }}" method="GET">
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Your Full Name">
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Your Email Address">
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="3" placeholder="Project Details..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold">Submit Request</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="rounded-4 overflow-hidden shadow">
                        <iframe src="{{ $nConfig['contact_map_url'] }}" width="100%" height="320" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <!-- OPTION 3: Split Dark/Light Panel -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="row g-0">
                    <div class="col-lg-6 bg-dark text-white p-5">
                        <h3 class="fw-bold mb-3">Let's Build Something Great</h3>
                        <p class="text-white-50 mb-4">Our dedicated technical consultants are available to help align your architecture goals.</p>
                        <p><i class="fa-solid fa-phone me-2 text-primary"></i>{{ $nConfig['contact_phone'] }}</p>
                        <p><i class="fa-solid fa-envelope me-2 text-primary"></i>{{ $nConfig['contact_email'] }}</p>
                    </div>
                    <div class="col-lg-6 p-5 bg-white">
                        <a href="{{ pathto('contact') }}" class="btn btn-primary btn-lg rounded-pill fw-bold">Schedule Consultation <i class="fa-solid fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<!--==============================
Blog Section (Aligned with /public_html/n-tech/index.html)
==============================-->
<div class="tv-blog-section py-5 bg-white">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase small">Tech Insights</span>
            <h2 class="fw-bold heading-font display-6">Latest Articles & News</h2>
        </div>

        <div class="row gy-4">
            <?php 
            $recentPosts = $recent_posts ?? [];
            if (!empty($recentPosts)) {
                $bCount = 0;
                foreach ($recentPosts as $p) {
                    if ($bCount++ >= 3) break;
                    $pTitle = $p['title'] ?? 'Untitled Article';
                    $pSlug = $p['slug'] ?? '';
                    $pDate = !empty($p['created_at']) ? date('d M, Y', strtotime((string)$p['created_at'])) : date('d M, Y');
                    $pImg = !empty($p['featured_image']) ? pathto($p['featured_image']) : pathto('Themes/n-tech/images/blog/blog0' . ($bCount) . '.webp');
            ?>
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <article class="blog-single-box h-100 shadow-sm border-0 rounded-4 overflow-hidden bg-light">
                            <div class="inner-box">
                                <div class="blog-image position-relative overflow-hidden" style="max-height: 220px;">
                                    <img src="{{ $pImg }}" alt="{{ htmlspecialchars($pTitle, ENT_QUOTES, 'UTF-8') }}" class="w-100 h-100 object-fit-cover">
                                    <div class="category-tag position-absolute top-0 end-0 bg-primary text-white px-3 py-1 m-3 rounded-pill small fw-semibold">
                                        <span></span>{{ $pDate }}
                                    </div>
                                </div>
                                <div class="blog-content p-4">
                                    <h4 class="title fw-bold fs-5 mb-3">
                                        <a href="{{ pathto($pSlug) }}" class="text-dark text-decoration-none">{{ htmlspecialchars($pTitle, ENT_QUOTES, 'UTF-8') }}</a>
                                    </h4>
                                    <div class="pt-2 pb-3"><div class="border-bottom border-secondary opacity-25"></div></div>
                                    <div class="blog-meta d-flex justify-content-between align-items-center pt-2">
                                        <a href="{{ pathto($pSlug) }}" class="continue-reading fw-bold text-primary text-decoration-none">
                                            Explore More <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </a>
                                        <span class="text-muted small">Article</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
            <?php 
                } 
            } else { 
            ?>
                <div class="col-12 text-center text-muted">
                    <p>No blog articles published yet.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

{{ $this->view('Themes/' . $theme_name . '/footer', $data) }}
