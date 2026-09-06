<?php
declare(strict_types=1);

if (!function_exists('ntech_copy_directory')) {
    function ntech_copy_directory(string $source, string $destination): void
    {
        $source = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $source), DIRECTORY_SEPARATOR);
        $destination = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $destination), DIRECTORY_SEPARATOR);

        if (!is_dir($source)) {
            return;
        }

        if (!is_dir($destination)) {
            @mkdir($destination, 0755, true);
        }

        $dir = @opendir($source);
        if ($dir) {
            while (($file = readdir($dir)) !== false) {
                if ($file !== '.' && $file !== '..') {
                    $srcFile = $source . DIRECTORY_SEPARATOR . $file;
                    $dstFile = $destination . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($srcFile)) {
                        ntech_copy_directory($srcFile, $dstFile);
                    } else {
                        @copy($srcFile, $dstFile);
                    }
                }
            }
            closedir($dir);
        }
    }
}

if (!function_exists('ntech_normalize_menu_url')) {
    function ntech_normalize_menu_url(string $url): string
    {
        $url = trim($url);
        if (empty($url) || $url === '#' || $url === 'index.html' || $url === 'index.php') {
            return pathto('');
        }

        if (defined('BASE_URL') && str_starts_with($url, BASE_URL)) {
            $url = substr($url, strlen(BASE_URL));
        } elseif (preg_match('#^https?://[^/]+(.*)$#i', $url, $m)) {
            if (!str_contains($m[0], 'cmsys.wis') && !str_contains($m[0], 'localhost')) {
                return $url;
            }
            $url = $m[1];
        }

        $url = ltrim($url, '/');
        if (str_ends_with($url, '.html')) {
            $url = substr($url, 0, -5);
        }
        if ($url === 'index' || $url === 'home') {
            return pathto('');
        }
        if ($url === 'service') {
            $url = 'services';
        }
        if ($url === 'product') {
            $url = 'products';
        }

        return pathto($url);
    }
}

// Ensure default homepage option
try {
    $optModel = new \App\Models\OptionModel();
    if (empty($optModel->getOption('show_on_front'))) {
        $optModel->updateOption('show_on_front', 'posts');
    }
} catch (\Throwable $e) {
    // Silently ignore if DB connection unavailable during early bootstrap
}

if (function_exists('add_filter')) {
    // 1. Override customize view file to load Themes/n-tech/customizer
    add_filter('admin_theme_customize_view', function(string $view, string $name): string {
        if ($name === 'n-tech') {
            return 'Themes/n-tech/customizer';
        }
        return $view;
    }, 10, 2);

    // 2. Load supplemental data for customizer
    add_filter('admin_theme_customize_data', function(array $data, string $name): array {
        if ($name === 'n-tech') {
            $postModel = new \App\Models\PostModel();
            $faqModel = new \App\Models\FaqModel();
            $taxonomyModel = new \App\Models\TaxonomyModel();

            $data['posts_list'] = $postModel->getAllPosts('post');
            $data['products_list'] = $postModel->getAllPosts('product');
            $data['services_list'] = $postModel->getAllPosts('service');
            $data['faqs_list'] = $faqModel->getAllFaqs();
            $data['categories_list'] = $taxonomyModel->getAllTaxonomies('category');
        }
        return $data;
    }, 10, 2);

    // 3. Save configuration for N-Tech customizer
    add_filter('admin_theme_customize_save_config', function(array $config, string $name): array {
        if ($name === 'n-tech') {
            $postedCustomCss = $_POST['custom_css'] ?? '';
            $rawUserCss = $_POST['ntech_raw_custom_css'] ?? $postedCustomCss;

            $ntechConfig = [];
            if (preg_match('/\*NTECH_CONFIG:(.*?):END_NTECH_CONFIG\*/s', $postedCustomCss, $matches)) {
                $ntechConfig = json_decode($matches[1], true) ?: [];
            } elseif (preg_match('/\*NTECH_CONFIG:(.*?):END_NTECH_CONFIG\*/s', $rawUserCss, $matches)) {
                $ntechConfig = json_decode($matches[1], true) ?: [];
            }

            $cleanUserCss = trim(preg_replace('/\*NTECH_CONFIG:(.*?):END_NTECH_CONFIG\*/s', '', $rawUserCss));
            $cleanUserCss = stripslashes($cleanUserCss);

            $fields = [
                'header_layout', 'footer_layout', 'get_in_touch_layout',
                'heading_font', 'body_font', 'primary_color', 'secondary_color', 'bg_color',
                'hero_title_1', 'hero_subtitle_1', 'hero_btn_text_1', 'hero_btn_link_1', 'hero_image_1',
                'hero_title_2', 'hero_subtitle_2', 'hero_btn_text_2', 'hero_btn_link_2', 'hero_image_2',
                'hero_title_3', 'hero_subtitle_3', 'hero_btn_text_3', 'hero_btn_link_3', 'hero_image_3',
                'about_heading', 'about_lead', 'about_description', 'about_mission', 'about_stat1_num', 'about_stat1_label', 'about_stat2_num', 'about_stat2_label',
                'services_title', 'services_subtitle', 'services_limit',
                'logo_slider_title', 'logo_slider_enable', 'logo_1', 'logo_2', 'logo_3', 'logo_4', 'logo_5', 'logo_6',
                'contact_email', 'contact_phone', 'contact_address', 'contact_map_url',
                'newsletter_title', 'newsletter_subtitle', 'newsletter_action',
                'show_breadcrumbs', 'wow_animations', 'parallax_hero'
            ];

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $val = $_POST[$field];
                    $config[$field] = is_array($val) ? $val : validate_data((string)$val);
                    $ntechConfig[$field] = $config[$field];
                } elseif (array_key_exists($field, $ntechConfig)) {
                    $val = $ntechConfig[$field];
                    $config[$field] = is_array($val) ? $val : validate_data((string)$val);
                    $ntechConfig[$field] = $config[$field];
                } else {
                    $config[$field] = '';
                    $ntechConfig[$field] = '';
                }
            }

            $config['custom_css'] = $cleanUserCss . ($cleanUserCss !== '' ? "\n" : "") . '/*NTECH_CONFIG:' . json_encode($ntechConfig) . ':END_NTECH_CONFIG*/';
        }
        return $config;
    }, 10, 2);
}
