<?php
$theme_config = $theme_config ?? [];
$raw_css_config = $theme_config['custom_css'] ?? '';
$codies_config = [];
if (preg_match('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', $raw_css_config, $matches)) {
    $codies_config = json_decode($matches[1], true) ?: [];
}
$theme_config = array_merge($theme_config, $codies_config);
$archive_layout = $theme_config['archive_layout'] ?? 'grid';
$viewData = array_diff_key(get_defined_vars(), ['content' => 1, 'viewFile' => 1, 'view' => 1, 'data' => 1]);
?>
{{ $this->view('Themes/codies/header', $viewData) }}

<?php

$posts = $posts ?? [];
$archive_title = $archive_title ?? 'Archive';
$widget_categories = $widget_categories ?? [];
?>

<!-- Category Hero Banner with Parallax style if enabled -->
<section class="container mt-4 wow fadeIn" data-wow-duration="0.6s">
    <div class="category-hero p-4 p-md-5 text-white rounded-4 position-relative overflow-hidden mb-4 shadow" style="background: linear-gradient(145deg, var(--accent-color) 25%, var(--secondary-color) 100%);">
        <!-- Breadcrumb tracker -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom mb-3 small text-white-50">
                <li class="breadcrumb-item text-white-50"><a href="{{ pathto('') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item text-white-50"><a href="#" class="text-white-50 text-decoration-none">Blueprints</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">{{ $archive_title }}</li>
            </ol>
        </nav>

        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark fw-bold mb-2 text-uppercase">Subscribed Track</span>
                <h1 class="display-5 fw-bold mb-3" id="mainCategoryTitle">{{ $archive_title }}</h1>
                <p class="lead mb-3 text-white-50" id="mainCategoryDesc">Master deep visual styling guides, advanced UI reactive models, responsive layouts, and technical blueprints.</p>

                <!-- Key Statistics -->
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <div class="badge bg-white-50 text-white border p-2" style="background: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.2);"><i class="fa-regular fa-file-code me-1"></i> <span>{{ count($posts) }}</span> </div>
                    <div class="badge bg-white-50 text-white border p-2" style="background: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.2);"><i class="fa-regular fa-user me-1"></i> Active Track</div>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-end opacity-25">
                <i class="fa-solid fa-laptop-code fa-10x text-white"></i>
            </div>
        </div>
    </div>
</section>

<!-- Filter Chips & Toolbar Controls -->
<section class="container mb-4">
    <div class="card p-3">
        <div class="row align-items-center g-3">
            <!-- Sub-category filter tags -->
            <div class="col-md-7 col-12">
                <div class="filter-scroll-wrapper d-flex gap-2 overflow-x-auto pb-1" id="chipContainer" style="scrollbar-width: none;">
                    <div class="btn btn-sm btn-outline-primary rounded-pill px-3 active" data-sub="all" onclick="filterChips('all', this)">All Modules</div>
                    <?php foreach ($widget_categories as $cat) { ?>
                        <div class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-sub="{{ $cat['slug'] }}" onclick="filterChips('{{ $cat['slug'] }}', this)">{{ $cat['name'] }}</div>
                    <?php } ?>
                </div>
            </div>

            <!-- Controls Toolbar (Sort only - layout is set via Theme Customizer) -->
            <div class="col-md-5 col-12 d-flex justify-content-md-end justify-content-start align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small text-nowrap">Sort By</span>
                    <select class="form-select form-select-sm" id="sortSelect" onchange="sortCards()">
                        <option value="newest" selected>Latest</option>
                        <option value="views">Most Viewed</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Grid Section -->
<main class="container py-2">
    <div class="row g-4">
        <!-- Main Grid Area -->
        <div class="col-lg-9 col-12">
            <div class="row g-4 tutorial-grid" id="categoryGrid">
                <?php if (!empty($posts)) { ?>
                    <?php foreach ($posts as $post) {
                        $meta = (new \App\Models\PostModel())->getPostMeta((int)$post['id']);
                        $views = $post['views'] ?? 0;
                        $read_time = !empty($meta['read_time']) ? $meta['read_time'] : calculate_read_time($post['content'] ?? '');

                        // Parse taxonomy category slug for filtering
                        $post_cats = (new \App\Models\TaxonomyModel())->getPostTaxonomies((int)$post['id'], 'category');
                        $cat_slugs = [];
                        foreach ($post_cats as $pc) {
                            $cat_slugs[] = $pc['slug'];
                        }
                        $category_attr = implode(' ', $cat_slugs);

                        // Try parsing a code block inside post content for collapsible display
                        $code_block = '';
                        $code_lang = 'javascript';
                        if (preg_match('/<pre[^>]*><code[^>]*class="language-([^"]+)"[^>]*>(.*?)<\/code><\/pre>/is', $post['content'], $matches)) {
                            $code_lang = $matches[1];
                            $code_block = htmlspecialchars_decode($matches[2]);
                        } elseif (preg_match('/<pre[^>]*>(.*?)<\/pre>/is', $post['content'], $matches)) {
                            $code_block = strip_tags($matches[1]);
                        }

                        $author_name =  $author->username ?? '';

                    ?>
                        <div class="<?php echo $archive_layout === 'list' ? 'col-12' : 'col-md-6 col-12'; ?> tutorial-item-col" data-category="{{ $category_attr }}" data-views="{{ $views }}" data-date="{{ date('Y-m-d', strtotime(!empty($post['publish_date']) ? $post['publish_date'] : $post['created_at'])) }}">
                            <article class="card h-100 wow fadeInUp" data-wow-duration="0.6s" style="<?php echo $archive_layout === 'list' ? 'flex-direction: row;' : ''; ?>">
                                <img src="{{ !empty($meta['featured_image']) ? pathto($meta['featured_image']) : 'https://placehold.co/600x400/1e293b/38bdf8?text=' . urlencode($post['title']) }}" class="card-img-top card-img-side" style="display:block; object-fit: cover; <?php echo $archive_layout === 'list' ? 'width: 280px; min-width: 280px; height: 100%;' : 'height: 200px; width: 100%;'; ?>" alt="{{ htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') }}">
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
                                        <!-- Collapsible code block inside card -->
                                        <?php if (!empty($code_block)) { ?>
                                            <button class="btn btn-outline-secondary btn-sm w-100 mb-3 border-secondary-subtle" type="button" onclick="toggleCardCode(this)">
                                                <i class="fa-solid fa-code me-1"></i> View Code Snippet
                                            </button>
                                            <div class="collapse-code-wrapper d-none mb-3 border rounded overflow-hidden">
                                                <div class="bg-light px-3 py-1 text-muted border-bottom d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                                                    <span>{{ htmlspecialchars($code_lang, ENT_QUOTES, 'UTF-8') }} snippet</span>
                                                    <div class="d-flex gap-1">
                                                        <span class="badge bg-danger rounded-circle p-1" style="width:6px; height:6px;"></span>
                                                        <span class="badge bg-warning rounded-circle p-1" style="width:6px; height:6px;"></span>
                                                        <span class="badge bg-success rounded-circle p-1" style="width:6px; height:6px;"></span>
                                                    </div>
                                                </div>
                                                <pre class="m-0 p-2" style="font-size:0.8rem; background-color:#1e293b; color:#fff;"><code class="language-{{ htmlspecialchars($code_lang, ENT_QUOTES, 'UTF-8') }}">{{ htmlspecialchars($code_block, ENT_QUOTES, 'UTF-8') }}</code></pre>
                                            </div>
                                        <?php } ?>

                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light-subtle mb-3">
                                            <span class="text-muted small"><i class="fa-regular fa-clock me-1"></i> {{ htmlspecialchars($read_time, ENT_QUOTES, 'UTF-8') }}</span>
                                            <span class="text-muted small"><i class="fa-regular fa-eye me-1"></i> {{ htmlspecialchars((string)$views, ENT_QUOTES, 'UTF-8') }} views</span>
                                        </div>
                                        <div class="d-grid">
                                            <?php redirectto($post['slug'], 'Read more …', 'btn btn-outline-primary btn-sm rounded-pill'); ?>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="col-12 py-5 text-center bg-white rounded border">
                        <i class="fa-solid fa-laptop-code fa-3x text-muted mb-3"></i>
                        <h4>No Blueprints Under This Category</h4>
                        <p class="text-muted">Return to the dashboard to populate categories and publish guides.</p>
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

        <!-- Sidebar Area -->
        <div class="col-lg-3 col-12">
            {{ $this->view('Themes/codies/sidebar', $viewData) }}
        </div>
    </div>
</main>

<script>
    let selectedCategoryChip = 'all';

    // Collapsible Card Code preview toggle
    function toggleCardCode(button) {
        const wrapper = button.nextElementSibling;
        if (wrapper.classList.contains('d-none')) {
            wrapper.classList.remove('d-none');
            button.innerHTML = '<i class="fa-solid fa-chevron-up me-1"></i> Hide Code Snippet';
        } else {
            wrapper.classList.add('d-none');
            button.innerHTML = '<i class="fa-solid fa-code me-1"></i> View Code Snippet';
        }
    }

    // Filter Chips click logic
    function filterChips(slug, element) {
        selectedCategoryChip = slug;

        // Remove active class from other buttons
        const container = document.getElementById('chipContainer');
        container.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active', 'btn-primary'));
        container.querySelectorAll('.btn').forEach(btn => btn.classList.add('btn-outline-secondary'));

        element.classList.remove('btn-outline-secondary');
        element.classList.add('active', 'btn-primary');

        applyFiltersAndSearch();
        triggerToast(`Category filter applied: [${slug.toUpperCase()}]`);
    }

    // Combined filter search execution
    function applyFiltersAndSearch() {
        const query = document.getElementById('sidebarSearch').value.toLowerCase();
        let visibleCount = 0;
        const items = document.querySelectorAll('.tutorial-item-col');

        items.forEach(col => {
            const categories = (col.getAttribute('data-category') || '').split(' ');
            const title = col.querySelector('.card-title a').textContent.toLowerCase();
            const text = col.querySelector('.card-text').textContent.toLowerCase();

            const matchesCategory = (selectedCategoryChip === 'all' || categories.includes(selectedCategoryChip));
            const matchesQuery = (title.includes(query) || text.includes(query));

            if (matchesCategory && matchesQuery) {
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

    // Sidebar search key handler
    function filterBlogList() {
        applyFiltersAndSearch();
    }

    function resetFilters() {
        document.getElementById('sidebarSearch').value = '';
        const container = document.getElementById('chipContainer');
        const firstChip = container.querySelector('[data-sub="all"]');
        if (firstChip) {
            filterChips('all', firstChip);
        } else {
            applyFiltersAndSearch();
        }
    }

    // Sorting logic
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

        cards.forEach(card => grid.appendChild(card));
        triggerToast(`Sorted category items by: ${criteria.toUpperCase()}`);
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