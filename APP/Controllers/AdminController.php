<?php

declare(strict_types=1);

namespace App\Controllers;

use System\Config\Controller;
use App\Models\OptionModel;
use App\Models\UserModel;
use App\Models\PostModel;
use App\Models\TaxonomyModel;
use App\Models\CommentModel;
use App\Models\MediaModel;
use App\Models\MenuModel;
use App\Models\ExtensionModel;
use App\Filters\ICTM_Auth;

class AdminController extends Controller
{
    private OptionModel $optionModel;
    private UserModel $userModel;
    private PostModel $postModel;
    private TaxonomyModel $taxonomyModel;
    private CommentModel $commentModel;
    private MediaModel $mediaModel;
    private MenuModel $menuModel;
    private ExtensionModel $extensionModel;

    public function __construct()
    {
        if (!file_exists(APPPATH . '.env') || is_dir(APPPATH . 'Views/install')) {
            redirect('install');
            exit();
        }

        ICTM_Auth::guard();

        // Initialize Models
        $this->optionModel = new OptionModel();
        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
        $this->taxonomyModel = new TaxonomyModel();
        $this->commentModel = new CommentModel();
        $this->mediaModel = new MediaModel();
        $this->menuModel = new MenuModel();
        $this->extensionModel = new ExtensionModel();
        boot_active_modules();
        $this->extensionModel->bootActiveModules();
    }

    private function checkAdminOnly(): void
    {
        ICTM_Auth::guard('admin');
    }

    public function view(string $view, array $data = []): string
    {
        $vendorsDir = is_dir(APPPATH . 'vendors') ? APPPATH . 'vendors/' : APPPATH . 'Vendors/';

        if (strpos($view, 'admin/') === 0) {
            $backendTheme = $this->optionModel->getOption('backend_theme', 'admin');
            if ($backendTheme !== 'admin') {
                $customVendorThemeView = $vendorsDir . 'themes/' . $backendTheme . '/' . substr($view, 6) . '.php';
                $customCoreThemeView = APPPATH . 'Views/Themes/' . $backendTheme . '/' . substr($view, 6) . '.php';

                if (file_exists($customVendorThemeView)) {
                    return $this->renderDirectViewFile($customVendorThemeView, $data);
                } elseif (file_exists($customCoreThemeView)) {
                    $view = 'Themes/' . $backendTheme . '/' . substr($view, 6);
                }
            }
        }

        if (str_starts_with($view, 'Themes/')) {
            $parts = explode('/', substr($view, 7), 2);
            $themeName = $parts[0] ?? '';
            $subView = $parts[1] ?? '';
            $vendorThemeFile = $vendorsDir . 'themes/' . $themeName . '/' . $subView . '.php';
            if (file_exists($vendorThemeFile)) {
                return $this->renderDirectViewFile($vendorThemeFile, $data);
            }
        }

        if (str_starts_with($view, 'Modules/') || str_contains($view, 'Modules/') || str_starts_with($view, 'Vendors/modules/') || str_starts_with($view, 'vendors/modules/')) {
            $cleanModPath = preg_replace('#^(Vendors/modules/|vendors/modules/|Modules/)#', '', $view);
            $vendorModFile = $vendorsDir . 'modules/' . $cleanModPath . '.php';
            $legacyModFile = APPPATH . 'Modules/' . $cleanModPath . '.php';

            if (file_exists($vendorModFile)) {
                return $this->renderDirectViewFile($vendorModFile, $data);
            } elseif (file_exists($legacyModFile)) {
                return $this->renderDirectViewFile($legacyModFile, $data);
            }
        }

        return parent::view($view, $data);
    }

    private function renderDirectViewFile(string $viewFile, array $data = []): string
    {
        $content = file_get_contents($viewFile);
        $content = preg_replace('/\{\{([^}]+)\}\}/', '<?php echo $1; ?>', $content);
        ob_start();
        extract($data);
        eval('?>' . $content);
        return ob_get_clean() ?: '';
    }

    public function index()
    {
        redirect('admin/dashboard');
        exit();
    }

    // 1. Dashboard View
    public function dashboard()
    {
        $postCount = $this->postModel->num_rows('posts', '', [['type', '=', 'post']]);
        $pageCount = $this->postModel->num_rows('posts', '', [['type', '=', 'page']]);
        $commentCount = $this->commentModel->num_rows('comments');
        $userCount = $this->userModel->num_rows('users');

        $recentPosts = $this->postModel->getRecentPosts(5);

        $data = [
            'title' => 'Dashboard',
            'post_count' => $postCount,
            'page_count' => $pageCount,
            'comment_count' => $commentCount,
            'user_count' => $userCount,
            'recent_posts' => $recentPosts
        ];

        echo $this->view('admin/dashboard', $data);
    }

    // 2. Options and Settings
    public function settings()
    {
        $this->checkAdminOnly();

        $data = [
            'title' => 'System Settings',
            'site_title' => $this->optionModel->getOption('site_title'),
            'site_description' => $this->optionModel->getOption('site_description'),
            'site_email' => $this->optionModel->getOption('site_email'),
            'maintenance_mode' => $this->optionModel->getOption('maintenance_mode', '0'),
            'posts_per_page' => $this->optionModel->getOption('posts_per_page', '10'),
            'logo' => $this->optionModel->getOption('site_logo'),
            'favicon' => $this->optionModel->getOption('site_favicon'),
            'loader' => $this->optionModel->getOption('site_loader'),
            // New options as per screen specifications
            'site_tagline' => $this->optionModel->getOption('site_tagline'),
            'site_timezone' => $this->optionModel->getOption('site_timezone', 'Asia/Kathmandu'),
            'date_format' => $this->optionModel->getOption('date_format', 'Y-m-d'),
            'time_format' => $this->optionModel->getOption('time_format', 'h:i A'),
            'show_on_front' => $this->optionModel->getOption('show_on_front', 'posts'),
            'page_on_front' => $this->optionModel->getOption('page_on_front', '0'),
            'page_for_posts' => $this->optionModel->getOption('page_for_posts', '0'),
            'meta_keywords' => $this->optionModel->getOption('meta_keywords'),
            'analytics_code' => $this->optionModel->getOption('analytics_code'),
            'pages' => $this->postModel->getPublishedPages()
        ];

        echo $this->view('admin/settings', $data);
    }

    public function saveSettings()
    {
        $this->checkAdminOnly();

        $this->optionModel->updateOption('site_title', validate_data($_POST['site_title'] ?? ''));
        $this->optionModel->updateOption('site_description', validate_data($_POST['site_description'] ?? ''));
        $this->optionModel->updateOption('site_email', validate_data($_POST['site_email'] ?? ''));
        $this->optionModel->updateOption('maintenance_mode', validate_data($_POST['maintenance_mode'] ?? '0'));
        $this->optionModel->updateOption('posts_per_page', validate_data($_POST['posts_per_page'] ?? '10'));

        // New options saved here
        $this->optionModel->updateOption('site_tagline', validate_data($_POST['site_tagline'] ?? ''));
        $this->optionModel->updateOption('site_timezone', validate_data($_POST['site_timezone'] ?? 'Asia/Kathmandu'));
        $this->optionModel->updateOption('date_format', validate_data($_POST['date_format'] ?? 'Y-m-d'));
        $this->optionModel->updateOption('time_format', validate_data($_POST['time_format'] ?? 'h:i A'));
        $this->optionModel->updateOption('show_on_front', validate_data($_POST['show_on_front'] ?? 'posts'));
        $this->optionModel->updateOption('page_on_front', validate_data($_POST['page_on_front'] ?? '0'));
        $this->optionModel->updateOption('page_for_posts', validate_data($_POST['page_for_posts'] ?? '0'));
        $this->optionModel->updateOption('meta_keywords', validate_data($_POST['meta_keywords'] ?? ''));
        $this->optionModel->updateOption('analytics_code', $_POST['analytics_code'] ?? '');

        // Logo Uploads
        $uploadDir = ROOTPATH . 'public_html/Writables/images/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        foreach (['site_logo', 'site_favicon', 'site_loader'] as $key) {
            if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
                $filename = $key . '_' . time() . '.' . $ext;
                $targetFile = $uploadDir . $filename;

                if (move_uploaded_file($_FILES[$key]['tmp_name'], $targetFile)) {
                    $webPath = 'Writables/images/' . $filename;
                    $this->optionModel->updateOption($key, $webPath);
                    $this->mediaModel->logMedia($_FILES[$key]['name'], $webPath, $_FILES[$key]['type'], '', $_SESSION['ICTM_Auth']['user_id']);
                }
            } else {
                if (isset($_POST[$key])) {
                    $this->optionModel->updateOption($key, validate_data($_POST[$key]));
                }
            }
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Settings saved successfully!']);
            exit();
        }

        flash('success_msg', 'Settings updated successfully!', 'alert alert-success');
        redirect('admin/settings');
        exit();
    }

    // 3. Posts Management
    public function posts()
    {
        $posts = $this->postModel->getAllPosts('post');
        echo $this->view('admin/posts_list', ['title' => 'Manage Posts', 'posts' => $posts]);
    }

    public function postForm(?string $id = null)
    {
        $post = null;
        $meta = [];
        $postCategories = [];
        $postTags = [];

        if ($id !== null) {
            $post = $this->postModel->getPostById((int)$id);
            if (!$post || $post->type !== 'post') {
                redirect('admin/posts');
                exit();
            }
            $meta = $post->meta;
            $postCategories = array_map(fn($c) => (int)$c['id'], $this->taxonomyModel->getPostTaxonomies((int)$id, 'category'));
            $postTags = array_map(fn($t) => $t['name'], $this->taxonomyModel->getPostTaxonomies((int)$id, 'tag'));
        }

        $categories = $this->taxonomyModel->getAllTaxonomies('category');

        $data = [
            'title' => $post ? 'Edit Post' : 'Add New Post',
            'post' => $post,
            'meta' => $meta,
            'post_categories' => $postCategories,
            'post_tags' => implode(',', $postTags),
            'categories' => $categories
        ];

        echo $this->view('admin/post_form', $data);
    }

    public function savePost()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        $postData = [
            'title' => validate_data($_POST['title'] ?? ''),
            'slug' => validate_data($_POST['slug'] ?? ''),
            'content' => $_POST['content'] ?? '',
            'excerpt' => validate_data($_POST['excerpt'] ?? ''),
            'type' => 'post',
            'status' => validate_data($_POST['status'] ?? 'draft'),
            'publish_date' => !empty($_POST['publish_date']) ? $_POST['publish_date'] : null,
            'author_id' => $_SESSION['ICTM_Auth']['user_id']
        ];

        if ($id > 0) {
            $postData['id'] = $id;
        }

        $metaData = [
            'seo_title' => validate_data($_POST['seo_title'] ?? ''),
            'seo_description' => validate_data($_POST['seo_description'] ?? ''),
            'featured_image' => validate_data($_POST['featured_image'] ?? '')
        ];

        $postId = $this->postModel->savePost($postData, $metaData);

        if ($postId) {
            // Save Categories
            $catIds = array_map('intval', $_POST['categories'] ?? []);
            $this->taxonomyModel->setPostTaxonomies((int)$postId, $catIds, 'category');

            // Save Tags
            $tagsInput = $_POST['tags'] ?? '';
            $tagNames = array_filter(array_map('trim', explode(',', $tagsInput)));
            $tagIds = [];
            foreach ($tagNames as $name) {
                $slug = strtolower($name);
                $slug = preg_replace('/[^a-z0-9-]/', '', str_replace(' ', '-', $slug));
                $tag = $this->taxonomyModel->getTaxonomyBySlug($slug, 'tag');
                if ($tag) {
                    $tagIds[] = (int)$tag->id;
                } else {
                    $newTagId = $this->taxonomyModel->saveTaxonomy([
                        'name' => $name,
                        'slug' => $slug,
                        'type' => 'tag'
                    ]);
                    if ($newTagId) {
                        $tagIds[] = (int)$newTagId;
                    }
                }
            }
            $this->taxonomyModel->setPostTaxonomies((int)$postId, $tagIds, 'tag');

            flash('success_msg', 'Post saved successfully!', 'alert alert-success');
            echo json_encode(['status' => 'success', 'message' => 'Post saved successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save post']);
        }
        exit();
    }

    public function deletePost()
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $this->postModel->deletePost($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete post']);
        }
        exit();
    }

    // 4. Pages Management
    public function pages()
    {
        $pages = $this->postModel->getAllPosts('page');
        echo $this->view('admin/pages_list', ['title' => 'Manage Pages', 'pages' => $pages]);
    }

    public function pageForm(?string $id = null)
    {
        $page = null;
        $meta = [];

        if ($id !== null) {
            $page = $this->postModel->getPostById((int)$id);
            if (!$page || $page->type !== 'page') {
                redirect('admin/pages');
                exit();
            }
            $meta = $page->meta;
        }

        $data = [
            'title' => $page ? 'Edit Page' : 'Add New Page',
            'page' => $page,
            'meta' => $meta
        ];

        echo $this->view('admin/page_form', $data);
    }

    public function savePage()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : 0;

        $pageData = [
            'title' => validate_data($_POST['title'] ?? ''),
            'slug' => validate_data($_POST['slug'] ?? ''),
            'content' => $_POST['content'] ?? '',
            'type' => 'page',
            'status' => validate_data($_POST['status'] ?? 'draft'),
            'author_id' => $_SESSION['ICTM_Auth']['user_id']
        ];

        if ($id > 0) {
            $pageData['id'] = $id;
        }

        $metaData = [
            'seo_title' => validate_data($_POST['seo_title'] ?? ''),
            'seo_description' => validate_data($_POST['seo_description'] ?? ''),
            'featured_image' => validate_data($_POST['featured_image'] ?? ''),
            'page_layout' => validate_data($_POST['page_layout'] ?? 'full_width')
        ];

        $pageId = $this->postModel->savePost($pageData, $metaData);

        if ($pageId) {
            flash('success_msg', 'Page saved successfully!', 'alert alert-success');
            echo json_encode(['status' => 'success', 'message' => 'Page saved successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save page']);
        }
        exit();
    }

    public function deletePage()
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $this->postModel->deletePost($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete page']);
        }
        exit();
    }

    // 5. Taxonomy Management: Categories & Tags
    public function categories()
    {
        $categories = $this->taxonomyModel->getAllTaxonomies('category');
        echo $this->view('admin/categories', ['title' => 'Categories', 'categories' => $categories]);
    }

    public function saveCategory()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = validate_data($_POST['name'] ?? '');
        $slug = validate_data($_POST['slug'] ?? '');

        $data = [
            'name' => $name,
            'slug' => $slug,
            'type' => 'category'
        ];

        if ($id > 0) {
            $data['id'] = $id;
        }

        $res = $this->taxonomyModel->saveTaxonomy($data);
        if ($res) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'duplicate', 'message' => 'Duplicate slug found!']);
        }
        exit();
    }

    public function deleteCategory()
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $this->taxonomyModel->deleteTaxonomy($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete category']);
        }
        exit();
    }

    public function tags()
    {
        $tags = $this->taxonomyModel->getAllTaxonomies('tag');
        echo $this->view('admin/tags', ['title' => 'Tags', 'tags' => $tags]);
    }

    public function saveTag()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = validate_data($_POST['name'] ?? '');
        $slug = validate_data($_POST['slug'] ?? '');

        $data = [
            'name' => $name,
            'slug' => $slug,
            'type' => 'tag'
        ];

        if ($id > 0) {
            $data['id'] = $id;
        }

        $res = $this->taxonomyModel->saveTaxonomy($data);
        if ($res) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'duplicate', 'message' => 'Duplicate slug found!']);
        }
        exit();
    }

    public function deleteTag()
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $this->taxonomyModel->deleteTaxonomy($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete tag']);
        }
        exit();
    }

    // 6. Media Library
    public function media()
    {
        $media = $this->mediaModel->getAllMedia();

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            $formattedMedia = [];
            foreach ($media as $item) {
                $formattedMedia[] = [
                    'id' => $item['id'],
                    'original_name' => $item['original_name'],
                    'path' => pathto($item['path']),
                    'mime_type' => $item['mime_type']
                ];
            }
            echo json_encode($formattedMedia);
            exit();
        }

        echo $this->view('admin/media', ['title' => 'Media Library', 'media' => $media]);
    }

    public function uploadMedia()
    {
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            // YYYY/MM/DD directory structure under public_html/Writables/images/
            $subPath = 'Writables/images/' . date('Y/m/d') . '/';
            $uploadDir = ROOTPATH . 'public_html/' . $subPath;

            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }

            $originalName = $_FILES['file']['name'];
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'mp4'];
            if (!in_array(strtolower($ext), $allowedExtensions)) {
                echo json_encode(['status' => 'error', 'message' => 'Unsupported file type. Allowed extensions: ' . implode(', ', $allowedExtensions)]);
                exit();
            }

            $cleanName = preg_replace('/[^a-zA-Z0-9]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $filename = $cleanName . '_' . time() . '.' . $ext;
            $targetFile = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                $webPath = $subPath . $filename;

                // Get dimensions if it's an image
                $dimensions = '';
                if (str_starts_with($_FILES['file']['type'], 'image/')) {
                    $sizes = @getimagesize($targetFile);
                    if ($sizes) {
                        $dimensions = $sizes[0] . 'x' . $sizes[1];
                    }
                }

                $mediaId = $this->mediaModel->logMedia(
                    $originalName,
                    $webPath,
                    $_FILES['file']['type'],
                    $dimensions,
                    $_SESSION['ICTM_Auth']['user_id']
                );

                if ($mediaId) {
                    flash('success_msg', 'File uploaded successfully!', 'alert alert-success');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'File uploaded successfully',
                        'url' => pathto($webPath)
                    ]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to save to database']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No file uploaded or file upload error']);
        }
        exit();
    }

    public function deleteMedia()
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $this->mediaModel->deleteMedia($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete media record']);
        }
        exit();
    }

    // 7. Menus
    public function menus()
    {
        $menus = $this->menuModel->getMenus();
        $activeMenuId = (int)($_GET['menu'] ?? (isset($menus[0]['id']) ? $menus[0]['id'] : 0));

        $menuItems = [];
        $pages = [];
        $categories = [];

        if ($activeMenuId > 0) {
            $menuItems = $this->menuModel->getMenuItems($activeMenuId);
            $pages = $this->postModel->getAllPosts('page', 'published');
            $categories = $this->taxonomyModel->getAllTaxonomies('category');
        }

        $data = [
            'title' => 'Menu Manager',
            'menus' => $menus,
            'active_menu_id' => $activeMenuId,
            'menu_items' => $menuItems,
            'pages' => $pages,
            'categories' => $categories
        ];

        echo $this->view('admin/menus', $data);
    }

    public function saveMenu()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = validate_data($_POST['name'] ?? '');
        $location = isset($_POST['location']) ? validate_data($_POST['location']) : null;

        $data = [
            'name' => $name,
            'location' => !empty($location) ? $location : null
        ];

        if ($id > 0) {
            $data['id'] = $id;
        }

        $res = $this->menuModel->saveMenu($data);
        if ($res) {
            flash('success_msg', 'Menu saved successfully!', 'alert alert-success');
        } else {
            flash('error_msg', 'Failed to save menu.', 'alert alert-danger');
        }

        redirect('admin/menus?menu=' . ($id > 0 ? $id : $res));
        exit();
    }

    public function deleteMenu()
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $this->menuModel->deleteMenu($id)) {
            flash('success_msg', 'Menu deleted successfully!', 'alert alert-success');
        } else {
            flash('error_msg', 'Failed to delete menu.', 'alert alert-danger');
        }
        redirect('admin/menus');
        exit();
    }

    public function saveMenuItems()
    {
        $menuId = (int)($_POST['menu_id'] ?? 0);
        $itemsJson = $_POST['menu_items_data'] ?? '[]';
        $items = json_decode($itemsJson, true);

        if ($menuId > 0 && $this->menuModel->saveMenuItems($menuId, $items)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save menu items']);
        }
        exit();
    }

    // 8. Widgets & Sidebars
    public function widgets()
    {
        $this->checkAdminOnly();
        $settingsJson = $this->optionModel->getOption('widget_settings', '{"sidebar_widgets":[],"sidebar_layout":"right"}');
        $settings = json_decode($settingsJson, true);

        $pages = $this->postModel->getAllPosts('page', 'published');

        echo $this->view('admin/widgets', [
            'title' => 'Widgets & Sidebars',
            'settings' => $settings,
            'pages' => $pages
        ]);
    }

    public function saveWidgets()
    {
        $this->checkAdminOnly();

        $layout = validate_data($_POST['sidebar_layout'] ?? 'right');
        $widgets = $_POST['widgets'] ?? [];
        $order = $_POST['widgets_order'] ?? [];

        $orderedActiveWidgets = [];
        foreach ($order as $key) {
            $key = validate_data($key);
            if (in_array($key, $widgets, true)) {
                $orderedActiveWidgets[] = $key;
            }
        }
        if (empty($orderedActiveWidgets)) {
            $orderedActiveWidgets = $widgets;
        }

        $recent_limit = (int)($_POST['recent_posts_limit'] ?? 5);
        $trending_limit = (int)($_POST['trending_posts_limit'] ?? 5);

        $settings = [
            'sidebar_layout' => $layout,
            'sidebar_widgets' => $orderedActiveWidgets,
            'recent_posts_limit' => $recent_limit,
            'trending_posts_limit' => $trending_limit
        ];

        $this->optionModel->updateOption('widget_settings', json_encode($settings));
        flash('success_msg', 'Widgets and sidebars updated successfully!', 'alert alert-success');
        redirect('admin/widgets');
        exit();
    }

    // 9. Themes Management
    public function themes()
    {
        $this->checkAdminOnly();
        $themes = $this->extensionModel->find_all('themes');

        // Scan core templates in /app/views/themes and vendor extensions in /app/vendors/themes
        $vendorsDir = is_dir(APPPATH . 'vendors') ? APPPATH . 'vendors/' : APPPATH . 'Vendors/';
        $themesVendorDir = $vendorsDir . 'themes/';
        $themesCoreDir = APPPATH . 'Views/Themes/';
        $localThemes = [];
        $discoveredThemes = [];

        // 1. Scan core system templates in /app/views/themes
        if (is_dir($themesCoreDir)) {
            $dirs = array_diff(scandir($themesCoreDir), ['.', '..']);
            foreach ($dirs as $dir) {
                if (is_dir($themesCoreDir . $dir) && file_exists($themesCoreDir . $dir . '/manifest.json')) {
                    $discoveredThemes[$dir] = [
                        'path' => $themesCoreDir . $dir,
                        'is_system' => true
                    ];
                }
            }
        }

        $adminThemeDir = APPPATH . 'Views/admin';
        if (is_dir($adminThemeDir) && file_exists($adminThemeDir . '/manifest.json')) {
            $discoveredThemes['admin'] = [
                'path' => $adminThemeDir,
                'is_system' => true
            ];
        }

        // 2. Scan vendor extension themes in /app/vendors/themes
        if (is_dir($themesVendorDir)) {
            $dirs = array_diff(scandir($themesVendorDir), ['.', '..']);
            foreach ($dirs as $dir) {
                if (is_dir($themesVendorDir . $dir) && file_exists($themesVendorDir . $dir . '/manifest.json')) {
                    $discoveredThemes[$dir] = [
                        'path' => $themesVendorDir . $dir,
                        'is_system' => false
                    ];
                }
            }
        }

        foreach ($discoveredThemes as $themeKey => $info) {
            $manifest = json_decode(file_get_contents($info['path'] . '/manifest.json'), true);
            if ($manifest) {
                $scope = $manifest['scope'] ?? 'frontend';
                $localThemes[] = [
                    'name' => $manifest['name'],
                    'version' => $manifest['version'],
                    'author' => $manifest['author'] ?? 'Unknown',
                    'scope' => $scope,
                    'is_system' => $info['is_system'],
                    'is_active' => ($scope === 'frontend')
                        ? ($this->optionModel->getOption('frontend_theme') === $manifest['name'] ? 1 : 0)
                        : ($this->optionModel->getOption('backend_theme') === $manifest['name'] ? 1 : 0)
                ];
            }
        }

        echo $this->view('admin/themes', [
            'title' => 'Theme Manager',
            'themes' => $localThemes
        ]);
    }

    public function uploadTheme()
    {
        $this->checkAdminOnly();

        if (isset($_FILES['theme_zip']) && $_FILES['theme_zip']['error'] === UPLOAD_ERR_OK) {
            $zipPath = $_FILES['theme_zip']['tmp_name'];
            $res = $this->extensionModel->installExtension($zipPath, 'theme');

            if ($res['status'] === 'success') {
                flash('success_msg', $res['message'], 'alert alert-success');
            } else {
                flash('error_msg', $res['message'], 'alert alert-danger');
            }
        } else {
            flash('error_msg', 'File upload error.', 'alert alert-danger');
        }

        redirect('admin/themes');
        exit();
    }

    public function activateTheme(string $name, string $scope)
    {
        $this->checkAdminOnly();
        $name = validate_data($name);
        $scope = validate_data($scope);

        if ($this->extensionModel->activateTheme($name, $scope)) {
            flash('success_msg', 'Theme activated successfully!', 'alert alert-success');
        } else {
            flash('error_msg', 'Failed to activate theme.', 'alert alert-danger');
        }

        redirect('admin/themes');
        exit();
    }

    public function deleteTheme()
    {
        $this->checkAdminOnly();
        $name = validate_data($_POST['name'] ?? '');

        // Core system templates are strictly restricted to /app/views/themes and protected from deletion
        if ($name === 'classic' || $name === 'admin' || file_exists(APPPATH . 'Views/Themes/' . $name . '/manifest.json')) {
            flash('error_msg', 'The ' . $name . ' theme is a core system template and cannot be deleted.', 'alert alert-warning');
            redirect('admin/themes');
            exit();
        }

        if (!empty($name) && $this->extensionModel->deleteExtension($name, 'theme')) {
            flash('success_msg', 'Theme deleted successfully!', 'alert alert-success');
        } else {
            flash('error_msg', 'Failed to delete theme.', 'alert alert-danger');
        }

        redirect('admin/themes');
        exit();
    }

    // 10. Modules Management
    public function modules()
    {
        $this->checkAdminOnly();

        $vendorsDir = is_dir(APPPATH . 'vendors') ? APPPATH . 'vendors/' : APPPATH . 'Vendors/';
        $modulesVendorDir = $vendorsDir . 'modules/';
        $modulesLegacyDir = APPPATH . 'Modules/';
        $localModules = [];
        $scannedModules = [];

        // 1. Scan /app/vendors/modules for custom modules
        if (is_dir($modulesVendorDir)) {
            $dirs = array_diff(scandir($modulesVendorDir), ['.', '..']);
            foreach ($dirs as $dir) {
                if (is_dir($modulesVendorDir . $dir) && file_exists($modulesVendorDir . $dir . '/manifest.json')) {
                    $scannedModules[$dir] = [
                        'path' => $modulesVendorDir . $dir,
                        'source' => 'vendor'
                    ];
                }
            }
        }

        // 2. Scan legacy modules path if present
        if (is_dir($modulesLegacyDir)) {
            $dirs = array_diff(scandir($modulesLegacyDir), ['.', '..']);
            foreach ($dirs as $dir) {
                if (is_dir($modulesLegacyDir . $dir) && !isset($scannedModules[$dir]) && file_exists($modulesLegacyDir . $dir . '/manifest.json')) {
                    $scannedModules[$dir] = [
                        'path' => $modulesLegacyDir . $dir,
                        'source' => 'legacy'
                    ];
                }
            }
        }

        foreach ($scannedModules as $modKey => $info) {
            $manifest = json_decode(file_get_contents($info['path'] . '/manifest.json'), true);
            if ($manifest) {
                $dbReg = $this->extensionModel->find_single('modules', null, '', [['name', '=', $manifest['name']]]);
                $isActive = $dbReg ? (int)$dbReg->is_active : 0;

                $localModules[] = [
                    'name' => $manifest['name'],
                    'version' => $manifest['version'],
                    'author' => $manifest['author'] ?? 'Unknown',
                    'description' => $manifest['description'] ?? '',
                    'is_active' => $isActive,
                    'source' => $info['source']
                ];
            }
        }

        echo $this->view('admin/modules', [
            'title' => 'Module Manager',
            'modules' => $localModules
        ]);
    }

    public function uploadModule()
    {
        $this->checkAdminOnly();

        if (isset($_FILES['module_zip']) && $_FILES['module_zip']['error'] === UPLOAD_ERR_OK) {
            $zipPath = $_FILES['module_zip']['tmp_name'];
            $res = $this->extensionModel->installExtension($zipPath, 'module');

            if ($res['status'] === 'success') {
                flash('success_msg', $res['message'], 'alert alert-success');
            } else {
                flash('error_msg', $res['message'], 'alert alert-danger');
            }
        } else {
            flash('error_msg', 'File upload error.', 'alert alert-danger');
        }

        redirect('admin/modules');
        exit();
    }

    public function toggleModule(string $name)
    {
        $this->checkAdminOnly();
        $name = validate_data($name);

        if ($this->extensionModel->toggleModule($name)) {
            flash('success_msg', 'Module toggled successfully!', 'alert alert-success');
        } else {
            flash('error_msg', 'Failed to toggle module.', 'alert alert-danger');
        }

        redirect('admin/modules');
        exit();
    }

    public function deleteModule()
    {
        $this->checkAdminOnly();
        $name = validate_data($_POST['name'] ?? '');

        if (!empty($name) && $this->extensionModel->deleteExtension($name, 'module')) {
            flash('success_msg', 'Module deleted successfully!', 'alert alert-success');
        } else {
            flash('error_msg', 'Failed to delete module.', 'alert alert-danger');
        }

        redirect('admin/modules');
        exit();
    }

    // 11. Users Management
    public function users()
    {
        $this->checkAdminOnly();
        $users = $this->userModel->getAllUsers();
        echo $this->view('admin/users', ['title' => 'User Management', 'users' => $users]);
    }

    public function saveUser()
    {
        $this->checkAdminOnly();

        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $username = validate_data($_POST['username'] ?? '');
        $email = validate_data($_POST['email'] ?? '');
        $role = validate_data($_POST['role'] ?? 'subscriber');
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        $password = $_POST['password'] ?? '';

        $data = [
            'username' => $username,
            'email' => $email,
            'role' => $role,
            'status' => $status
        ];

        if (!empty($password)) {
            $data['password'] = $password;
        }

        if ($id > 0) {
            // Prevent self-deactivation or self-role change for current logged-in admin
            if ($id === $_SESSION['ICTM_Auth']['user_id']) {
                $data['role'] = 'admin';
                $data['status'] = 1;
            }
            $this->userModel->updateUser($id, $data);
            flash('success_msg', 'User updated successfully!', 'alert alert-success');
        } else {
            if (empty($password)) {
                flash('error_msg', 'Password is required for new users.', 'alert alert-danger');
                redirect('admin/users');
                exit();
            }
            $this->userModel->createUser($data);
            flash('success_msg', 'User created successfully!', 'alert alert-success');
        }

        redirect('admin/users');
        exit();
    }

    public function deleteUser()
    {
        $this->checkAdminOnly();
        $id = (int)($_POST['id'] ?? 0);

        if ($id === $_SESSION['ICTM_Auth']['user_id']) {
            echo json_encode(['status' => 'error', 'message' => 'You cannot delete yourself!']);
            exit();
        }

        if ($id > 0 && $this->userModel->deleteUser($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete user']);
        }
        exit();
    }

    // 12. CPT Configurations (Metadata fields builder)
    public function cpts()
    {
        $this->checkAdminOnly();
        $cptsJson = $this->optionModel->getOption('custom_post_types', '[]');
        $cpts = json_decode($cptsJson, true);

        echo $this->view('admin/cpts', [
            'title' => 'Custom Post Types',
            'cpts' => $cpts
        ]);
    }

    public function saveCpt()
    {
        $this->checkAdminOnly();

        $name = validate_data($_POST['name'] ?? '');
        $label = validate_data($_POST['label'] ?? '');
        $description = validate_data($_POST['description'] ?? '');
        $fieldsJson = $_POST['fields_data'] ?? '[]';
        $fields = json_decode($fieldsJson, true);

        $cptsJson = $this->optionModel->getOption('custom_post_types', '[]');
        $cpts = json_decode($cptsJson, true);

        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9-]/', '', str_replace(' ', '-', $slug));

        $cpts[$slug] = [
            'name' => $slug,
            'label' => $label,
            'description' => $description,
            'fields' => $fields
        ];

        $this->optionModel->updateOption('custom_post_types', json_encode($cpts));
        flash('success_msg', 'Custom Post Type saved successfully!', 'alert alert-success');
        redirect('admin/cpts');
        exit();
    }

    public function deleteCpt()
    {
        $this->checkAdminOnly();
        $slug = validate_data($_POST['slug'] ?? '');

        if (!empty($slug)) {
            $cptsJson = $this->optionModel->getOption('custom_post_types', '[]');
            $cpts = json_decode($cptsJson, true);
            if (isset($cpts[$slug])) {
                unset($cpts[$slug]);
                $this->optionModel->updateOption('custom_post_types', json_encode($cpts));
                // Delete all entries of this type
                $this->db->query("DELETE FROM posts WHERE type = ?", [$slug]);
                echo json_encode(['status' => 'success']);
                exit();
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete CPT']);
        exit();
    }

    // Dynamic CPT Entry Controllers
    public function cptEntries(string $type)
    {
        $type = validate_data($type);
        $cptsJson = $this->optionModel->getOption('custom_post_types', '[]');
        $cpts = json_decode($cptsJson, true);

        if (!isset($cpts[$type])) {
            redirect('admin/dashboard');
            exit();
        }

        $entries = $this->postModel->getAllPosts($type);

        echo $this->view('admin/cpt_entries', [
            'title' => $cpts[$type]['label'],
            'cpt' => $cpts[$type],
            'entries' => $entries
        ]);
    }

    public function cptEntryForm(string $type, ?string $id = null)
    {
        $type = validate_data($type);
        $cptsJson = $this->optionModel->getOption('custom_post_types', '[]');
        $cpts = json_decode($cptsJson, true);

        if (!isset($cpts[$type])) {
            redirect('admin/dashboard');
            exit();
        }

        $entry = null;
        $meta = [];
        if ($id !== null) {
            $entry = $this->postModel->getPostById((int)$id);
            if (!$entry || $entry->type !== $type) {
                redirect('admin/cpt/entries/' . $type);
                exit();
            }
            $meta = $entry->meta;
        }

        echo $this->view('admin/cpt_entry_form', [
            'title' => $entry ? 'Edit ' . $cpts[$type]['label'] : 'Add ' . $cpts[$type]['label'],
            'cpt' => $cpts[$type],
            'entry' => $entry,
            'meta' => $meta
        ]);
    }

    public function saveCptEntry()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $type = validate_data($_POST['type'] ?? '');

        $cptsJson = $this->optionModel->getOption('custom_post_types', '[]');
        $cpts = json_decode($cptsJson, true);

        if (!isset($cpts[$type])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Custom Post Type']);
            exit();
        }

        $entryData = [
            'title' => validate_data($_POST['title'] ?? ''),
            'slug' => validate_data($_POST['slug'] ?? ''),
            'content' => $_POST['content'] ?? '',
            'type' => $type,
            'status' => validate_data($_POST['status'] ?? 'draft'),
            'author_id' => $_SESSION['ICTM_Auth']['user_id']
        ];

        if ($id > 0) {
            $entryData['id'] = $id;
        }

        // Gather CPT fields meta
        $metaData = [];
        foreach ($cpts[$type]['fields'] as $field) {
            $fKey = $field['name'];
            $metaData[$fKey] = $_POST[$fKey] ?? '';
        }

        $entryId = $this->postModel->savePost($entryData, $metaData);

        if ($entryId) {
            flash('success_msg', $cpts[$type]['label'] . ' entry saved successfully!', 'alert alert-success');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save CPT entry']);
        }
        exit();
    }

    public function deleteCptEntry()
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $this->postModel->deletePost($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete CPT entry']);
        }
        exit();
    }

    // 13. System Notifications
    public function notifications()
    {
        $this->checkAdminOnly();

        $data = [
            'title' => 'Email SMTP Configurations',
            'smtp_host' => $this->optionModel->getOption('smtp_host'),
            'smtp_user' => $this->optionModel->getOption('smtp_user'),
            'smtp_pass' => $this->optionModel->getOption('smtp_pass'),
            'smtp_port' => $this->optionModel->getOption('smtp_port', '587'),
            'smtp_encryption' => $this->optionModel->getOption('smtp_encryption', 'tls'),
            'notify_triggers' => json_decode($this->optionModel->getOption('notify_triggers', '[]'), true)
        ];

        echo $this->view('admin/notifications', $data);
    }

    public function saveNotifications()
    {
        $this->checkAdminOnly();

        $this->optionModel->updateOption('smtp_host', validate_data($_POST['smtp_host'] ?? ''));
        $this->optionModel->updateOption('smtp_user', validate_data($_POST['smtp_user'] ?? ''));
        $this->optionModel->updateOption('smtp_pass', $_POST['smtp_pass'] ?? '');
        $this->optionModel->updateOption('smtp_port', validate_data($_POST['smtp_port'] ?? '587'));
        $this->optionModel->updateOption('smtp_encryption', validate_data($_POST['smtp_encryption'] ?? 'tls'));

        $triggers = $_POST['notify_triggers'] ?? [];
        $this->optionModel->updateOption('notify_triggers', json_encode($triggers));

        flash('success_msg', 'Email configurations updated successfully!', 'alert alert-success');
        redirect('admin/notifications');
        exit();
    }

    // 14. Import / Export
    public function importExport()
    {
        $this->checkAdminOnly();
        echo $this->view('admin/import_export', ['title' => 'Import & Export']);
    }

    public function exportDatabase()
    {
        $this->checkAdminOnly();

        $tables = ['site_options', 'users', 'posts', 'post_meta', 'taxonomies', 'post_taxonomies', 'comments', 'media_library', 'menus', 'menu_items', 'modules', 'themes'];
        $sqlDump = "-- CMsys Database Export\n-- Generated on: " . now() . "\n\nSET FOREIGN_KEY_CHECKS=0;\n";

        $db = db();

        // Use PHP Reflection to access the private connection property of the QueryBuilder class
        $reflection = new \ReflectionClass($db);
        $connProp = $reflection->getProperty('conn');
        $connProp->setAccessible(true);
        $conn = $connProp->getValue($db);

        foreach ($tables as $table) {
            $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";

            // Get structure
            $showCreate = $db->query("SHOW CREATE TABLE `$table`");
            if (is_array($showCreate) && !empty($showCreate)) {
                $sqlDump .= $showCreate[0]['Create Table'] . ";\n\n";
            }

            // Get data
            $db->table($table)->select('*');
            $data = $db->get();
            foreach ($data as $row) {
                $cols = implode("`, `", array_keys($row));
                $vals = array_map(function ($v) use ($conn) {
                    if ($v === null) {
                        return 'NULL';
                    }
                    if (is_int($v) || is_float($v)) {
                        return $v;
                    }
                    return "'" . $conn->real_escape_string((string)$v) . "'";
                }, array_values($row));
                $valsStr = implode(", ", $vals);
                $sqlDump .= "INSERT INTO `$table` (`$cols`) VALUES ($valsStr);\n";
            }
            $sqlDump .= "\n";
        }
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="cmsys_backup_' . time() . '.sql"');
        echo $sqlDump;
        exit();
    }

    public function importDatabase()
    {
        $this->checkAdminOnly();

        if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
            $sql = file_get_contents($_FILES['import_file']['tmp_name']);

            // Strip SQL comments and empty lines
            $lines = explode("\n", $sql);
            $cleanLines = [];
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed === '' || strpos($trimmed, '--') === 0 || strpos($trimmed, '#') === 0) {
                    continue;
                }
                $cleanLines[] = $line;
            }
            $sql = implode("\n", $cleanLines);

            // Parse and split queries safely by semicolon, honoring quoted strings and escapes
            $queries = [];
            $query = '';
            $inString = false;
            $stringChar = '';
            $escaped = false;
            $length = strlen($sql);

            for ($i = 0; $i < $length; $i++) {
                $char = $sql[$i];

                if ($escaped) {
                    $query .= $char;
                    $escaped = false;
                    continue;
                }

                if ($char === '\\') {
                    $query .= $char;
                    $escaped = true;
                    continue;
                }

                if ($inString) {
                    $query .= $char;
                    if ($char === $stringChar) {
                        $inString = false;
                    }
                } else {
                    if ($char === "'" || $char === '"') {
                        $inString = true;
                        $stringChar = $char;
                        $query .= $char;
                    } elseif ($char === ';') {
                        $queries[] = $query;
                        $query = '';
                    } else {
                        $query .= $char;
                    }
                }
            }
            if (trim($query) !== '') {
                $queries[] = $query;
            }

            $db = db();
            $db->query("SET FOREIGN_KEY_CHECKS=0");
            $success = 0;
            $failed = 0;

            foreach ($queries as $q) {
                $q = trim($q);
                if (!empty($q)) {
                    if ($db->query($q)) {
                        $success++;
                    } else {
                        $failed++;
                    }
                }
            }
            $db->query("SET FOREIGN_KEY_CHECKS=1");

            flash('success_msg', "Import completed! Successfully executed queries: $success. Errors: $failed", 'alert alert-success');
        } else {
            flash('error_msg', 'Please upload a valid SQL file.', 'alert alert-danger');
        }

        redirect('admin/import-export');
        exit();
    }

    // 15. Ajax updateStatusApi for quick toggles
    public function updateStatusApi()
    {
        $id = (int)($_POST['id'] ?? 0);
        $action = validate_data($_POST['action'] ?? '');
        $status = validate_data($_POST['status'] ?? '');

        if ($id <= 0 || empty($action)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
            exit();
        }

        $success = false;
        if ($action === 'post_status') {
            $success = $this->postModel->update('posts', ['status' => $status], $id);
        } elseif ($action === 'comment_status') {
            $success = $this->commentModel->updateCommentStatus($id, $status);
        } elseif ($action === 'user_status') {
            // Protect current logged-in user
            if ($id === $_SESSION['ICTM_Auth']['user_id']) {
                echo json_encode(['status' => 'error', 'message' => 'You cannot change your own status']);
                exit();
            }
            $success = $this->userModel->updateUser($id, ['status' => (int)$status]);
        }

        if ($success) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }
        exit();
    }

    // 16. Custom Page Editor (Block-builder)
    public function pageEditor(string $id)
    {
        $page = $this->postModel->getPostById((int)$id);
        if (!$page || $page->type !== 'page') {
            redirect('admin/pages');
            exit();
        }

        $blocksJson = $this->postModel->getSingleMeta((int)$id, 'page_blocks', '[]');
        $blocks = json_decode($blocksJson, true);

        echo $this->view('admin/page_editor', [
            'title' => 'Page Block Builder: ' . $page->title,
            'page' => $page,
            'blocks' => $blocks
        ]);
    }

    public function savePageEditor()
    {
        $id = (int)($_POST['page_id'] ?? 0);
        $blocksJson = $_POST['blocks_data'] ?? '[]';

        if ($id > 0) {
            $this->postModel->savePostMeta($id, ['page_blocks' => $blocksJson]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid page ID']);
        }
        exit();
    }
    public function customizeTheme(string $name)
    {
        $this->checkAdminOnly();
        $name = validate_data($name);

        $configKey = "theme_{$name}_config";
        $configJson = $this->optionModel->getOption($configKey, '{}');
        $config = json_decode($configJson, true);

        $data = [
            'title' => 'Customize Theme: ' . ucfirst($name),
            'theme_name' => $name,
            'config' => is_array($config) ? $config : []
        ];

        $data = apply_filters('admin_theme_customize_data', $data, $name);
        $viewFile = apply_filters('admin_theme_customize_view', 'admin/theme_customize', $name);

        echo $this->view($viewFile, $data);
    }

    public function saveThemeCustomization(string $name)
    {
        $this->checkAdminOnly();
        $name = validate_data($name);

        $primaryColor = validate_data($_POST['primary_color'] ?? '');
        $secondaryColor = validate_data($_POST['secondary_color'] ?? '');
        $customCss = $_POST['custom_css'] ?? '';
        $stickyHeader = isset($_POST['sticky_header']) ? '1' : '0';

        // Custom Styling & Scripts
        $customCssCode  = $_POST['custom_css_code']   ?? '';
        $headerScripts  = $_POST['header_scripts']     ?? '';
        $footerScripts  = $_POST['footer_scripts']     ?? '';

        $config = [
            'primary_color'   => $primaryColor,
            'secondary_color' => $secondaryColor,
            'custom_css'      => $customCss,
            'sticky_header'   => $stickyHeader,
            'custom_css_code' => $customCssCode,
            'header_scripts'  => $headerScripts,
            'footer_scripts'  => $footerScripts,
        ];

        $config = apply_filters('admin_theme_customize_save_config', $config, $name);

        $configKey = "theme_{$name}_config";
        $this->optionModel->updateOption($configKey, json_encode($config));

        do_action('admin_theme_customize_saved', $name, $config);

        flash('success_msg', 'Theme customization saved successfully!', 'alert alert-success');
        redirect('admin/theme/customize/' . $name);
        exit();
    }
}
