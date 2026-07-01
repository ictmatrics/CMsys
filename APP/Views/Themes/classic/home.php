<?php error_log('HOME_SCOPE: title=' . (isset($title) ? $title : 'NOTSET') . ' data_keys=' . (isset($data) ? implode(',', array_keys($data)) : 'NODATA')); ?><?php echo isset($data) ? '' : '<!-- DATA IS EMPTY -->'; ?>
{{ $this->view('Themes/classic/header', $data) }}

<!-- Hero Banner -->
<section class="hero-banner text-center text-md-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 font-weight-bold text-dark animate__animated animate__fadeInLeft">{{ $site_title }}</h1>
                <p class="lead text-muted animate__animated animate__fadeInLeft" style="animation-delay: 0.1s;">{{ $site_description }}</p>
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
        
        // Handle search filter if present
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
                {{ $this->view('Themes/classic/sidebar', $data) }}
            </div>
        <?php } ?>

        <!-- Main Loop -->
        <div class="{{ $mainCols }}">
            <?php if (!empty($searchQuery)) { ?>
                <h4 class="mb-4">Search Results for: <code>{{ htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') }}</code></h4>
            <?php } ?>

            <?php if (empty($filteredPosts)) { ?>
                <div class="card p-5 text-center text-muted border-0 shadow-sm rounded-3">
                    <i class="fa-solid fa-file-excel fa-4x mb-3 text-secondary"></i>
                    <h4>No posts found.</h4>
                    <p class="mb-0">Try checking back later or searching for a different keyword.</p>
                </div>
            <?php } else { ?>
                <?php foreach ($filteredPosts as $post) { 
                    // Calculate reading time (roughly 200 words per minute)
                    $wordCount = str_word_count(strip_tags($post['content']));
                    $readingTime = ceil($wordCount / 200);
                    $img = !empty($post['featured_image']) ? pathto($post['featured_image']) : '';
                    ?>
                    <article class="card card-post overflow-hidden">
                        <?php if ($img) { ?>
                            <a href="{{ pathto('post/' . $post['slug']) }}" class="d-block overflow-hidden" style="height: 250px;">
                                <img src="{{ $img }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </a>
                        <?php } ?>
                        <div class="card-body p-4 p-md-5">
                            <div class="mb-3 d-flex align-items-center text-muted small fw-semibold">
                                <span><i class="fa-regular fa-calendar me-2"></i>{{ date('M d, Y', strtotime($post['created_at'])) }}</span>
                                <span class="mx-2 text-primary">•</span>
                                <span><i class="fa-regular fa-clock me-2"></i>{{ $readingTime }} min read</span>
                            </div>
                            <h2 class="card-title h3 mb-3">
                                <a href="{{ pathto('post/' . $post['slug']) }}" class="text-dark text-decoration-none font-weight-bold">{{ htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') }}</a>
                            </h2>
                            <p class="card-text text-secondary mb-4" style="line-height: 1.7; font-size: 1.05rem;">
                                {{ substr(strip_tags($post['content']), 0, 180) }}...
                            </p>
                            <a href="{{ pathto('post/' . $post['slug']) }}" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold">Read Article <i class="fa-solid fa-arrow-right ms-2"></i></a>
                        </div>
                    </article>
                <?php } ?>
            <?php } ?>
        </div>

        <!-- Right Sidebar layout check -->
        <?php if ($layout === 'right') { ?>
            <div class="col-lg-4 mb-4">
                {{ $this->view('Themes/classic/sidebar', $data) }}
            </div>
        <?php } ?>
    </div>
</div>

{{ $this->view('Themes/classic/footer', $data) }}
