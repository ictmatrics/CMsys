<?php
declare(strict_types=1);

namespace Modules\Prismatic;

class Prismatic
{
    public function __construct()
    {
        // Hook into the_content to inject syntax highlighting if code snippets exist
        add_filter('the_content', [$this, 'highlightSnippets']);
    }

    public function highlightSnippets($content)
    {
        if (empty($content)) {
            return $content;
        }

        // Check if content has <pre><code> or <pre class="...
        if (strpos($content, '<pre') !== false && strpos($content, '<code') !== false) {
            // Append PrismJS CSS and JS to the content since we only want it loaded when needed
            $prismCss = '<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />';
            $prismJs = '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>';
            $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>';
            $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>';
            $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>';
            $prismJs .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup.min.js"></script>';

            // Ensure proper class names are applied if summernote left them out
            // Summernote uses <pre> block, we might need to make sure code block has language class
            $content = $content . "\n" . $prismCss . "\n" . $prismJs;
        }

        return $content;
    }
}

// Initialize the module
new Prismatic();
