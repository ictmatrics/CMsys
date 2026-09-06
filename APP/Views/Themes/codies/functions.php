<?php
declare(strict_types=1);

if (function_exists('add_filter')) {
    // 1. Override the customize view file to load admin/codies_theme_customize instead of the default
    add_filter('admin_theme_customize_view', function(string $view, string $name): string {
        if ($name === 'codies') {
            return 'admin/codies_theme_customize';
        }
        return $view;
    }, 10, 2);

    // 2. Load the list of published posts in layout customized data so we can select a Featured Post
    add_filter('admin_theme_customize_data', function(array $data, string $name): array {
        if ($name === 'codies') {
            $postModel = new \App\Models\PostModel();
            // Get all published posts using query builder
            $data['posts_list'] = $postModel->find_all('posts', '', [
                ['status', '=', 'published'],
                ['type', '=', 'post']
            ]);
            
            // Also load categories for carousel filtering if needed
            $taxonomyModel = new \App\Models\TaxonomyModel();
            $data['categories_list'] = $taxonomyModel->getAllTaxonomies('category');
        }
        return $data;
    }, 10, 2);

    // 3. Intercept theme save action configuration to record all our custom fields.
    //    The Codies customizer JS serializes all settings into a JSON comment block
    //    embedded inside custom_css: /*CODIES_CONFIG:{...}:END_CODIES_CONFIG*/
    //    We extract and merge those values directly into the config array.
    add_filter('admin_theme_customize_save_config', function(array $config, string $name): array {
        if ($name === 'codies') {
            $postedCustomCss = $_POST['custom_css'] ?? '';
            $rawUserCss = $_POST['codies_raw_custom_css'] ?? $postedCustomCss;

            // Extract the JSON config blob embedded by JS submit handler
            $codiesConfig = [];
            if (preg_match('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', $postedCustomCss, $matches)) {
                $codiesConfig = json_decode($matches[1], true) ?: [];
            } elseif (preg_match('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', $rawUserCss, $matches)) {
                $codiesConfig = json_decode($matches[1], true) ?: [];
            }

            // Clean custom CSS for saving
            $cleanUserCss = trim(preg_replace('/\*CODIES_CONFIG:(.*?):END_CODIES_CONFIG\*/s', '', $rawUserCss));
            $cleanUserCss = stripslashes($cleanUserCss);

            // Fields managed by the Codies customizer
            $fields = [
                'header_layout', 'footer_layout', 'heading_font', 'body_font',
                'bg_color', 'card_bg_color', 'border_color', 'default_color_mode',
                'parallax_hero', 'wow_animations', 'reading_progress', 'table_of_contents',

                // Carousel Settings
                'carousel_mode', 'carousel_category',
                'slide1_img', 'slide1_title', 'slide1_badge', 'slide1_lead', 'slide1_link', 'slide1_readtime',
                'slide2_img', 'slide2_title', 'slide2_badge', 'slide2_lead', 'slide2_link', 'slide2_readtime',
                'slide3_img', 'slide3_title', 'slide3_badge', 'slide3_lead', 'slide3_link', 'slide3_readtime',

                // Ad Banners Settings
                'ad_leaderboard_img', 'ad_leaderboard_link', 'ad_leaderboard_alt', 'ad_leaderboard_enable', 'ad_leaderboard_html',
                'ad_sidebar_img', 'ad_sidebar_link', 'ad_sidebar_alt', 'ad_sidebar_enable', 'ad_sidebar_html',
                'ad_footer_img', 'ad_footer_link', 'ad_footer_alt', 'ad_footer_enable', 'ad_footer_html',

                // Footer Custom Texts & Social Links
                'footer_about_text', 'footer_copyright', 'footer_community_title', 'footer_community_desc',
                'footer_github', 'footer_discord', 'footer_twitter', 'footer_facebook', 'footer_youtube', 'footer_tiktok',

                // Featured Post
                'featured_post_id',
            ];

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $val = $_POST[$field];
                    $config[$field] = is_array($val) ? $val : validate_data($val);
                    $codiesConfig[$field] = $config[$field];
                } elseif (array_key_exists($field, $codiesConfig)) {
                    $val = $codiesConfig[$field];
                    $config[$field] = is_array($val) ? $val : validate_data((string)$val);
                    $codiesConfig[$field] = $config[$field];
                } else {
                    $config[$field] = '';
                    $codiesConfig[$field] = '';
                }
            }

            // Reconstruct clean custom_css with updated JSON config blob
            $config['custom_css'] = $cleanUserCss . ($cleanUserCss !== '' ? "\n" : "") . '/*CODIES_CONFIG:' . json_encode($codiesConfig) . ':END_CODIES_CONFIG*/';
        }
        return $config;
    }, 10, 2);
}

if (!function_exists('calculate_read_time')) {
    /**
     * Calculate the estimated reading time for a block of HTML or plain-text content.
     *
     * Uses the industry-standard average adult reading speed of 200 words per minute.
     * Strips all HTML tags before counting to avoid counting markup as words.
     * Returns a human-readable string like "3 min read" with a minimum of 1 minute.
     *
     * @param  string $content  Raw HTML or plain-text post content.
     * @param  int    $wpm      Words per minute reading speed (default: 200).
     * @return string           Formatted reading time, e.g. "5 min read".
     */
    function calculate_read_time(string $content, int $wpm = 200): string
    {
        // Strip HTML tags so only visible text words are counted
        $text = strip_tags($content);

        // Collapse all whitespace (newlines, tabs, multiple spaces) into single spaces
        $text = preg_replace('/\s+/', ' ', trim($text));

        if ($text === '') {
            return '1 min read';
        }

        $wordCount = str_word_count($text);
        $minutes   = (int) ceil($wordCount / max(1, $wpm));

        // Enforce a minimum of 1 minute
        $minutes = max(1, $minutes);

        return $minutes . ' min read';
    }
}

