<?php
$displayTitle = $title ?? 'N-Tech';
$nConfig = array_merge([
    'show_breadcrumbs' => '1',
], $theme_config ?? []);

if ($nConfig['show_breadcrumbs'] === '1') {
?>
<section class="tv-breadcrumb-section container my-4">
     <div class="tv-breadcrumb-inner mx-30 ml-mx-0 position-relative overflow-hidden br-30 ml-br-0">
        
        <!-- Background Image & Overlay -->
        <div class="bg-image position-absolute w-100 h-100 top-0 start-0 opacity-80" style="z-index: 0;">
            <img src="{{ pathto('Themes/n-tech/images/bg-img/breadcrumb.webp') }}" alt="{{ htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8') }}" class="w-100 h-100 object-fit-cover">
        </div>

        <!-- Floating Decorative Shapes (Positioned matching exact layout) -->
        <div class="shapes-container position-absolute w-100 h-100 top-0 start-0 pointer-events-none" style="z-index: 1;">
            <!-- Top Left Dot -->
            <img src="{{ pathto('Themes/n-tech/images/shapes/circle.webp') }}" alt="dot" class="position-absolute" style="top: 25%; left: 7%; width: 8px; filter: brightness(1.5);">
            
            <!-- Upper Left Wavy Squiggle -->
            <img src="{{ pathto('Themes/n-tech/images/shapes/snake.webp') }}" alt="wavy" class="position-absolute" style="top: 28%; left: 30%; width: 42px; opacity: 0.95;">
            
            <!-- Lower Left Star Flower -->
            <img src="{{ pathto('Themes/n-tech/images/shapes/star.webp') }}" alt="star" class="position-absolute spin2" style="bottom: 22%; left: 31%; width: 26px; opacity: 0.85;">
            
            <!-- Upper Right Outline Circle -->
            <img src="{{ pathto('Themes/n-tech/images/shapes/circle.webp') }}" alt="circle" class="position-absolute" style="top: 22%; right: 33%; width: 18px; opacity: 0.85;">
            
            <!-- Lower Right 6-Dot Grid -->
            <img src="{{ pathto('Themes/n-tech/images/shapes/doot.webp') }}" alt="grid-dots" class="position-absolute jump3" style="bottom: 22%; right: 34%; width: 28px; opacity: 0.85;">
        </div>

        <!-- Main Center Content -->
        <div class="container position-relative py-3 text-center" style="z-index: 2;">
            <h1 class="display-4 fw-bold text-white mb-3 heading-font">{{ htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8') }}</h1>
            
            <!-- Glassmorphic Pill Breadcrumb Container -->
            <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill border border-white border-opacity-25 text-white small shadow-sm" style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                <a href="{{ pathto('') }}" class="text-white text-decoration-none d-inline-flex align-items-center me-1 fw-semibold">
                    <i class="fa-solid fa-house-chimney me-2" style="font-size: 13px;"></i>Home
                </a>
                <span class="text-white-50">/</span>
                <span class="text-white-50 fw-semibold">{{ htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8') }}</span>
            </div>
        </div>
    </div>
</section>
<?php } ?>



