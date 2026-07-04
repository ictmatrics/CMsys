<?php

declare(strict_types=1);

namespace Modules\Prismatic;

use App\Models\OptionModel;

class Prismatic
{
    public function __construct()
    {
        // Hook into the_content to inject syntax highlighting if code snippets exist
        add_filter('the_content', [$this, 'highlightSnippets']);

        // Module settings actions hook
        add_action('admin_module_actions', [$this, 'injectSettingsLink']);

        // Customize theme hooks for settings page mapping
        add_filter('admin_theme_customize_view', [$this, 'filterCustomizerView'], 10, 2);
        add_filter('admin_theme_customize_data', [$this, 'filterCustomizerData'], 10, 2);
        add_filter('admin_theme_customize_save_config', [$this, 'filterCustomizerSaveConfig'], 10, 2);

        // Assets and Summernote button hooks
        add_action('admin_footer', [$this, 'enqueueSummernotePlugin']);
        add_filter('summernote_insert_buttons', [$this, 'filterSummernoteButtons']);

        // Hook into admin head to style the code blocks inside the backend editor
        add_action('admin_head', [$this, 'enqueueAdminStyles']);
    }

    public function injectSettingsLink(array $mod): void
    {
        if ($mod['name'] === 'Prismatic' && $mod['is_active'] === 1) {
            redirectto('admin/theme/customize/prismatic', 'Settings', 'btn btn-sm btn-outline-primary me-2');
        }
    }

    public function filterCustomizerView(string $view, string $name): string
    {
        if ($name === 'prismatic') {
            return 'Modules/Prismatic/Views/settings';
        }
        return $view;
    }

    public function filterCustomizerData(array $data, string $name): array
    {
        if ($name === 'prismatic') {
            $data['title'] = 'Prismatic Settings';
        }
        return $data;
    }

    public function filterCustomizerSaveConfig(array $config, string $name): array
    {
        if ($name === 'prismatic') {
            $settings = [
                'theme' => validate_data($_POST['theme'] ?? 'prism-tomorrow'),
                'bg_color' => validate_data($_POST['bg_color'] ?? '#2d2d2d'),
                'font_size' => validate_data($_POST['font_size'] ?? '14px'),
                'border_radius' => validate_data($_POST['border_radius'] ?? '8px'),
                'padding' => validate_data($_POST['padding'] ?? '16px'),
                'line_numbers' => isset($_POST['line_numbers']) ? 1 : 0,
                'enable_copy' => isset($_POST['enable_copy']) ? 1 : 0,
                'color_preview' => isset($_POST['color_preview']) ? 1 : 0,
            ];
            $config = [
                'primary_color' => '',
                'secondary_color' => '',
                'custom_css' => '',
                'sticky_header' => '0',
                'custom_css_code' => json_encode($settings),
                'header_scripts' => '',
                'footer_scripts' => '',
            ];
        }
        return $config;
    }

    public function enqueueSummernotePlugin(): void
    {
        $prismaticJsPath = APPPATH . 'Modules/Prismatic/assets/prismatic.js';
        if (file_exists($prismaticJsPath)) {
            echo '<script>' . file_get_contents($prismaticJsPath) . '</script>';
        }
    }

    public function filterSummernoteButtons(string $buttons): string
    {
        return $buttons . ", 'prismatic'";
    }

    public function enqueueAdminStyles(): void
    {
        $optionModel = new OptionModel();
        $configJson = $optionModel->getOption('theme_prismatic_config', '{}');
        $config = json_decode($configJson, true);

        $settings = [];
        if (!empty($config['custom_css_code'])) {
            if (is_array($config['custom_css_code'])) {
                $settings = $config['custom_css_code'];
            } else {
                $settings = json_decode($config['custom_css_code'], true) ?: [];
            }
        }

        $bgColor = $settings['bg_color'] ?? '#2d2d2d';
        $fontSize = $settings['font_size'] ?? '14px';
        $borderRadius = $settings['border_radius'] ?? '8px';
        $padding = $settings['padding'] ?? '16px';
        $lineNumbers = (int)($settings['line_numbers'] ?? 0);

        // Print custom styles inside editor and admin pages
        echo '<style>
            /* Editor pre formatting */
            .note-editable pre {
                background-color: ' . $bgColor . ' !important;
                color: #ffffff !important;
                border-radius: ' . $borderRadius . ' !important;
                padding-top: ' . $padding . ' !important;
                padding-bottom: ' . $padding . ' !important;
                padding-right: ' . $padding . ' !important;
                padding-left: ' . ($lineNumbers === 1 ? '3.8em' : $padding) . ' !important;
                font-size: ' . $fontSize . ' !important;
                position: relative;
                font-family: Consolas, Monaco, \'Andale Mono\', \'Ubuntu Mono\', monospace !important;
                margin: 1rem 0 !important;
            }
            .note-editable pre code {
                color: inherit !important;
                background: transparent !important;
                font-family: inherit !important;
                font-size: inherit !important;
            }
        </style>';
    }

    public function highlightSnippets($content)
    {
        if (empty($content)) {
            return $content;
        }

        // Case-insensitive verification check for post content code blocks
        if (stripos($content, '<pre') !== false && stripos($content, '<code') !== false) {
            $optionModel = new OptionModel();

            // Retrieve config stored in theme_prismatic_config
            $configJson = $optionModel->getOption('theme_prismatic_config', '{}');
            $config = json_decode($configJson, true);

            // Extract the custom settings payload supporting both array and string shapes
            $settings = [];
            if (!empty($config['custom_css_code'])) {
                if (is_array($config['custom_css_code'])) {
                    $settings = $config['custom_css_code'];
                } else {
                    $settings = json_decode($config['custom_css_code'], true) ?: [];
                }
            }

            $theme = $settings['theme'] ?? 'prism-tomorrow';
            $bgColor = $settings['bg_color'] ?? '#2d2d2d';
            $fontSize = $settings['font_size'] ?? '14px';
            $borderRadius = $settings['border_radius'] ?? '8px';
            $padding = $settings['padding'] ?? '16px';
            $lineNumbers = (int)($settings['line_numbers'] ?? 0);
            $enableCopy = (int)($settings['enable_copy'] ?? 1);
            $colorPreview = (int)($settings['color_preview'] ?? 1);

            // Server-side class propagator (adds language-xxxx and line-numbers to <pre> tags dynamically)
            $content = preg_replace_callback('/<pre([^>]*?)>(\s*)<code([^>]*?)class=["\']([^"\']*?language-[^"\']*?)["\']([^>]*?)>/is', function ($matches) use ($lineNumbers) {
                $preAttrs = $matches[1];
                $whitespace = $matches[2];
                $codeAttrs = $matches[3];
                $codeClasses = $matches[4];
                $codeRest = $matches[5];

                // Extract language class (e.g. language-php)
                $langClass = '';
                if (preg_match('/(language-[^\s"\']+)/i', $codeClasses, $langMatches)) {
                    $langClass = $langMatches[1];
                }

                // Prepare pre classes
                $preClassList = [$langClass];
                if ($lineNumbers === 1) {
                    $preClassList[] = 'line-numbers';
                }

                // If pre already has class attribute, merge them; otherwise add it
                if (preg_match('/class=["\']([^"\']*?)["\']/i', $preAttrs, $classMatches)) {
                    $existingClasses = explode(' ', $classMatches[1]);
                    foreach ($preClassList as $cls) {
                        if (!in_array($cls, $existingClasses)) {
                            $existingClasses[] = $cls;
                        }
                    }
                    $preAttrs = preg_replace('/class=["\']([^"\']*?)["\']/i', 'class="' . implode(' ', $existingClasses) . '"', $preAttrs);
                } else {
                    $preAttrs .= ' class="' . implode(' ', $preClassList) . '"';
                }

                return '<pre' . $preAttrs . '>' . $whitespace . '<code' . $codeAttrs . 'class="' . $codeClasses . '"' . $codeRest . '>';
            }, $content);

            $themeMap = [
                'prism-tomorrow' => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css',
                'prism-dark' => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-dark.min.css',
                'prism-okaidia' => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-okaidia.min.css',
                'prism-twilight' => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-twilight.min.css',
                'prism-coy' => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-coy.min.css',
                'prism-solarizedlight' => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-solarized-light.min.css',
                'prism-default' => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css',
            ];

            $themeUrl = $themeMap[$theme] ?? $themeMap['prism-tomorrow'];

            // Append PrismJS CSS and JS (including the required markup-templating dependency)
            $prismCss = '<link href="' . $themeUrl . '" rel="stylesheet" />';

            if ($lineNumbers === 1) {
                $prismCss .= "\n" . '<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.css" rel="stylesheet" />';
            }

            $prismJs = '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>';
            $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup-templating.min.js"></script>';
            $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>';
            $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>';
            $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>';
            $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup.min.js"></script>';
            $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-sql.min.js"></script>';
            $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-bash.min.js"></script>';

            if ($lineNumbers === 1) {
                $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>';
            }

            // Custom styling for code block (IDE styling with sticky line number gutter)
            $customCss = '<style>
                pre[class*="language-"] {
                    background-color: ' . $bgColor . ' !important;
                    border-radius: ' . $borderRadius . ' !important;
                    padding-top: ' . $padding . ' !important;
                    padding-bottom: ' . $padding . ' !important;
                    padding-right: ' . $padding . ' !important;
                    padding-left: ' . ($lineNumbers === 1 ? '3.8em' : $padding) . ' !important;
                    font-size: ' . $fontSize . ' !important;
                    margin: 1.5rem 0 !important;
                    position: relative;
                    overflow: auto;
                }
                code[class*="language-"] {
                    font-size: ' . $fontSize . ' !important;
                    font-family: Consolas, Monaco, \'Andale Mono\', \'Ubuntu Mono\', monospace !important;
                }
                .line-numbers .line-numbers-rows {
                    left: -3.8em !important;
                    width: 3em !important;
                    border-right: 1px solid rgba(255, 255, 255, 0.15) !important;
                    background-color: ' . $bgColor . ' !important;
                    padding-top: ' . $padding . ' !important;
                    box-sizing: border-box !important;
                    z-index: 10 !important;
                }
                .pro-code-container pre[class*="language-"] {
                    margin: 0 !important;
                }
                .color-preview-wrapper {
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                }
                .color-preview-dot {
                    display: inline-block;
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    border: 1px solid rgba(255, 255, 255, 0.3);
                    box-shadow: 0 0 2px rgba(0,0,0,0.5);
                    vertical-align: middle;
                }

                /* Token coloring overrides to match visual design (green comments, pink properties, white braces) */
                .token.comment,
                .token.prolog,
                .token.doctype,
                .token.cdata {
                    color: #6a9955 !important;
                    font-style: italic !important;
                }
                .token.punctuation {
                    color: #ffffff !important;
                }
                .token.selector,
                .token.attr-name,
                .token.string,
                .token.char,
                .token.builtin,
                .token.inserted {
                    color: #b5ce2f !important;
                }
                .token.property,
                .token.tag,
                .token.boolean,
                .token.number,
                .token.constant,
                .token.symbol,
                .token.deleted {
                    color: #ff5370 !important;
                }
                .token.function,
                .token.class-name {
                    color: #ffd254 !important;
                }
                .token.keyword {
                    color: #c5a5c5 !important;
                }
            </style>';

            // Enqueue JS to wrap in a beautiful UI container, handle copy button and color previews
            $customJs = '<script>
            (function() {
                function enhanceCodeBlock(code) {
                    const pre = code.parentNode;
                    if (!pre || pre.tagName !== "PRE") return;
                    if (pre.parentNode && pre.parentNode.classList && pre.parentNode.classList.contains("pro-code-container")) return;

                    // 1. Enhance code blocks (Mac style container + Copy Button)
                    const enableCopy = ' . $enableCopy . ';
                    let lang = "CODE";
                    const classes = code.className.split(" ");
                    for (let i = 0; i < classes.length; i++) {
                        if (classes[i].indexOf("language-") === 0) {
                            lang = classes[i].replace("language-", "").toUpperCase();
                            break;
                        }
                    }

                    // Create wrapper container
                    const container = document.createElement("div");
                    container.className = "pro-code-container";
                    container.style.position = "relative";
                    container.style.margin = "1.5rem 0";
                    container.style.borderRadius = "' . $borderRadius . '";
                    container.style.overflow = "hidden";
                    container.style.border = "1px solid rgba(255, 255, 255, 0.1)";
                    container.style.boxShadow = "0 8px 24px rgba(0,0,0,0.15)";

                    // Create header bar
                    const header = document.createElement("div");
                    header.className = "pro-code-header";
                    header.style.display = "flex";
                    header.style.justifyContent = "space-between";
                    header.style.alignItems = "center";
                    header.style.padding = "8px 16px";
                    header.style.background = "rgba(30, 30, 30, 0.9)";
                    header.style.borderBottom = "1px solid rgba(255, 255, 255, 0.05)";
                    header.style.userSelect = "none";

                    // Left side: Mac-style window controls + Language badge
                    const leftSide = document.createElement("div");
                    leftSide.style.display = "flex";
                    leftSide.style.alignItems = "center";
                    leftSide.style.gap = "12px";

                    const dots = document.createElement("div");
                    dots.style.display = "flex";
                    dots.style.gap = "6px";
                    ["#ff5f56", "#ffbd2e", "#27c93f"].forEach(function(color) {
                        const dot = document.createElement("span");
                        dot.style.width = "10px";
                        dot.style.height = "10px";
                        dot.style.borderRadius = "50%";
                        dot.style.backgroundColor = color;
                    });
                    leftSide.appendChild(dots);

                    const langBadge = document.createElement("span");
                    langBadge.className = "pro-code-lang";
                    langBadge.textContent = lang;
                    langBadge.style.fontSize = "11px";
                    langBadge.style.fontWeight = "600";
                    langBadge.style.color = "#888";
                    langBadge.style.textTransform = "uppercase";
                    langBadge.style.letterSpacing = "0.5px";
                    leftSide.appendChild(langBadge);
                    header.appendChild(leftSide);

                    // Right side: Copy Button
                    if (enableCopy) {
                        const copyBtn = document.createElement("button");
                        copyBtn.className = "pro-code-copy";
                        copyBtn.innerHTML = `<i class="fa-regular fa-copy"></i> Copy`;
                        copyBtn.style.background = "none";
                        copyBtn.style.border = "none";
                        copyBtn.style.color = "#888";
                        copyBtn.style.fontSize = "12px";
                        copyBtn.style.cursor = "pointer";
                        copyBtn.style.padding = "4px 8px";
                        copyBtn.style.borderRadius = "4px";
                        copyBtn.style.transition = "all 0.2s";

                        copyBtn.addEventListener("mouseenter", function() {
                            copyBtn.style.color = "#fff";
                            copyBtn.style.background = "rgba(255,255,255,0.05)";
                        });
                        copyBtn.addEventListener("mouseleave", function() {
                            copyBtn.style.color = "#888";
                            copyBtn.style.background = "none";
                        });

                        copyBtn.addEventListener("click", function() {
                            const textToCopy = code.innerText;
                            navigator.clipboard.writeText(textToCopy).then(function() {
                                copyBtn.innerHTML = `<i class="fa-solid fa-check" style="color: #27c93f;"></i> Copied!`;
                                setTimeout(function() {
                                    copyBtn.innerHTML = `<i class="fa-regular fa-copy"></i> Copy`;
                                }, 2000);
                            });
                        });
                        header.appendChild(copyBtn);
                    }

                    // Rearrange DOM
                    pre.parentNode.insertBefore(container, pre);
                    container.appendChild(header);
                    container.appendChild(pre);

                    pre.style.margin = "0";
                    pre.style.border = "none";
                    pre.style.borderRadius = "0";

                    // 2. Line Numbers class verification
                    const showLineNumbers = ' . $lineNumbers . ';
                    if (showLineNumbers && !pre.classList.contains("line-numbers")) {
                        pre.classList.add("line-numbers");
                    }

                    // 3. Color Previews
                    const colorPreview = ' . $colorPreview . ';
                    if (colorPreview) {
                        const colorRegex = /#(?:[0-9a-fA-F]{3,4}){1,2}\b|rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*(?:,\s*[0-9.]+\s*)?\)/gi;
                        const walk = document.createTreeWalker(code, NodeFilter.SHOW_TEXT, null, false);
                        let node;
                        const nodesToReplace = [];
                        while (node = walk.nextNode()) {
                            colorRegex.lastIndex = 0;
                            if (colorRegex.test(node.nodeValue)) {
                                nodesToReplace.push(node);
                            }
                        }
                        nodesToReplace.forEach(function(textNode) {
                            const parent = textNode.parentNode;
                            if (!parent || parent.tagName === "STYLE" || parent.tagName === "SCRIPT") return;
                            if (parent.classList && (parent.classList.contains("attr-name") || parent.classList.contains("tag"))) return;

                            const originalText = textNode.nodeValue;
                            colorRegex.lastIndex = 0;
                            const matches = Array.from(originalText.matchAll(colorRegex));
                            if (matches.length === 0) return;

                            const fragment = document.createDocumentFragment();
                            let lastIndex = 0;

                            matches.forEach(function(match) {
                                const matchText = match[0];
                                const matchIndex = match.index;

                                if (matchIndex > lastIndex) {
                                    fragment.appendChild(document.createTextNode(originalText.substring(lastIndex, matchIndex)));
                                }

                                const wrapper = document.createElement("span");
                                wrapper.className = "color-preview-wrapper";
                                wrapper.style.display = "inline-flex";
                                wrapper.style.alignItems = "center";
                                wrapper.style.gap = "4px";

                                const dot = document.createElement("span");
                                dot.className = "color-preview-dot";
                                dot.style.display = "inline-block";
                                dot.style.width = "10px";
                                dot.style.height = "10px";
                                dot.style.borderRadius = "50%";
                                dot.style.border = "1px solid rgba(255,255,255,0.3)";
                                dot.style.backgroundColor = matchText;
                                dot.style.boxShadow = "0 0 2px rgba(0,0,0,0.5)";
                                dot.style.verticalAlign = "middle";

                                const textSpan = document.createElement("span");
                                textSpan.textContent = matchText;

                                wrapper.appendChild(dot);
                                wrapper.appendChild(textSpan);
                                fragment.appendChild(wrapper);

                                lastIndex = matchIndex + matchText.length;
                            });

                            if (lastIndex < originalText.length) {
                                fragment.appendChild(document.createTextNode(originalText.substring(lastIndex)));
                            }

                            parent.replaceChild(fragment, textNode);
                        });
                    }
                }

                // Register with Prism hook
                if (typeof Prism !== "undefined") {
                    Prism.hooks.add("complete", function(env) {
                        enhanceCodeBlock(env.element);
                    });
                    
                    // Force Prism to highlight all code blocks if DOM is already complete
                    if (document.readyState === "complete" || document.readyState === "interactive") {
                        Prism.highlightAll();
                    } else {
                        // Also run initially on all already highlighted code blocks
                        document.querySelectorAll("pre code").forEach(function(code) {
                            enhanceCodeBlock(code);
                        });
                    }
                } else {
                    // Fallback to DOMContentLoaded if Prism is not loaded yet
                    function fallbackInit() {
                        document.querySelectorAll("pre code").forEach(function(code) {
                            enhanceCodeBlock(code);
                        });
                    }
                    if (document.readyState === "loading") {
                        document.addEventListener("DOMContentLoaded", fallbackInit);
                    } else {
                        fallbackInit();
                    }
                }
            })();
            </script>';

            // Ensure proper class names are applied if summernote left them out
            $content = $content . "\n" . $prismCss . "\n" . $prismJs . "\n" . $customCss . "\n" . $customJs;
        }

        return $content;
    }
}

// Initialize the module
new Prismatic();
