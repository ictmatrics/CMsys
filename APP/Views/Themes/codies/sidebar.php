<?php
$theme_config = $theme_config ?? [];
$raw_css_config = $theme_config['custom_css'] ?? '';
$codies_config = [];
if (preg_match('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', $raw_css_config, $matches)) {
    $codies_config = json_decode($matches[1], true) ?: [];
}
$theme_config = array_merge($theme_config, $codies_config);

$widget_settings = $widget_settings ?? ['sidebar_widgets' => []];
$activeWidgets = $widget_settings['sidebar_widgets'] ?? [];
$widget_recent_posts = $widget_recent_posts ?? [];
$widget_categories = $widget_categories ?? [];
$widget_tags = $widget_tags ?? [];
$widget_pages = $widget_pages ?? [];
$blogPath = 'blog';
?>

<?php foreach ($activeWidgets as $widgetKey) { ?>
    <?php if ($widgetKey === 'search') { ?>
        <!-- Widget: Search -->
        <div class="sidebar-widget bg-white shadow-sm mb-4 p-4 rounded-3 border">
            <h5 class="widget-title mb-3 fw-bold pb-2 border-bottom text-uppercase" style="font-size: 1rem; border-left: 4px solid var(--accent-color); padding-left: 10px; letter-spacing: 0.5px;">
                Engine Search
            </h5>
            <form action="{{ pathto($blogPath) }}" method="GET" onsubmit="if (typeof filterBlogList === 'function') { event.preventDefault(); filterBlogList(); return false; }">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted" id="search-addon-sidebar">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="s" id="sidebarSearch" class="form-control border-start-0" placeholder="Type key term to search..." aria-describedby="search-addon-sidebar" value="{{ htmlspecialchars($_GET['s'] ?? '', ENT_QUOTES, 'UTF-8') }}" oninput="if (typeof filterBlogList === 'function') { filterBlogList(); }">
                </div>
            </form>
            <small class="text-muted d-block mt-2">Filter results instantaneously by title or tag terms.</small>
        </div>
    <?php } elseif ($widgetKey === 'recent_posts') { ?>
        <!-- Widget: Recent Posts -->
        <div class="sidebar-widget bg-white shadow-sm mb-4 p-4 rounded-3 border">
            <h5 class="widget-title mb-3 fw-bold pb-2 border-bottom text-uppercase" style="font-size: 1rem; border-left: 4px solid var(--accent-color); padding-left: 10px; letter-spacing: 0.5px;">
                Recent Posts
            </h5>
            <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                <?php if (empty($widget_recent_posts)) { ?>
                    <li class="small text-muted">No recent posts.</li>
                <?php } else { ?>
                    <?php foreach ($widget_recent_posts as $post) { ?>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-file-lines text-primary mt-1" style="font-size: 0.85rem;"></i>
                            <div>
                                <?php redirectto($post['slug'], $post['title'], 'text-decoration-none fw-semibold text-dark small'); ?>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ date('M d, Y', strtotime(!empty($post['publish_date']) ? $post['publish_date'] : $post['created_at'])) }}</small>
                            </div>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
    <?php } elseif ($widgetKey === 'trending_posts') { ?>
        <!-- Widget: Trending Posts -->
        <div class="sidebar-widget bg-white shadow-sm mb-4 p-4 rounded-3 border">
            <h5 class="widget-title mb-3 fw-bold pb-2 border-bottom text-uppercase" style="font-size: 1rem; border-left: 4px solid var(--accent-color); padding-left: 10px; letter-spacing: 0.5px;">
                <i class="fa-solid fa-fire text-warning me-2"></i>Trending Posts
            </h5>
            <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                <?php if (empty($widget_trending_posts)) { ?>
                    <li class="small text-muted">No trending posts.</li>
                <?php } else { ?>
                    <?php foreach ($widget_trending_posts as $post) { ?>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-bolt text-warning mt-1" style="font-size: 0.85rem;"></i>
                            <div>
                                <?php redirectto(is_object($post) ? $post->slug : $post['slug'], is_object($post) ? $post->title : $post['title'], 'text-decoration-none fw-semibold text-dark small'); ?>
                                <small class="text-muted d-block" style="font-size: 0.75rem;"><i class="fa-regular fa-eye me-1"></i>{{ htmlspecialchars((string)(is_object($post) ? ($post->views ?? 0) : ($post['views'] ?? 0)), ENT_QUOTES, 'UTF-8') }} views</small>
                            </div>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
    <?php } elseif ($widgetKey === 'categories') { ?>
        <!-- Widget: Categories -->
        <div class="sidebar-widget bg-white shadow-sm mb-4 p-4 rounded-3 border">
            <h5 class="widget-title mb-3 fw-bold pb-2 border-bottom text-uppercase" style="font-size: 1rem; border-left: 4px solid var(--accent-color); padding-left: 10px; letter-spacing: 0.5px;">
                Categories
            </h5>
            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                <?php if (empty($widget_categories)) { ?>
                    <li class="small text-muted">No categories.</li>
                <?php } else { ?>
                    <?php foreach ($widget_categories as $cat) { ?>
                        <li class="d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fa-solid fa-folder me-2 text-primary"></i>
                                <?php redirectto('category/' . $cat['slug'], $cat['name'], 'text-decoration-none small text-dark'); ?>
                            </span>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
    <?php } elseif ($widgetKey === 'tags') { ?>
        <!-- Widget: Tags -->
        <div class="sidebar-widget bg-white shadow-sm mb-4 p-4 rounded-3 border">
            <h5 class="widget-title mb-3 fw-bold pb-2 border-bottom text-uppercase" style="font-size: 1rem; border-left: 4px solid var(--accent-color); padding-left: 10px; letter-spacing: 0.5px;">
                Tags
            </h5>
            <div class="d-flex flex-wrap gap-2">
                <?php if (empty($widget_tags)) { ?>
                    <span class="small text-muted">No tags.</span>
                <?php } else { ?>
                    <?php foreach ($widget_tags as $tag) { ?>
                        <?php redirectto('tag/' . $tag['slug'], $tag['name'], 'btn btn-sm btn-light border text-decoration-none small mb-2 me-2'); ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    <?php } elseif ($widgetKey === 'pages') { ?>
        <!-- Widget: Pages -->
        <div class="sidebar-widget bg-white shadow-sm mb-4 p-4 rounded-3 border">
            <h5 class="widget-title mb-3 fw-bold pb-2 border-bottom text-uppercase" style="font-size: 1rem; border-left: 4px solid var(--accent-color); padding-left: 10px; letter-spacing: 0.5px;">
                Pages
            </h5>
            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                <?php if (empty($widget_pages)) { ?>
                    <li class="small text-muted">No pages.</li>
                <?php } else { ?>
                    <?php foreach ($widget_pages as $page) { ?>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="fa-solid fa-circle-chevron-right text-success" style="font-size: 0.85rem;"></i>
                            <?php redirectto($page['slug'], $page['title'], 'text-decoration-none small text-dark'); ?>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
<?php } ?>

<!-- Widget: Sidebar Square Ad Space -->
<?php if (!empty($theme_config['ad_sidebar_enable'])) { ?>
    <div class="sidebar-widget bg-white shadow-sm mb-4 p-4 rounded-3 border text-center">
        <h5 class="widget-title mb-3 fw-bold pb-2 border-bottom text-uppercase" style="font-size: 1rem; border-left: 4px solid var(--accent-color); padding-left: 10px; letter-spacing: 0.5px;">
            Sponsor
        </h5>
        <div class="ad-square mx-auto p-1 bg-light border rounded position-relative" style="width: 100%; max-width: 300px; min-height: 250px; display: flex; align-items: center; justify-content: center;">
            <span class="ad-tag text-muted">Sponsor</span>
            <?php if (!empty($theme_config['ad_sidebar_html'])) { ?>
                {{ htmlspecialchars_decode($theme_config['ad_sidebar_html']) }}
            <?php } else { ?>
                <a href="{{ htmlspecialchars($theme_config['ad_sidebar_link'] ?? '#', ENT_QUOTES, 'UTF-8') }}" target="_blank">
                    <?php if (!empty($theme_config['ad_sidebar_img'])) { ?>
                        <img src="{{ pathto($theme_config['ad_sidebar_img']) }}" alt="{{ htmlspecialchars($theme_config['ad_sidebar_alt'] ?? 'Ad', ENT_QUOTES, 'UTF-8') }}" class="img-fluid rounded" style="object-fit: cover;">
                    <?php } else { ?>
                        <div class="p-3">
                            <i class="fa-solid fa-code fa-2x text-primary mb-2"></i>
                            <h6 class="fw-bold mb-1">Developer Jobs</h6>
                            <p class="small text-muted mb-0">Looking for a Senior PHP Role? Click to view open opportunities.</p>
                        </div>
                    <?php } ?>
                </a>
            <?php } ?>
        </div>
    </div>
<?php } ?>

<!-- Widget: Engineer Praise (Testimonials Slider) -->
<div class="sidebar-widget bg-white shadow-sm mb-4 p-4 rounded-3 border">
    <h5 class="widget-title mb-3 fw-bold pb-2 border-bottom text-uppercase" style="font-size: 1rem; border-left: 4px solid var(--accent-color); padding-left: 10px; letter-spacing: 0.5px;">
        Engineer Praise
    </h5>
    
    <div id="sidebarTestimonialCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="text-center py-2">
                    <p class="mb-3 small italic" style="font-style: italic;">"Codies' deep dive into React Suspense transitions saved our engineering squad weeks of asynchronous integration tests. Extremely high fidelity material."</p>
                    <p class="mb-0 fw-bold small text-dark">— Sarah Jenkins</p>
                    <span class="text-muted small" style="font-size: 0.75rem;">Senior Director of UX, Stripe</span>
                </div>
            </div>
            <div class="carousel-item">
                <div class="text-center py-2">
                    <p class="mb-3 small italic" style="font-style: italic;">"Having access to actual syntax-highlighted snippets showing line-by-line validation structures made teaching team onboarding immensely productive."</p>
                    <p class="mb-0 fw-bold small text-dark">— Dr. Alan Vance</p>
                    <span class="text-muted small" style="font-size: 0.75rem;">Lead Educator, TechAcademy</span>
                </div>
            </div>
            <div class="carousel-item">
                <div class="text-center py-2">
                    <p class="mb-3 small italic" style="font-style: italic;">"The modern templates represent actual high-performance paradigms instead of standard entry level constructs. High marks on system designs."</p>
                    <p class="mb-0 fw-bold small text-dark">— Robert S.</p>
                    <span class="text-muted small" style="font-size: 0.75rem;">DevOps Engineer, Cloudflare</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Widget: Newsletter Sign-up -->
<div class="sidebar-widget bg-white shadow-sm p-4 rounded-3 border text-center" style="background-color: rgba(var(--accent-rgb), 0.03) !important; border-color: rgba(var(--accent-rgb), 0.15) !important;">
    <i class="fa-regular fa-paper-plane text-primary fa-2x mb-3 animate__animated animate__swing animate__infinite"></i>
    <h6 class="fw-bold mb-2">Subscribe to {{ SITENAME }}</h6>
    <p class="text-muted small mb-3">Join 5,000+ developers receiving monthly high-quality scripts straight to their inboxes.</p>
    <div class="input-group">
        <input type="email" class="form-control form-control-sm" placeholder="Enter dev email..." id="newsletterEmail">
        <button class="btn btn-primary btn-sm" onclick="subscribeNewsletter()">Join</button>
    </div>
</div>
