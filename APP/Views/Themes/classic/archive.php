{{ $this->view('Themes/classic/header', $data) }}

<!-- Archive Banner -->
<section class="hero-banner text-center text-md-start">
    <div class="container">
        <h1 class="display-5 font-weight-bold text-dark animate__animated animate__fadeInLeft">{{ $archive_title }}</h1>
        <p class="text-muted mb-0">Browse latest posts related to this taxonomy keyword archive.</p>
    </div>
</section>

<!-- Content Main Area -->
<div class="container my-5">
    <div class="row">
        <?php 
        $layout = $widget_settings['sidebar_layout'] ?? 'right';
        $mainCols = $layout === 'none' ? 'col-lg-12' : 'col-lg-8';
        ?>

        <!-- Left Sidebar layout check -->
        <?php if ($layout === 'left') { ?>
            <div class="col-lg-4 mb-4">
                {{ $this->view('Themes/classic/sidebar', $data) }}
            </div>
        <?php } ?>

        <!-- Main Archive Loop -->
        <div class="{{ $mainCols }}">
            <?php if (empty($posts)) { ?>
                <div class="card p-5 text-center text-muted border-0 shadow-sm rounded-3">
                    <i class="fa-solid fa-file-excel fa-4x mb-3 text-secondary"></i>
                    <h4>No posts found.</h4>
                    <p class="mb-0">No posts are tagged under this archive taxonomy yet.</p>
                </div>
            <?php } else { ?>
                <?php foreach ($posts as $post) { 
                    $meta = (new \App\Models\PostModel())->getPostMeta((int)$post['id']);
                    $img = isset($meta['featured_image']) && !empty($meta['featured_image']) ? pathto($meta['featured_image']) : '';
                    ?>
                    <article class="card card-post overflow-hidden border-0">
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
                                        <i class="fa-solid fa-calendar me-2"></i> {{ date('M d, Y', strtotime($post['created_at'])) }}
                                    </p>
                                    <p class="card-text mb-4">
                                        {{ htmlspecialchars($post['excerpt'] ?? substr(strip_tags($post['content']), 0, 150) . '...', ENT_QUOTES, 'UTF-8') }}
                                    </p>
                                </div>
                                <div>
                                    <a href="{{ pathto('post/' . $post['slug']) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Read More &rarr;</a>
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
                {{ $this->view('Themes/classic/sidebar', $data) }}
            </div>
        <?php } ?>
    </div>
</div>

{{ $this->view('Themes/classic/footer', $data) }}
