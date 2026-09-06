<?php
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

$widget_settings = $widget_settings ?? ['sidebar_layout'=>'right'];
$page = $page ?? null;
$blocks = $blocks ?? [];

$layoutSetting = $widget_settings['sidebar_layout'] ?? 'right';
$page_meta = (new \App\Models\PostModel())->getPostMeta((int)$page->id);
$layout = $page_meta['page_layout'] ?? 'full_width';

if ($layout === 'full_width') {
    $mainCols = 'col-lg-12';
} else {
    $mainCols = 'col-lg-8';
}

$img = !empty($page->featured_image) ? pathto($page->featured_image) : '';
?>

<div class="container my-5">
    <div class="row">
        <!-- Left Sidebar layout check -->
        <?php if ($layout === 'sidebar_left' && $layoutSetting !== 'none') { ?>
            <div class="col-lg-4 mb-4">
                {{ $this->view('Themes/codies/sidebar', $viewData) }}
            </div>
        <?php } ?>

        <!-- Page Content Column -->
        <div class="{{ $mainCols }}">
            <article class="bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light">
            <!--     <header class="mb-4">
                    <h1 class="display-5 font-weight-bold text-dark">{{ htmlspecialchars($page->title, ENT_QUOTES, 'UTF-8') }}</h1>
                </header> -->

                <?php if (!empty($img)) { ?>
                    <div class="mb-4 text-center">
                        <img src="{{ $img }}" class="img-fluid rounded-3" style="max-height: 350px; width: 100%; object-fit: cover;">
                    </div>
                <?php } ?>

                <div class="entry-content mb-4">
                    <!-- If Page Builder blocks exist, render them recursively -->
                    <?php if (!empty($blocks)) { ?>
                        <div class="page-builder-canvas">
                            <?php foreach ($blocks as $b) { ?>
                                <div class="pb-block mb-4">
                                    <?php if ($b['type'] === 'heading') { 
                                        $size = $b['size'] ?? 'h2';
                                        ?>
                                        <{{ $size }} class="font-weight-bold text-dark mt-4 mb-3">{{ htmlspecialchars($b['title'] ?? '', ENT_QUOTES, 'UTF-8') }}</{{ $size }}>
                                    <?php } elseif ($b['type'] === 'text') { ?>
                                        <div class="rich-text-block" style="line-height: 1.8;">
                                            {{ $b['content'] ?? '' }}
                                        </div>
                                    <?php } elseif ($b['type'] === 'image') { ?>
                                        <div class="text-center my-3">
                                            <img src="{{ pathto($b['url'] ?? '') }}" class="img-fluid rounded shadow-sm" alt="{{ htmlspecialchars($b['alt'] ?? '', ENT_QUOTES, 'UTF-8') }}">
                                        </div>
                                    <?php } elseif ($b['type'] === 'columns') { ?>
                                        <div class="row my-4">
                                            <div class="col-md-6 mb-3">
                                                <div class="p-3 bg-light rounded" style="line-height: 1.7;">
                                                    {{ $b['col1'] ?? '' }}
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="p-3 bg-light rounded" style="line-height: 1.7;">
                                                    {{ $b['col2'] ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                    <?php } elseif ($b['type'] === 'php') { ?>
                                        <div class="custom-php-execution-block">
                                            <?php 
                                            $code = $b['code'] ?? '';
                                            if (!empty($code)) {
                                                try {
                                                    eval('?>' . $code);
                                                } catch (\Throwable $e) {
                                                    echo '<div class="alert alert-danger font-monospace">PHP Execution Error: ' . $e->getMessage() . '</div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    <?php } elseif ($b['type'] === 'alert') { 
                                        $alertType = $b['alert_type'] ?? 'info';
                                        ?>
                                        <div class="alert alert-{{ $alertType }} shadow-sm rounded border-0" role="alert">
                                            {{ htmlspecialchars($b['content'] ?? '', ENT_QUOTES, 'UTF-8') }}
                                        </div>
                                    <?php } elseif ($b['type'] === 'button') { 
                                        $btnStyle = $b['btn_style'] ?? 'btn-primary';
                                        $btnUrl = $b['url'] ?? '#';
                                        ?>
                                        <div class="my-3 text-start">
                                            <a href="{{ $btnUrl }}" class="btn {{ $btnStyle }} px-4 py-2 font-weight-bold rounded-pill shadow-sm">{{ htmlspecialchars($b['title'] ?? '', ENT_QUOTES, 'UTF-8') }}</a>
                                        </div>
                                    <?php } elseif ($b['type'] === 'divider') { 
                                        $dividerStyle = $b['style'] ?? 'solid';
                                        ?>
                                        <hr style="border-top: 2px {{ $dividerStyle }} #dee2e6; opacity: 1;" class="my-4">
                                    <?php } elseif ($b['type'] === 'html') { ?>
                                        <div class="custom-html-block">
                                            {{ $b['content'] ?? '' }}
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <!-- Fallback to standard text layout content -->
                        <div class="normal-page-content" style="line-height: 1.8; font-size: 1.1rem;">
                            {{ htmlspecialchars_decode($page->content) }}
                        </div>
                    <?php } ?>
                </div>
            </article>
        </div>

        <!-- Right Sidebar layout check -->
        <?php if ($layout === 'sidebar_right' && $layoutSetting !== 'none') { ?>
            <div class="col-lg-4 mb-4">
                {{ $this->view('Themes/codies/sidebar', $viewData) }}
            </div>
        <?php } ?>
    </div>
</div>

{{ $this->view('Themes/codies/footer', $viewData) }}
