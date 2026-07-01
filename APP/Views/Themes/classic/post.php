{{ $this->view('Themes/classic/header', $data) }}

<div class="container my-5">
    <div class="row">
        <?php 
        $layout = $widget_settings['sidebar_layout'] ?? 'right';
        $mainCols = $layout === 'none' ? 'col-lg-12' : 'col-lg-8';
        $img = isset($post->meta['featured_image']) && !empty($post->meta['featured_image']) ? pathto($post->meta['featured_image']) : '';
        ?>

        <!-- Left Sidebar -->
        <?php if ($layout === 'left') { ?>
            <div class="col-lg-4 mb-4">
                {{ $this->view('Themes/classic/sidebar', $data) }}
            </div>
        <?php } ?>

        <!-- Post Content Column -->
        <div class="{{ $mainCols }}">
            <article class="bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light mb-5">
                <header class="mb-4">
                    <h1 class="display-5 font-weight-bold text-dark">{{ htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8') }}</h1>
                    <div class="text-muted small my-3 d-flex flex-wrap align-items-center gap-3">
                        <span><i class="fa-solid fa-calendar me-2"></i> {{ date('M d, Y', strtotime($post->created_at)) }}</span>
                        <?php if (!empty($post_categories)) { ?>
                            <span>
                                <i class="fa-solid fa-folder me-2 text-primary"></i>
                                <?php foreach ($post_categories as $cat) { ?>
                                    <span class="badge bg-light text-dark border me-1">{{ htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') }}</span>
                                <?php } ?>
                            </span>
                        <?php } ?>
                    </div>
                </header>

                <?php if (!empty($img)) { ?>
                    <div class="mb-4 text-center">
                        <img src="{{ $img }}" class="img-fluid rounded-3" style="max-height: 400px; width: 100%; object-fit: cover;">
                    </div>
                <?php } ?>

                <div class="entry-content mb-5" style="line-height: 1.8; font-size: 1.1rem;">
                    <?= $post->content ?>
                </div>

                <?php if (!empty($post_tags)) { ?>
                    <footer class="pt-3 border-top bg-white">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="font-weight-bold text-muted me-2 align-self-center">Tags:</span>
                            <?php foreach ($post_tags as $tag) { ?>
                                <a href="{{ pathto('tag/' . $tag['slug']) }}" class="btn btn-sm btn-light border" style="font-size: 13px;"><i class="fa-solid fa-tag me-1 text-muted"></i> {{ htmlspecialchars($tag['name'], ENT_QUOTES, 'UTF-8') }}</a>
                            <?php } ?>
                        </div>
                    </footer>
                <?php } ?>
            </article>

            <!-- Comments Area -->
            <section class="bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light mb-5">
                <h4 class="font-weight-bold mb-4"><i class="fa-solid fa-comments me-2 text-primary"></i> Comments ({{ count($comments) }})</h4>
                
                <?php flash('comment_msg'); ?>

                <!-- Comments List -->
                <div class="comments-list mb-5">
                    <?php if (empty($comments)) { ?>
                        <p class="text-muted small">No comments yet. Be the first to comment!</p>
                    <?php } else { ?>
                        <?php foreach ($comments as $comment) { ?>
                            <div class="d-flex mb-4 p-3 bg-light rounded border-start border-primary border-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-weight:700;">
                                        {{ strtoupper(substr($comment['author_name'], 0, 1)) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="font-weight-bold mb-1 text-dark">{{ htmlspecialchars($comment['author_name'], ENT_QUOTES, 'UTF-8') }}</h6>
                                    <small class="text-muted d-block mb-2">{{ date('M d, Y \a\t h:i a', strtotime($comment['created_at'])) }}</small>
                                    <p class="mb-0 text-muted">{{ htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8') }}</p>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>

                <!-- Add Comment Form -->
                <h5 class="font-weight-bold mb-3">Leave a Reply</h5>
                <form action="{{ pathto('comment/add') }}" method="POST">
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="author_name" class="form-label small font-weight-bold">Name</label>
                            <input type="text" name="author_name" id="author_name" class="form-control" required placeholder="Your name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="author_email" class="form-label small font-weight-bold">Email</label>
                            <input type="email" name="author_email" id="author_email" class="form-control" required placeholder="user@example.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="comment_content" class="form-label small font-weight-bold">Comment</label>
                        <textarea name="content" id="comment_content" class="form-control" rows="4" required placeholder="Type your comment..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-paper-plane me-2"></i> Submit Comment</button>
                </form>
            </section>
        </div>

        <!-- Right Sidebar -->
        <?php if ($layout === 'right') { ?>
            <div class="col-lg-4 mb-4">
                {{ $this->view('Themes/classic/sidebar', $data) }}
            </div>
        <?php } ?>
    </div>
</div>

{{ $this->view('Themes/classic/footer', $data) }}
