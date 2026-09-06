<?php
$theme_config = $theme_config ?? [];
$raw_css_config = $theme_config['custom_css'] ?? '';
$codies_config = [];
if (preg_match('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', $raw_css_config, $matches)) {
    $codies_config = json_decode($matches[1], true) ?: [];
}
$theme_config = array_merge($theme_config, $codies_config);
$home_layout = $theme_config['home_layout'] ?? 'grid';
$viewData = array_diff_key(get_defined_vars(), ['content' => 1, 'viewFile' => 1, 'view' => 1, 'data' => 1]);
?>
{{ $this->view('Themes/codies/header', $viewData) }}

<?php

$posts = $posts ?? [];
$widget_recent_posts = $widget_recent_posts ?? [];

// 1. Resolve Carousel Slides
$carousel_mode = $theme_config['carousel_mode'] ?? 'recent';
$slides = [];

if ($carousel_mode === 'custom') {
    // Custom customizer slides
    for ($i = 1; $i <= 3; $i++) {
        $img = $theme_config["slide{$i}_img"] ?? '';
        $title_slide = $theme_config["slide{$i}_title"] ?? '';
        if (!empty($title_slide)) {
            $link = $theme_config["slide{$i}_link"] ?? '#';
            $badge = $theme_config["slide{$i}_badge"] ?? 'Featured';
            
            $postModel = new \App\Models\PostModel();
            $taxonomyModel = new \App\Models\TaxonomyModel();
            $matchedPost = $postModel->find_single('posts', null, "WHERE title = ? AND status = 'published'", [$title_slide]);
            if (!$matchedPost && !empty($link) && $link !== '#') {
                $slug = basename(parse_url($link, PHP_URL_PATH) ?: '');
                if (!empty($slug)) {
                    $matchedPost = $postModel->getPostBySlug($slug, 'post');
                }
            }
            if ($matchedPost) {
                $post_cats = $taxonomyModel->getPostTaxonomies((int)$matchedPost->id, 'category');
                if (!empty($post_cats)) {
                    $badge = $post_cats[0]['name'];
                }
            }
            
            $slides[] = (object)[
                'image' => !empty($img) ? pathto($img) : 'https://placehold.co/1200x500/1e293b/38bdf8?text=Slide+' . $i,
                'title' => $title_slide,
                'badge' => $badge,
                'lead' => $theme_config["slide{$i}_lead"] ?? '',
                'link' => $link,
                'read_time' => $theme_config["slide{$i}_readtime"] ?? '5 min read'
            ];
        }
    }
} else {
    // Fetch posts from database
    $feed_posts = $posts;
    if ($carousel_mode === 'category') {
        $cat_id = (int)($theme_config['carousel_category'] ?? 0);
        if ($cat_id > 0) {
            $feed_posts = (new \App\Models\PostModel())->getPostsByTaxonomy($cat_id);
        }
    }
    
    $carousel_source = !empty($feed_posts) ? $feed_posts : $widget_recent_posts;
    $slice_posts = array_slice($carousel_source, 0, 3);
    
    $idx = 1;
    foreach ($slice_posts as $p) {
        $meta = (new \App\Models\PostModel())->getPostMeta((int)$p['id']);
        $read_time = !empty($meta['read_time']) ? $meta['read_time'] : calculate_read_time($p['content'] ?? '');
        $post_cats = (new \App\Models\TaxonomyModel())->getPostTaxonomies((int)$p['id'], 'category');
        $badge = !empty($post_cats) ? $post_cats[0]['name'] : 'Tutorial';
        
        $slides[] = (object)[
            'image' => !empty($meta['featured_image']) ? pathto($meta['featured_image']) : 'https://placehold.co/1200x500/1e293b/38bdf8?text=' . urlencode($p['title']),
            'title' => $p['title'],
            'badge' => $badge,
            'lead' => !empty($p['excerpt']) ? $p['excerpt'] : substr(strip_tags($p['content']), 0, 120) . '...',
            'link' => pathto($p['slug']),
            'read_time' => $read_time
        ];
        $idx++;
    }
}

// 2. Resolve Stack Panel (Right 25% Columns)
$stack_items = [];
$stack_source = array_slice($widget_recent_posts, 0, 3);
foreach ($stack_source as $p) {
    $meta = (new \App\Models\PostModel())->getPostMeta((int)$p['id']);
    $badge = 'Recent';
    $stack_items[] = (object)[
        'image' => !empty($meta['featured_image']) ? pathto($meta['featured_image']) : 'https://placehold.co/400x300/1e293b/38bdf8?text=' . urlencode($p['title']),
        'title' => $p['title'],
        'badge' => $badge,
        'link' => pathto($p['slug'])
    ];
}

// 3. Resolve Featured Post (Featured Post highlight system)
$featured_post = null;
$featured_post_id = (int)($theme_config['featured_post_id'] ?? 0);
if ($featured_post_id > 0) {
    $featured_post = (new \App\Models\PostModel())->getPostById($featured_post_id);
}
?>

<!-- Parallax Hero Container -->
<section class="container hero-container wow fadeIn" data-wow-duration="0.8s" style="<?php if (!empty($theme_config['parallax_hero'])) { echo 'background-attachment: fixed;'; } ?>">
    <div class="row g-4">
        <!-- Left Carousel Panel (75% Width) -->
        <div class="col-lg-9 col-12">
            <?php if (!empty($slides)) { ?>
                <div id="heroCarousel" class="carousel slide carousel-fixed-height" data-bs-ride="carousel" style="height: 500px; border-radius: 12px; overflow: hidden; position: relative;">
                    <!-- Indicators -->
                    <div class="carousel-indicators" style="z-index: 3;">
                        <?php foreach ($slides as $index => $slide) { ?>
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                        <?php } ?>
                    </div>
                    
                    <div class="carousel-inner h-100">
                        <?php foreach ($slides as $index => $slide) { ?>
                            <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                                <div class="carousel-image-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.85) 100%); z-index: 1;"></div>
                                <img src="{{ $slide->image }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ htmlspecialchars($slide->title, ENT_QUOTES, 'UTF-8') }}">
                                <div class="carousel-caption-custom text-start" style="position: absolute; bottom: 0; left: 0; right: 0; z-index: 2; padding: 3rem 2.5rem; color: #ffffff;">
                                    <span class="badge bg-primary mb-2">{{ htmlspecialchars($slide->badge, ENT_QUOTES, 'UTF-8') }}</span>
                                    <h1 class="display-6 fw-bold mb-3"><a href="{{ $slide->link }}" class="text-white">{{ htmlspecialchars($slide->title, ENT_QUOTES, 'UTF-8') }}</a></h1>
                                    <div class="d-flex align-items-center gap-2 mt-3">
                                        <span class="small"><i class="fa-regular fa-clock me-1"></i> {{ htmlspecialchars($slide->read_time, ENT_QUOTES, 'UTF-8') }}</span>
                                        <span class="mx-2">•</span>
                                        <span class="small"><i class="fa-solid fa-fire text-warning me-1"></i> Trending</span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <!-- Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" style="z-index: 3;">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" style="z-index: 3;">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            <?php } else { ?>
                <div class="carousel-fixed-height bg-dark text-white d-flex align-items-center justify-content-center rounded-3" style="height: 500px;">
                    <div class="text-center">
                        <i class="fa-solid fa-image fa-3x mb-3 text-muted"></i>
                        <h4>No Slides Available</h4>
                        <p class="text-muted">Configure the Carousel slides inside the Theme Customizer settings.</p>
                    </div>
                </div>
            <?php } ?>
        </div>
        
        <!-- Right Stack Panel (25% Width) -->
        <div class="col-lg-3 col-12">
            <div class="hero-stack-container d-flex flex-column justify-content-between" style="height: 500px;">
                <?php if (!empty($stack_items)) { ?>
                    <?php foreach ($stack_items as $item) { ?>
                        <div class="stack-item position-relative overflow-hidden cursor-pointer" onclick="location.href='{{ $item->link }}'" style="height: calc(33.333% - 10px); border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); cursor: pointer;">
                            <div class="carousel-image-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.9) 100%); z-index: 2;"></div>
                            <img src="{{ $item->image }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;" alt="{{ htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') }}">
                            <div class="stack-item-caption position-absolute" style="bottom: 0; left: 0; right: 0; padding: 1rem; color: #ffffff; z-index: 3;">
                                <span class="badge bg-danger mb-1" style="font-size: 0.65rem;">{{ htmlspecialchars($item->badge, ENT_QUOTES, 'UTF-8') }}</span>
                                <h6 class="mb-0 text-white text-truncate">{{ htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') }}</h6>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="h-100 bg-light border d-flex align-items-center justify-content-center rounded-3">
                        <span class="text-muted small">No stack items.</span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- FEATURED  SHOWCASE SYSTEM        -->
<!-- ========================================== -->
<?php if ($featured_post) { 
    $meta = (new \App\Models\PostModel())->getPostMeta((int)$featured_post->id);
    $read_time = !empty($meta['read_time']) ? $meta['read_time'] : calculate_read_time($featured_post->content ?? '');
    $views = $featured_post->views ?? $meta['views'] ?? ((($featured_post->id * 17) % 500) + 120);
    ?>
    <section class="container my-5">
        <div class="card bg-dark text-white overflow-hidden shadow border-0 rounded-4 wow fadeInUp" data-wow-duration="0.8s">
            <div class="row g-0">
                <div class="col-md-5">
                    <img src="{{ !empty($meta['featured_image']) ? pathto($meta['featured_image']) : 'https://placehold.co/600x400/1e293b/38bdf8?text=Featured' }}" class="img-fluid w-100 h-100" style="object-fit: cover; min-height: 250px;" alt="{{ htmlspecialchars($featured_post->title, ENT_QUOTES, 'UTF-8') }}">
                </div>
                <div class="col-md-7 d-flex align-items-center">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge bg-warning text-dark fw-bold mb-3 text-uppercase"><i class="fa-solid fa-star me-1"></i> Featured</span>
                        <h2 class="fw-bold mb-3"><a href="{{ pathto($featured_post->slug) }}" class="text-white text-decoration-none">{{ htmlspecialchars($featured_post->title, ENT_QUOTES, 'UTF-8') }}</a></h2>
                        <p class="text-muted mb-4 small">
                            {{ !empty($featured_post->excerpt) ? $featured_post->excerpt : substr(strip_tags($featured_post->content), 0, 200) . '...' }}
                        </p>
                        <div class="d-flex align-items-center gap-3 text-muted small mb-4">
                            <span><i class="fa-regular fa-clock me-1"></i> {{ htmlspecialchars($read_time, ENT_QUOTES, 'UTF-8') }}</span>
                            <span>•</span>
                            <span><i class="fa-regular fa-eye me-1"></i> {{ htmlspecialchars((string)$views, ENT_QUOTES, 'UTF-8') }} views</span>
                        </div>
                        <?php redirectto($featured_post->slug, 'Read more …', 'btn btn-primary px-4 py-2 fw-semibold rounded-pill'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<!-- Main content area: Loop & Sidebar -->
<main class="container py-4">
    <div class="row g-4">
        <!-- Left Main Column:  List -->
        <div class="col-lg-9 col-12" id="blogContainer">
            <div class="d-flex justify-content-between align-items-center mb-4">
               <h3></h3>
                
                <!-- Controls toolbar -->
                <div class="d-flex align-items-center gap-3">
                    <div class="d-none d-sm-flex align-items-center gap-2">
                        <span class="text-muted small text-nowrap">Sort By</span>
                        <select class="form-select form-select-sm" id="sortSelect" onchange="sortCards()">
                            <option value="newest" selected>Latest</option>
                            <option value="views">Most Viewed</option>
                        </select>
                    </div>
                </div>
            </div>

            <!--  loop container -->
            <div class="row g-4" id="categoryGrid">
                <?php if (!empty($posts)) { ?>
                    <?php foreach ($posts as $post) { 
                        $meta = (new \App\Models\PostModel())->getPostMeta((int)$post['id']);
                        $views = $post['views'] ?? $meta['views'] ?? ((($post['id'] * 17) % 500) + 120);
                        $read_time = !empty($meta['read_time']) ? $meta['read_time'] : calculate_read_time($post['content'] ?? '');
                        
                        // Author meta fallback
                      $author_name = $author->username ?? '';
                        ?>
                        <div class="<?php echo $home_layout === 'list' ? 'col-12' : 'col-md-6 col-12'; ?> tutorial-item-col" data-views="{{ $views }}" data-date="{{ date('Y-m-d', strtotime(!empty($post['publish_date']) ? $post['publish_date'] : $post['created_at'])) }}">
                            <article class="card h-100 wow fadeInUp" data-wow-duration="0.6s" style="<?php echo $home_layout === 'list' ? 'flex-direction: row;' : ''; ?>">
                                <img src="{{ !empty($meta['featured_image']) ? pathto($meta['featured_image']) : 'https://placehold.co/600x400/1e293b/38bdf8?text=' . urlencode($post['title']) }}" class="card-img-top card-img-side" style="display:block; object-fit: cover; <?php echo $home_layout === 'list' ? 'width: 280px; min-width: 280px; height: 100%;' : 'height: 200px; width: 100%;'; ?>" alt="{{ htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') }}">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>

                                        
                                        <h4 class="card-title fw-bold mb-3">
                                            <a href="{{ pathto($post['slug']) }}" class="text-inherit text-dark">{{ htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') }}</a>
                                        </h4>
                                        
                                        
                                        <p class="card-text text-muted small mb-4">
                                            {{ !empty($post['excerpt']) ? $post['excerpt'] : substr(strip_tags($post['content']), 0, 150) . '...' }}
                                        </p>
                                    </div>

                                    <div>
                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light-subtle">
                                            <span class="text-muted small"><i class="fa-regular fa-clock me-1"></i> {{ htmlspecialchars($read_time, ENT_QUOTES, 'UTF-8') }}</span>
                                            <span class="text-muted small"><i class="fa-regular fa-eye me-1"></i> {{ htmlspecialchars((string)$views, ENT_QUOTES, 'UTF-8') }} views</span>
                                        </div>
                                        <div class="d-grid mt-3">
                                            <?php redirectto($post['slug'], 'Read more …', 'btn btn-outline-primary btn-sm rounded-pill'); ?>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="col-12 py-5 text-center">
                        <i class="fa-solid fa-face-frown fa-3x text-muted mb-3"></i>
                        <h4>No  Published Yet</h4>
                        <p class="text-muted">Start writing and publishing content from the administrator dashboard area.</p>
                    </div>
                <?php } ?>
            </div>
            
            <!-- Dynamic No Results Message -->
            <div id="noResults" class="text-center py-5" style="display: none;">
                <i class="fa-solid fa-laptop-code fa-3x text-muted mb-3"></i>
                <h4>No Match Your Filter</h4>
                <p class="text-muted">Try selecting another topic in the navigation menu or searching for another keyword.</p>
                <button class="btn btn-primary btn-sm rounded-pill mt-2" onclick="resetFilters()">Reset Filter Workspace</button>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="col-lg-3 col-12">
            {{ $this->view('Themes/codies/sidebar', $viewData) }}
        </div>
    </div>
</main>

<script>
    // Client-side instant filter sorting logic
    function sortCards() {
        const select = document.getElementById('sortSelect');
        const criteria = select.value;
        const grid = document.getElementById('categoryGrid');
        const cards = Array.from(grid.children);

        cards.sort((a, b) => {
            if (criteria === 'newest') {
                const dateA = new Date(a.getAttribute('data-date'));
                const dateB = new Date(b.getAttribute('data-date'));
                return dateB - dateA;
            } else if (criteria === 'views') {
                const viewsA = parseInt(a.getAttribute('data-views'), 10);
                const viewsB = parseInt(b.getAttribute('data-views'), 10);
                return viewsB - viewsA;
            }
            return 0;
        });

        // Re-append sorted cards
        cards.forEach(card => grid.appendChild(card));
        triggerToast(`Feed items sorted by: ${criteria.toUpperCase()}`);
    }

    // Client-side sidebar instant filter integration
    function filterBlogList() {
        const query = document.getElementById('sidebarSearch').value.toLowerCase();
        let visibleCount = 0;
        const items = document.querySelectorAll('.tutorial-item-col');

        items.forEach(col => {
            const title = col.querySelector('.card-title a').textContent.toLowerCase();
            const text = col.querySelector('.card-text').textContent.toLowerCase();
            const badgeEl = col.querySelector('.badge');
            const badge = badgeEl ? badgeEl.textContent.toLowerCase() : '';
            
            if (title.includes(query) || text.includes(query) || (badge && badge.includes(query))) {
                col.style.display = 'block';
                visibleCount++;
            } else {
                col.style.display = 'none';
            }
        });

        const noRes = document.getElementById('noResults');
        if (visibleCount === 0) {
            noRes.style.display = 'block';
        } else {
            noRes.style.display = 'none';
        }
    }

    function resetFilters() {
        document.getElementById('sidebarSearch').value = '';
        filterBlogList();
    }

    // Trigger initial filter on page load if search query exists
    document.addEventListener("DOMContentLoaded", function() {
        const query = document.getElementById('sidebarSearch');
        if (query && query.value) {
            filterBlogList();
        }
    });
</script>

{{ $this->view('Themes/codies/footer', $viewData) }}
