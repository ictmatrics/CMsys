{{ $this->view('Themes/' . $theme_name . '/header', $data) }}

<?php
$pgObj = isset($page) ? (is_object($page) ? $page : (object)$page) : null;
$slug = strtolower($pgObj->slug ?? '');

// Dispatch to specialized templates based on admin-registered page slug
if ($slug === 'about' && file_exists(APPPATH . 'Views/Themes/' . $theme_name . '/about.php')) {
    echo $this->view('Themes/' . $theme_name . '/about', $data);
    return;
}
if ($slug === 'contact' && file_exists(APPPATH . 'Views/Themes/' . $theme_name . '/contact.php')) {
    echo $this->view('Themes/' . $theme_name . '/contact', $data);
    return;
}
if ($slug === 'faq' && file_exists(APPPATH . 'Views/Themes/' . $theme_name . '/faq.php')) {
    $faqModel = new \App\Models\FaqModel();
    $data['faqs'] = $faqModel->getPublishedFaqs();
    echo $this->view('Themes/' . $theme_name . '/faq', $data);
    return;
}
if (($slug === 'services' || $slug === 'service') && file_exists(APPPATH . 'Views/Themes/' . $theme_name . '/service.php')) {
    $postModel = new \App\Models\PostModel();
    $services = $postModel->getAllPosts('service');
    if (empty($services)) {
        $services = $postModel->getAllPosts('post');
    }
    $data['services'] = $services;
    echo $this->view('Themes/' . $theme_name . '/service', $data);
    return;
}
if (($slug === 'products' || $slug === 'product') && file_exists(APPPATH . 'Views/Themes/' . $theme_name . '/product.php')) {
    $postModel = new \App\Models\PostModel();
    $products = $postModel->getAllPosts('product');
    if (empty($products)) {
        $products = $postModel->getAllPosts('post');
    }
    $data['products'] = $products;
    echo $this->view('Themes/' . $theme_name . '/product', $data);
    return;
}
if (($slug === 'blog' || $slug === 'articles' || $slug === 'news' || $slug === 'posts') && file_exists(APPPATH . 'Views/Themes/' . $theme_name . '/blog.php')) {
    $postModel = new \App\Models\PostModel();
    $data['posts'] = $postModel->getPublishedPosts(12);
    echo $this->view('Themes/' . $theme_name . '/blog', $data);
    return;
}
?>

{{ $this->view('Themes/' . $theme_name . '/breadcrumbs', $data) }}

<?php
$pgTitle = $pgObj->title ?? ($title ?? 'Page');
$pgContent = $pgObj->content ?? '';
$blocks = $blocks ?? [];
?>

<section class="py-5">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <article class="card border-0 shadow-sm rounded-4 p-5 mb-4">
                    <h1 class="display-6 fw-bold mb-4 heading-font">{{ htmlspecialchars($pgTitle, ENT_QUOTES, 'UTF-8') }}</h1>
                    
                    <?php if (!empty($pgContent)) { ?>
                        <div class="page-content text-secondary mb-4" style="font-size: 1.05rem; line-height: 1.8;">
                            {{ $pgContent }}
                        </div>
                    <?php } ?>

                    <!-- Integrated Section Content & Page Blocks -->
                    <?php if (!empty($blocks) && is_array($blocks)) { ?>
                        <div class="integrated-section-blocks mt-4">
                            <?php foreach ($blocks as $block) { 
                                $blockTitle = $block['title'] ?? ($block['heading'] ?? '');
                                $blockContent = $block['content'] ?? ($block['body'] ?? '');
                                $blockType = $block['type'] ?? 'section';
                            ?>
                                <div class="section-block border-top pt-4 mt-4 block-type-{{ htmlspecialchars($blockType, ENT_QUOTES, 'UTF-8') }}">
                                    <?php if (!empty($blockTitle)) { ?>
                                        <h3 class="fw-bold mb-3 heading-font text-primary">{{ htmlspecialchars($blockTitle, ENT_QUOTES, 'UTF-8') }}</h3>
                                    <?php } ?>
                                    <?php if (!empty($blockContent)) { ?>
                                        <div class="block-content text-secondary">
                                            {{ apply_filters('the_content', $blockContent) }}
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </article>
            </div>
        </div>
    </div>
</section>

{{ $this->view('Themes/' . $theme_name . '/footer', $data) }}
