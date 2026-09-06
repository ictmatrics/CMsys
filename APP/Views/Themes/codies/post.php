<?php
$post = $post ?? null;
$theme_config = $theme_config ?? [];
$raw_css_config = $theme_config['custom_css'] ?? '';
$codies_config = [];
if (preg_match('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', $raw_css_config, $matches)) {
    $codies_config = json_decode($matches[1], true) ?: [];
}
$theme_config = array_merge($theme_config, $codies_config);
$viewData = array_diff_key(get_defined_vars(), ['content' => 1, 'viewFile' => 1, 'view' => 1, 'data' => 1]);
?>
{{ $this->view('Themes/codies/header', $viewData) }}

<?php

$post_categories = $post_categories ?? [];
$post_tags = $post_tags ?? [];
$comments = $comments ?? [];

// Fallbacks
$meta = (new \App\Models\PostModel())->getPostMeta((int)$post->id);
$views = $post->views ?? $meta['views'] ?? ((($post->id * 17) % 500) + 120);
$read_time = !empty($meta['read_time']) ? $meta['read_time'] : calculate_read_time($post->content ?? '');

$userModel = new \App\Models\UserModel();
$author = !empty($post->author_id) ? $userModel->getUserById((int)$post->author_id) : null;
$author_name = $author ? $author->username : '';


$table_of_contents_enabled = !empty($theme_config['table_of_contents']);

// Extract code blocks from content for multi-tab Prismatic hub
$code_tabs = [];
if (preg_match_all('/<pre[^>]*><code[^>]*class="language-([^"]+)"[^>]*>(.*?)<\/code><\/pre>/is', $post->content, $matches, PREG_SET_ORDER)) {
    $idx = 1;
    foreach ($matches as $match) {
        $lang = $match[1];
        $code = htmlspecialchars_decode($match[2]);
        $code_tabs[] = (object)[
            'id' => 'tab-code-' . $idx,
            'title' => 'Snippet ' . $idx . '.' . ($lang === 'javascript' ? 'js' : ($lang === 'python' ? 'py' : $lang)),
            'lang' => $lang,
            'code' => $code
        ];
        $idx++;
    }
}
?>

<section class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ pathto('') }}">Home</a></li>
            <?php if (!empty($post_categories)) { ?>
                <li class="breadcrumb-item"><a href="{{ pathto('category/' . $post_categories[0]['slug']) }}">{{ htmlspecialchars($post_categories[0]['name'], ENT_QUOTES, 'UTF-8') }}</a></li>
            <?php } ?>
            <li class="breadcrumb-item active" aria-current="page">{{ htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8') }}</li>
        </ol>
    </nav>

    <!-- Header Block -->
    <div class="mb-4">
        <h1 class="display-5 article-title mb-3 fw-bold">{{ htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8') }}</h1>
        
        <!-- Metadata parameters -->
        <div class="d-flex flex-wrap align-items-center gap-4 py-2 border-top border-bottom border-light-subtle">
            <div class="d-flex align-items-center gap-2">
          <i class="fa-regular fa-pen-to-square"></i>
                <div>
                    <span class="fw-bold d-block text-primary small">{{ $author_name }}</span>
                </div>
            </div>
            
            <div class="article-meta d-flex flex-wrap gap-3 small text-muted">
                <span><i class="fa-regular fa-calendar-days me-1"></i> Published: {{ date('F j, Y', strtotime(!empty($post->publish_date) ? $post->publish_date : $post->created_at)) }}</span>
                <span><i class="fa-regular fa-clock me-1"></i> {{ htmlspecialchars($read_time, ENT_QUOTES, 'UTF-8') }} duration</span>
                <span><i class="fa-regular fa-eye me-1"></i> {{ htmlspecialchars((string)$views, ENT_QUOTES, 'UTF-8') }} Views</span>
            </div>

            <div class="ms-md-auto d-flex gap-2 mt-2 mt-md-0">
                <button class="btn btn-sm btn-outline-secondary" onclick="triggerToast('Sharing link via Twitter!')"><i class="fa-brands fa-twitter"></i></button>
                <button class="btn btn-sm btn-outline-secondary" onclick="triggerToast('Sharing link via LinkedIn!')"><i class="fa-brands fa-linkedin-in"></i></button>
                <button class="btn btn-sm btn-outline-secondary" onclick="triggerToast('Direct Link copied to clipboard!')"><i class="fa-solid fa-link"></i></button>
            </div>
        </div>

        <!-- Post Featured Image -->
        <div class="mt-4 wow fadeIn" data-wow-duration="1s">
            <img src="{{ !empty($meta['featured_image']) ? pathto($meta['featured_image']) : 'https://placehold.co/1200x500/1e293b/38bdf8?text=' . urlencode($post->title) }}" 
                 class="img-fluid rounded-4 shadow-sm w-100" 
                 alt="{{ htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8') }}" 
                 style="max-height: 420px; object-fit: cover;">
        </div>
    </div>
</section>

<!-- Main content area -->
<main class="container py-2">
    <div class="row g-4">
        <!-- Primary Tutorial Section (75% Width) -->
        <div class="col-lg-9 col-12">
            <article class="article-body bg-white p-4 p-md-5 rounded-4 shadow-sm border mb-4">
                <div class="post-content-wysiwyg">
                    {{ htmlspecialchars_decode($post->content) }}
                </div>

                <!-- Multi-tab Prismatic code hub (renders if code blocks parsed) -->
                <?php if (!empty($code_tabs)) { ?>
                    <div class="prismatic-hub border rounded-3 overflow-hidden my-5">
                        <div class="prismatic-hub-header bg-dark d-flex justify-content-between align-items-center px-3 py-1">
                            <div class="code-tabs d-flex">
                                <?php foreach ($code_tabs as $index => $tab) { ?>
                                    <button class="btn btn-dark text-muted btn-sm code-tab-btn {{ $index === 0 ? 'active text-info' : '' }}" style="border-radius:0;" data-tab="{{ $tab->id }}">{{ htmlspecialchars($tab->title, ENT_QUOTES, 'UTF-8') }}</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="bg-dark p-3">
                            <?php foreach ($code_tabs as $index => $tab) { ?>
                                <div class="code-tab-panel {{ $index === 0 ? 'active' : 'd-none' }}" id="{{ $tab->id }}">
                                    <pre class="m-0 p-0 line-numbers"><code class="language-{{ htmlspecialchars($tab->lang, ENT_QUOTES, 'UTF-8') }}">{{ htmlspecialchars($tab->code, ENT_QUOTES, 'UTF-8') }}</code></pre>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <!-- Tags Row -->
                <?php if (!empty($post_tags)) { ?>
                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <?php foreach ($post_tags as $tag) { ?>
                            <span class="badge bg-light text-secondary">#{{ htmlspecialchars($tag['name'], ENT_QUOTES, 'UTF-8') }}</span>
                        <?php } ?>
                    </div>
                <?php } ?>
            </article>

            <!-- Comments Section -->
            <div class="card mb-4 p-4 p-md-5 border shadow-sm rounded-4 bg-white">
                <h4 class="fw-bold mb-4"><i class="fa-solid fa-comments text-primary me-2"></i>Comments Thread</h4>

                <!-- Flash Message for Comments -->
                {{ flash('comment_msg') }}

                <div class="comments-list mb-4">
                    <?php if (!empty($comments)) { ?>
                        <?php foreach ($comments as $comment) { ?>
                            <div class="d-flex gap-3 pb-3 mb-3 border-bottom">
                                <div class="bg-light rounded-circle text-primary d-flex align-items-center justify-content-center" style="width:40px; height:40px; font-weight:bold;">
                                    {{ strtoupper(substr($comment['author_name'], 0, 1)) }}
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="fw-bold mb-0 text-dark">{{ htmlspecialchars($comment['author_name'], ENT_QUOTES, 'UTF-8') }}</h6>
                                        <span class="text-muted small" style="font-size:0.75rem;">{{ date('M j, Y h:i A', strtotime($comment['created_at'])) }}</span>
                                    </div>
                                    <p class="mb-0 text-secondary small">{{ htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8') }}</p>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="py-4 text-center text-muted">
                            <p class="small">No comments posted yet. Be the first to share your thoughts!</p>
                        </div>
                    <?php } ?>
                </div>

                <!-- Add Comment Form -->
                <h5 class="fw-bold mb-3 mt-4">Leave a Comment</h5>
                <form action="{{ pathto('comment/add') }}" method="POST">
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="author_name" class="form-label small fw-bold">Your Name</label>
                            <input type="text" class="form-control form-control-sm" id="author_name" name="author_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="author_email" class="form-label small fw-bold">Email Address</label>
                            <input type="email" class="form-control form-control-sm" id="author_email" name="author_email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label small fw-bold">Comment Message</label>
                        <textarea class="form-control form-control-sm" id="content" name="content" rows="4" required></textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-semibold shadow-sm">Submit Comment</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Sidebar Table of Contents / Sidebar widgets -->
        <div class="col-lg-3 col-12">
            <div class="toc-widget-sticky" style="position: sticky; top: 5.5rem;">
                <!-- Table of Contents dynamic widget -->
               <!--  <?php if ($table_of_contents_enabled) { ?>
                    <div class="sidebar-widget bg-white shadow-sm mb-4 p-4 rounded-3 border">
                        <h5 class="widget-title mb-3 fw-bold pb-2 border-bottom text-uppercase" style="font-size: 1rem; border-left: 4px solid var(--accent-color); padding-left: 10px; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-list-check me-2 text-primary"></i>On This Page
                        </h5>
                        <ul class="toc-list list-unstyled ps-0 d-flex flex-column gap-2" style="font-size: 0.9rem;">
                            <!-- Dynamically loaded by JavaScript -->
                        <!-- </ul>
                    </div>
                <?php } ?> -->

                {{ $this->view('Themes/codies/sidebar', $viewData) }}
            </div>
        </div>
    </div>
</main>

<script>
    // Dynamically build TOC from H2 elements inside post body
    $(document).ready(function() {
        const $tocList = $('.toc-list');
        const $headings = $('.post-content-wysiwyg h2');
        
        if ($tocList.length > 0 && $headings.length > 0) {
            $headings.each(function(index) {
                const text = $(this).text();
                const cleanId = 'blueprint-heading-' + index;
                $(this).attr('id', cleanId);
                
                $tocList.append(`
                    <li>
                        <a href="#${cleanId}" class="toc-link d-block py-1 text-muted border-start border-2 ps-2 text-decoration-none" style="font-size:0.85rem; font-weight:500;">
                            ${text}
                        </a>
                    </li>
                `);
            });

            // Adjust TOC active scroll states
            $(window).on('scroll', function() {
                const scrollTop = $(window).scrollTop();
                let activeId = '';
                
                $headings.each(function() {
                    if (scrollTop >= $(this).offset().top - 140) {
                        activeId = $(this).attr('id');
                    }
                });

                if (activeId) {
                    $('.toc-link').removeClass('active border-primary text-primary').addClass('text-muted');
                    $(`.toc-link[href="#${activeId}"]`).addClass('active border-primary text-primary').removeClass('text-muted');
                }
            });
        } else {
            // Hide widget if no headings
            $('.toc-widget').closest('.sidebar-widget').hide();
        }
    });
</script>

{{ $this->view('Themes/codies/footer', $viewData) }}
