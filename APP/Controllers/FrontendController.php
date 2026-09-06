<?php
declare(strict_types=1);

namespace App\Controllers;

use System\Config\Controller;
use App\Models\OptionModel;
use App\Models\PostModel;
use App\Models\TaxonomyModel;
use App\Models\CommentModel;
use App\Models\MenuModel;

class FrontendController extends Controller
{
    private OptionModel $optionModel;
    private PostModel $postModel;
    private TaxonomyModel $taxonomyModel;
    private CommentModel $commentModel;
    private MenuModel $menuModel;

    public function __construct()
    {
        $this->optionModel = new OptionModel();
        $this->postModel = new PostModel();
        $this->taxonomyModel = new TaxonomyModel();
        $this->commentModel = new CommentModel();
        $this->menuModel = new MenuModel();

        $this->checkMaintenance();
        boot_active_modules();
    }

    private function getLayoutData(): array
    {
        $theme = $this->optionModel->getOption('frontend_theme', 'classic');
        
        // 1. Navigation Menus
        $mainMenu = $this->menuModel->getMenuByLocation('main');
        $mainMenuItems = $mainMenu ? $this->menuModel->getMenuItems((int)$activeId = $mainMenu->id) : [];

        $footerMenu = $this->menuModel->getMenuByLocation('footer');
        $footerMenuItems = $footerMenu ? $this->menuModel->getMenuItems((int)$activeId = $footerMenu->id) : [];

        // 2. Widget settings & data
        $widgetJson = $this->optionModel->getOption('widget_settings', '{"sidebar_widgets":[],"sidebar_layout":"right"}');
        $widgetSettings = json_decode($widgetJson, true) ?: [];
        $recentLimit = (int)($widgetSettings['recent_posts_limit'] ?? 5);
        $trendingLimit = (int)($widgetSettings['trending_posts_limit'] ?? 5);

        $categories = $this->taxonomyModel->getAllTaxonomies('category');
        $tags = $this->taxonomyModel->getAllTaxonomies('tag');
        $recentPosts = $this->postModel->getRecentPublishedPosts($recentLimit > 0 ? $recentLimit : 5);
        $trendingPosts = $this->postModel->getTrendingPosts($trendingLimit > 0 ? $trendingLimit : 5);
        $pages = $this->postModel->getPublishedPages();

        $themeConfigJson = $this->optionModel->getOption("theme_{$theme}_config", '{}');
        $themeConfig = json_decode($themeConfigJson, true) ?: [];


        return [
            'theme_name' => $theme,
            'theme_config' => $themeConfig,
            'site_title' => $this->optionModel->getOption('site_title', 'CMsys Website'),
            'site_description' => $this->optionModel->getOption('site_description'),
            'site_logo' => $this->optionModel->getOption('site_logo'),
            'site_favicon' => $this->optionModel->getOption('site_favicon'),
            'site_loader' => $this->optionModel->getOption('site_loader'),
            'custom_css' => $this->optionModel->getOption('custom_css'),
            'custom_js_header' => $this->optionModel->getOption('custom_js_header'),
            'custom_js_footer' => $this->optionModel->getOption('custom_js_footer'),
            'main_menu_items' => $mainMenuItems,
            'footer_menu_items' => $footerMenuItems,
            'site_email' => $this->optionModel->getOption('site_email', ''),
            'widget_settings' => $widgetSettings,
            'widget_categories' => $categories,
            'widget_tags' => $tags,
            'widget_recent_posts' => $recentPosts,
            'widget_trending_posts' => $trendingPosts,
            'widget_pages' => $pages
        ];
    }

    private function isAuthorized(object $content): bool
    {
        if ($content->status === 'published') {
            return true;
        }

        // Allow previewing draft/scheduled if logged in as Admin or Editor
        if (isset($_SESSION['ICTM_Auth']) && in_array($_SESSION['ICTM_Auth']['role'], ['admin', 'editor'], true)) {
            return true;
        }

        return false;
    }

    public function post(string $slug)
    {
        $slug = validate_data($slug);
        $post = $this->postModel->getPostBySlug($slug, 'post');

        if (!$post || !$this->isAuthorized($post)) {
            redirect('404.php');
            exit();
        }

        // Increment views
        $this->postModel->incrementViews((int)$post->id);
        $post->views = ((int)($post->views ?? 0)) + 1;

        $categories = $this->taxonomyModel->getPostTaxonomies((int)$post->id, 'category');
        $tags = $this->taxonomyModel->getPostTaxonomies((int)$post->id, 'tag');
        $comments = $this->commentModel->getCommentsByPost((int)$post->id, 'approved');

        if (function_exists('apply_filters')) {
            $post->content = apply_filters('the_content', $post->content);
        }

        $data = array_merge($this->getLayoutData(), [
            'post' => $post,
            'post_categories' => $categories,
            'post_tags' => $tags,
            'comments' => $comments,
            'title' => $post->title
        ]);

        echo $this->view('Themes/' . $data['theme_name'] . '/post', $data);
    }

    public function page(string $slug)
    {
        $slug = validate_data($slug);
        $page = $this->postModel->getPostBySlug($slug, 'page');

        if (!$page || !$this->isAuthorized($page)) {
            redirect('404.php');
            exit();
        }

        $showOnFront = $this->optionModel->getOption('show_on_front', 'posts');
        if ($showOnFront === 'page') {
            $pageForPostsId = (int)$this->optionModel->getOption('page_for_posts', '0');
            if ($pageForPostsId > 0 && (int)$page->id === $pageForPostsId) {
                $postsPerPage = (int)$this->optionModel->getOption('posts_per_page', '10');
                $posts = $this->postModel->getPublishedPosts($postsPerPage);

                $data = array_merge($this->getLayoutData(), [
                    'posts' => $posts,
                    'title' => $page->title
                ]);

                echo $this->view('Themes/' . $data['theme_name'] . '/home', $data);
                return;
            }
        }

        // Block-builder blocks integration
        $blocksJson = $this->postModel->getSingleMeta((int)$page->id, 'page_blocks', '[]');
        $blocks = json_decode($blocksJson, true);

        if (function_exists('apply_filters')) {
            $page->content = apply_filters('the_content', $page->content);
        }

        $data = array_merge($this->getLayoutData(), [
            'page' => $page,
            'blocks' => $blocks,
            'title' => $page->title
        ]);

        echo $this->view('Themes/' . $data['theme_name'] . '/page', $data);
    }

    public function detail(string $slug)
    {
        $slug = validate_data($slug);

        // 1. Try finding a post with this slug
        $post = $this->postModel->getPostBySlug($slug, 'post');
        if ($post && $this->isAuthorized($post)) {
            // Increment views
            $this->postModel->incrementViews((int)$post->id);
            $post->views = ((int)($post->views ?? 0)) + 1;

            $categories = $this->taxonomyModel->getPostTaxonomies((int)$post->id, 'category');
            $tags = $this->taxonomyModel->getPostTaxonomies((int)$post->id, 'tag');
            $comments = $this->commentModel->getCommentsByPost((int)$post->id, 'approved');

            if (function_exists('apply_filters')) {
                $post->content = apply_filters('the_content', $post->content);
            }

            $data = array_merge($this->getLayoutData(), [
                'post' => $post,
                'post_categories' => $categories,
                'post_tags' => $tags,
                'comments' => $comments,
                'title' => $post->title
            ]);

            echo $this->view('Themes/' . $data['theme_name'] . '/post', $data);
            return;
        }

        // 2. Try finding a page with this slug
        $page = $this->postModel->getPostBySlug($slug, 'page');
        if ($page && $this->isAuthorized($page)) {
            $showOnFront = $this->optionModel->getOption('show_on_front', 'posts');
            if ($showOnFront === 'page') {
                $pageForPostsId = (int)$this->optionModel->getOption('page_for_posts', '0');
                if ($pageForPostsId > 0 && (int)$page->id === $pageForPostsId) {
                    $postsPerPage = (int)$this->optionModel->getOption('posts_per_page', '10');
                    $posts = $this->postModel->getPublishedPosts($postsPerPage);

                    $data = array_merge($this->getLayoutData(), [
                        'posts' => $posts,
                        'title' => $page->title
                    ]);

                    echo $this->view('Themes/' . $data['theme_name'] . '/home', $data);
                    return;
                }
            }

            // Block-builder blocks integration
            $blocksJson = $this->postModel->getSingleMeta((int)$page->id, 'page_blocks', '');
            $blocks = !empty($blocksJson) ? json_decode($blocksJson, true) : [];

            if (function_exists('apply_filters')) {
                $page->content = apply_filters('the_content', $page->content);
            }

            $data = array_merge($this->getLayoutData(), [
                'page' => $page,
                'blocks' => $blocks,
                'title' => $page->title
            ]);

            echo $this->view('Themes/' . $data['theme_name'] . '/page', $data);
            return;
        }

        // 3. Try finding a custom post type entry with this slug
        $cptEntry = $this->postModel->find_single('posts', null, '', [
            ['slug', '=', $slug]
        ]);
        if ($cptEntry && !in_array($cptEntry->type, ['post', 'page'], true) && $this->isAuthorized($cptEntry)) {
            $cptEntry->meta = $this->postModel->getPostMeta((int)$cptEntry->id);
            
            if (function_exists('apply_filters')) {
                $cptEntry->content = apply_filters('the_content', $cptEntry->content);
            }
            $data = array_merge($this->getLayoutData(), [
                'entry' => $cptEntry,
                'title' => $cptEntry->title
            ]);

            try {
                echo $this->view('Themes/' . $data['theme_name'] . '/cpt_' . $cptEntry->type, $data);
            } catch (\Throwable $e) {
                $data['page'] = $cptEntry;
                $data['blocks'] = [];
                echo $this->view('Themes/' . $data['theme_name'] . '/page', $data);
            }
            return;
        }

        // 4. Fallback to 404
        redirect('404.php');
        exit();
    }

    public function category(string $slug)
    {
        $slug = validate_data($slug);
        $cat = $this->taxonomyModel->getTaxonomyBySlug($slug, 'category');

        if (!$cat) {
            redirect('404.php');
            exit();
        }

        $posts = $this->postModel->getPostsByTaxonomy((int)$cat->id);

        $data = array_merge($this->getLayoutData(), [
            'posts' => $posts,
            'archive_title' => 'Category: ' . $cat->name,
            'title' => $cat->name
        ]);

        echo $this->view('Themes/' . $data['theme_name'] . '/archive', $data);
    }

    public function tag(string $slug)
    {
        $slug = validate_data($slug);
        $tag = $this->taxonomyModel->getTaxonomyBySlug($slug, 'tag');

        if (!$tag) {
            redirect('404.php');
            exit();
        }

        $posts = $this->postModel->getPostsByTaxonomy((int)$tag->id);

        $data = array_merge($this->getLayoutData(), [
            'posts' => $posts,
            'archive_title' => 'Tag: ' . $tag->name,
            'title' => $tag->name
        ]);

        echo $this->view('Themes/' . $data['theme_name'] . '/archive', $data);
    }

    public function addComment()
    {
        $postId = (int)($_POST['post_id'] ?? 0);
        $name = validate_data($_POST['author_name'] ?? '');
        $email = validate_data($_POST['author_email'] ?? '');
        $content = validate_data($_POST['content'] ?? '');

        if ($postId <= 0 || empty($name) || empty($email) || empty($content)) {
            flash('comment_msg', 'All fields are required!', 'alert alert-danger');
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $commentId = $this->commentModel->saveComment([
            'post_id' => $postId,
            'author_name' => $name,
            'author_email' => $email,
            'content' => $content,
            'status' => 'pending' // pending moderation by default
        ]);

        if ($commentId) {
            flash('comment_msg', 'Comment submitted and is awaiting moderation!', 'alert alert-success');
            
            // Check triggers for notifications
            $triggers = json_decode($this->optionModel->getOption('notify_triggers', '[]'), true);
            if (in_array('comment_added', $triggers, true)) {
                $adminEmail = $this->optionModel->getOption('site_email');
                if (!empty($adminEmail)) {
                    $subject = "New Comment Pending Moderation";
                    $body = "A new comment has been posted by $name ($email):\n\n$content\n\nApprove it in the dashboard.";
                    @mail($adminEmail, $subject, $body, "From: noreply@cmsys.wis");
                }
            }
        } else {
            flash('comment_msg', 'Failed to submit comment. Try again.', 'alert alert-danger');
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public function cptEntry(string $type, string $slug)
    {
        $type = validate_data($type);
        $slug = validate_data($slug);

        $post = $this->postModel->getPostBySlug($slug, $type);
        if (!$post || !$this->isAuthorized($post)) {
            redirect('404.php');
            exit();
        }

        if (function_exists('apply_filters')) {
            $post->content = apply_filters('the_content', $post->content);
        }

        $data = array_merge($this->getLayoutData(), [
            'entry' => $post,
            'title' => $post->title
        ]);

        // Attempt CPT specific view, fallback to standard page
        try {
            echo $this->view('Themes/' . $data['theme_name'] . '/cpt_' . $type, $data);
        } catch (\Throwable $e) {
            // Fallback to page layout
            $data['page'] = $post;
            $data['blocks'] = [];
            echo $this->view('Themes/' . $data['theme_name'] . '/page', $data);
        }
    }

    private function checkMaintenance(): void
    {
        try {
            $rows = db()->query("SHOW TABLES LIKE 'site_options'");
            if (empty($rows)) {
                return;
            }

            $maintenanceMode = $this->optionModel->getOption('maintenance_mode', '0');
            if ($maintenanceMode === '1') {
                $isAdminOrEditor = isset($_SESSION['ICTM_Auth']) && in_array($_SESSION['ICTM_Auth']['role'], ['admin', 'editor'], true);
                if (!$isAdminOrEditor) {
                    header('HTTP/1.1 503 Service Temporarily Unavailable', true, 503);
                    header('Retry-After: 3600');

                    $theme = $this->optionModel->getOption('frontend_theme', 'classic');
                    $data = [
                        'site_title' => $this->optionModel->getOption('site_title', 'CMsys Website'),
                        'site_description' => $this->optionModel->getOption('site_description', 'Under Maintenance'),
                        'theme_name' => $theme,
                        'site_logo' => $this->optionModel->getOption('site_logo'),
                        'site_favicon' => $this->optionModel->getOption('site_favicon'),
                    ];

                    echo $this->view('maintenance', $data);
                    exit();
                }
            }
        } catch (\Throwable $e) {
            // Ignore database issues so the installer/bootstrap can handle them normally
        }
    }
}
