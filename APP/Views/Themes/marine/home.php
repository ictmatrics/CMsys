{{ $this->view('Themes/marine/header', $data) }}

<!-- Hero Banner -->
<section class="hero-banner text-center text-md-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 font-weight-bold animate__animated animate__fadeInLeft">{{ $site_title }}</h1>
                <p class="lead text-light animate__animated animate__fadeInLeft" style="animation-delay: 0.1s;">{{ $site_description }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Content Main Area -->
<div class="container">
    <div class="row">
        <?php 
        $layout = $widget_settings['sidebar_layout'] ?? 'right';
        $mainCols = $layout === 'none' ? 'col-lg-12' : 'col-lg-8';
        
        $searchQuery = validate_data($_GET['s'] ?? '');
        $filteredPosts = $posts;
        if (!empty($searchQuery)) {
            $filteredPosts = array_filter($posts, function($p) use ($searchQuery) {
                return (str_contains(strtolower($p['title']), strtolower($searchQuery)) || str_contains(strtolower($p['content']), strtolower($searchQuery)));
            });
        }
        ?>

        <!-- Left Sidebar layout check -->
        <?php if ($layout === 'left') { ?>
            <div class="col-lg-4 mb-4">
                {{ $this->view('Themes/marine/sidebar', $data) }}
            </div>
        <?php } ?>

        <!-- Main Loop -->
        <div class="{{ $mainCols }}">
            <?php if (!empty($searchQuery)) { ?>
                <h4 class="mb-4 text-dark">Search Results for: <code>{{ htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') }}</code></h4>
            <?php } ?>

            <?php if (empty($filteredPosts)) { ?>
                <div class="card p-5 text-center text-muted border-0 shadow-sm rounded-4">
                    <i class="fa-solid fa-file-excel fa-4x mb-3 text-secondary"></i>
                    <h4>No posts found.</h4>
                    <p class="mb-0">Try checking back later or searching for a different keyword.</p>
                </div>
            <?php } else { ?>
                <?php foreach ($filteredPosts as $post) { 
                    $meta = (new \App\Models\PostModel())->getPostMeta((int)$post['id']);
                    $img = isset($meta['featured_image']) && !empty($meta['featured_image']) ? pathto($meta['featured_image']) : '';
                    ?>
                    <article class="card card-post overflow-hidden border-0 bg-white">
                        <div class="row g-0">
                            <?php if (!empty($img)) { ?>
                                <div class="col-md-4">
                                    <img src="{{ $img }}" class="img-fluid h-100 w-100" style="object-fit: cover; min-height: 200px;">
                                </div>
                            <?php } ?>
                            <div class="col-md-<?= !empty($img) ? '8' : '12' ?> p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <h3 class="font-weight-bold mb-2">
                                        <a href="{{ pathto('post/' . $post['slug']) }}" class="text-decoration-none text-dark">{{ htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') }}</a>
                                    </h3>
                                    <p class="text-muted small mb-3">
                                        <i class="fa-solid fa-calendar-days me-2"></i> {{ date('M d, Y', strtotime($post['created_at'])) }}
                                    </p>
                                    <p class="card-text mb-4">
                                        {{ htmlspecialchars($post['excerpt'] ?? substr(strip_tags($post['content']), 0, 150) . '...', ENT_QUOTES, 'UTF-8') }}
                                    </p>
                                </div>
                                <div>
                                    <a href="{{ pathto('post/' . $post['slug']) }}" class="btn btn-outline-info btn-sm rounded-pill px-4 text-dark font-weight-bold">Explore &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php } ?>
            <?php } ?>
        </div>

        <!-- Right Sidebar layout check -->
        <?php if ($layout === 'right') { ?>
            <div class="col-lg-4 mb-4">
                {{ $this->view('Themes/marine/sidebar', $data) }}
            </div>
        <?php } ?>
    </div>
</div>

{{ $this->view('Themes/marine/footer', $data) }}
