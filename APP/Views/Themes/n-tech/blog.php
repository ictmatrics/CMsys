{{ $this->view('Themes/' . $theme_name . '/header', $data) }}
{{ $this->view('Themes/' . $theme_name . '/breadcrumbs', $data) }}

<!--==============================
Blog Section (Aligned with /public_html/n-tech/blog.html)
==============================-->
<div class="tv-blog-section py-5 bg-light">
    <div class="container py-3">
        <div class="row gy-4">
            <?php
            $postsList = $posts ?? [];
            if (!empty($postsList)) {
                $bCount = 0;
                foreach ($postsList as $p) {
                    $bCount++;
                    $postObj = is_object($p) ? $p : (object)$p;
                    $pTitle = $postObj->title ?? 'Untitled Article';
                    $pSlug = $postObj->slug ?? '';
                    $pDate = !empty($postObj->created_at) ? date('d M, Y', strtotime((string)$postObj->created_at)) : date('d M, Y');
                    $pImg = !empty($postObj->featured_image) ? pathto($postObj->featured_image) : pathto('Themes/n-tech/images/blog/blog0' . (($bCount % 3) + 1) . '.webp');
            ?>
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <article class="blog-single-box h-100 shadow-sm border-0 rounded-4 overflow-hidden bg-white">
                            <div class="inner-box">
                                <div class="blog-image position-relative overflow-hidden" style="max-height: 240px;">
                                    <img src="{{ $pImg }}" alt="{{ htmlspecialchars($pTitle, ENT_QUOTES, 'UTF-8') }}" class="w-100 h-100 object-fit-cover">
                                    <div class="category-tag position-absolute top-0 end-0 bg-primary text-white px-3 py-1 m-3 rounded-pill small fw-semibold">
                                        <span></span>{{ $pDate }}
                                    </div>
                                </div>
                                <div class="blog-content p-4">
                                    <h4 class="title fw-bold fs-5 mb-3">
                                        <a href="{{ pathto($pSlug) }}" class="text-dark text-decoration-none">{{ htmlspecialchars($pTitle, ENT_QUOTES, 'UTF-8') }}</a>
                                    </h4>
                                    <p class="text-muted small mb-3">{{ htmlspecialchars(substr(strip_tags($postObj->content ?? ''), 0, 120), ENT_QUOTES, 'UTF-8') }}...</p>
                                    <div class="pt-2 pb-3"><div class="border-bottom border-secondary opacity-25"></div></div>
                                    <div class="blog-meta d-flex justify-content-between align-items-center pt-2">
                                        <a href="{{ pathto($pSlug) }}" class="continue-reading fw-bold text-primary text-decoration-none">
                                            Explore More <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </a>
                                        <span class="text-muted small"><i class="fa-regular fa-clock me-1"></i> Tech Insight</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
            <?php 
                } 
            } else { 
            ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="fa-solid fa-newspaper fa-3x mb-3 text-secondary opacity-50"></i>
                    <p class="fs-5 fw-semibold mb-0">No blog posts published yet.</p>
                </div>
            <?php } ?>
        </div>

        <?php if (!empty($postsList) && count($postsList) > 6) { ?>
            <ul class="pagination-menu mt-5 text-center list-unstyled d-flex justify-content-center gap-2">
                <li><a href="#" class="btn btn-primary rounded-circle active px-3 py-2">1</a></li>
                <li><a href="#" class="btn btn-outline-secondary rounded-circle px-3 py-2">2</a></li>
                <li><a href="#" class="btn btn-outline-secondary rounded-circle px-3 py-2"><i class="fa-solid fa-arrow-right"></i></a></li>
            </ul>
        <?php } ?>
    </div>
</div>

{{ $this->view('Themes/' . $theme_name . '/footer', $data) }}
