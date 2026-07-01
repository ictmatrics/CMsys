<?php
declare(strict_types=1);

namespace App\Controllers;

use System\Config\Controller;
use App\Models\OptionModel;
use App\Models\PostModel;
use App\Models\TaxonomyModel;
use App\Models\MenuModel;

class HomeController extends Controller
{
    private OptionModel $optionModel;
    private PostModel $postModel;
    private TaxonomyModel $taxonomyModel;
    private MenuModel $menuModel;

    public function __construct()
    {
        $this->optionModel = new OptionModel();
        $this->postModel = new PostModel();
        $this->taxonomyModel = new TaxonomyModel();
        $this->menuModel = new MenuModel();
        
        $this->checkMaintenance();
    }

    private function getLayoutData(): array
    {
        $theme = $this->optionModel->getOption('frontend_theme', 'classic');

        // Main Navigation Menu
        $mainMenu = $this->menuModel->getMenuByLocation('main');
        $mainMenuItems = $mainMenu ? $this->menuModel->getMenuItems((int)$mainMenu->id) : [];

        // Footer Navigation Menu
        $footerMenu = $this->menuModel->getMenuByLocation('footer');
        $footerMenuItems = $footerMenu ? $this->menuModel->getMenuItems((int)$footerMenu->id) : [];

        // Widgets
        $widgetJson = $this->optionModel->getOption('widget_settings', '{"sidebar_widgets":[],"sidebar_layout":"right"}');
        $widgetSettings = json_decode($widgetJson, true);

        $categories = $this->taxonomyModel->getAllTaxonomies('category');
        $tags = $this->taxonomyModel->getAllTaxonomies('tag');
        $recentPosts = $this->postModel->getRecentPublishedPosts(5);
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
            'widget_pages' => $pages
        ];
    }

    public function index()
    {
        // Check if database is installed — query() returns array|bool directly
        try {
            $rows = db()->query("SHOW TABLES LIKE 'site_options'");
            if (empty($rows)) {
                redirect('install');
                exit();
            }
        } catch (\Throwable $e) {
            redirect('install');
            exit();
        }

        $data = $this->getLayoutData();
        
        // Fetch posts for the homepage loop
        $posts = $this->postModel->getPublishedPosts();
        $data['posts'] = $posts;
        $data['title'] = 'Home';

        // Check if theme home exists
        $themeHome = 'Themes/' . $data['theme_name'] . '/home';
        
        echo $this->view($themeHome, $data);
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
