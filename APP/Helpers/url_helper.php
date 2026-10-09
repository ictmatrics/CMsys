<?php declare(strict_types=1);

/**
 * Redirect to a given page and stop execution.
 */
function redirect(string $page): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($page, '/'));
    exit;
}

/**
 * Generate an HTML link.
 */
function redirectto(string $page, string $pagename = '', string $class = ''): void
{
    $url = BASE_URL . '/' . ltrim($page, '/');
    echo sprintf(
        '<a href="%s" class="%s">%s</a>',
        htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($class, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($pagename, ENT_QUOTES, 'UTF-8')
    );
}

/**
 * Output a URL to a given page.
 */
function linkto(string $page): void
{
    echo BASE_URL . '/' . ltrim($page, '/');
}

/**
 * Return the URL to a given page.
 */
function pathto(string $page): string
{
    return BASE_URL . '/' . ltrim($page, '/');
}
/**
 * Render an <img> element safely.
 * image(src, alt,  width, height,class, style)
 * image('logo.png', 'Site Logo', 'img-fluid',  '200px', '100px');
 *  image('banner.jpg', 'Homepage Banner', 'border-radius:8px;', '100%', 'auto');
 * image('avatar.png', 'User Avatar'); // no width/height

 */
function image(
    string $src,
    string $alt = '',
    string|int $width = '',
    string|int $height = '',
    string $class = '',
    string $style = ''
): void {
    $src   = htmlspecialchars($src, ENT_QUOTES, 'UTF-8');
    $alt   = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');
    $class = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');
    $style = htmlspecialchars($style, ENT_QUOTES, 'UTF-8');

    // Sanitize width/height (allow digits or %)
    $sanitizeDimension = static function (string|int $val): ?string {
        if ($val === '' || $val === 0) {
            return null;
        }
        $val = (string) $val;
        return preg_match('/^\d+(px|%)?$/', $val)
            ? $val
            : null;
    };

    $width  = $sanitizeDimension($width);
    $height = $sanitizeDimension($height);

    // Build attributes dynamically
    $attributes = [
        "src=\"$src\"",
        $alt     !== ''   ? "alt=\"$alt\""       : null,
        $class   !== ''   ? "class=\"$class\""   : null,
        $style   !== ''   ? "style=\"$style\""   : null,
        $width   !== null ? "width=\"$width\""   : null,
        $height  !== null ? "height=\"$height\"" : null,
    ];

    echo '<img ' . implode(' ', array_filter($attributes)) . '>';
}

/**
 * Show a simple JS alert.
 */
function alert(string $message = ''): void
{
    echo "<script>alert(" . json_encode($message) . ");</script>";
}

/**
 * Show alert and then redirect.
 */
function alertto(string $message = '', string $path = ''): void
{
    echo "<script>
        alert(" . json_encode($message) . ");
        location.href=" . json_encode($path) . ";
    </script>";
}

/**
 * Show alert and go back in history.
 */
function alerttoback(string $message = ''): void
{
    echo "<script>
        alert(" . json_encode($message) . ");
        history.back();
    </script>";
}

/**
 * Confirm dialog with conditional redirect.
 */
function confirmto(string $message = '', string $path = '', string $returnpath = ''): void
{
    echo "<script>
        if(confirm(" . json_encode($message) . ")) {
            location.href=" . json_encode($path) . ";
        } else {
            location.href=" . json_encode($returnpath) . ";
        }
    </script>";
}

/**
 * Get the real client IP address.
 */
function getUserIP(): string
{
    // Priority order of headers used by common load balancers/CDNs
    $headers = [
        'HTTP_CF_CONNECTING_IP',  // Cloudflare
        'HTTP_X_REAL_IP',         // Nginx / general reverse proxy
        'HTTP_CLIENT_IP',         // Some ISP proxies
        'HTTP_X_FORWARDED_FOR'    // Standard multi-proxy header
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            // X-Forwarded-For can contain a comma-separated list (client, proxy1, proxy2)
            $ips = explode(',', $_SERVER[$header]);

            foreach ($ips as $ip) {
                $ip = trim($ip);

                // Filter out invalid IPs and local/private range IPs (127.0.0.1, 192.168.x.x, etc.)
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
    }

    // Fallback to the direct connection IP
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
