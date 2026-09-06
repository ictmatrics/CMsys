{{ $this->view('Themes/' . $theme_name . '/header', $data) }}
{{ $this->view('Themes/' . $theme_name . '/breadcrumbs', $data) }}

<?php
$pObj = isset($post) ? (is_object($post) ? $post : (object)$post) : null;
$pTitle = $pObj->title ?? 'Blog Details';
$pContent = $pObj->content ?? '';
$pDate = $pObj->publish_date ?? ($pObj->created_at ?? date('Y-m-d'));
?>

<section class="py-5">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <article class="card border-0 shadow-sm rounded-4 p-5 mb-5">
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-3 align-self-start px-3 py-2">Tech Insights</span>
                    <h1 class="display-5 fw-bold mb-3 heading-font">{{ htmlspecialchars($pTitle, ENT_QUOTES, 'UTF-8') }}</h1>
                    <div class="text-muted small mb-4 pb-3 border-bottom">
                        <i class="fa-regular fa-calendar me-1 text-primary"></i> {{ $pDate }}
                    </div>

                    <div class="post-content leading-relaxed text-secondary" style="font-size: 1.1rem; line-height: 1.8;">
                        {{ $pContent }}
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

{{ $this->view('Themes/' . $theme_name . '/footer', $data) }}
