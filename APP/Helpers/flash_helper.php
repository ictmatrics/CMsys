<?php
declare(strict_types=1);

session_start();

/**
 * Flash message helper (PHP 8.3 optimized).
 */
function flash(
    string $name,
    string $message = '',
    string $class = 'alert alert-success',
    string $position = 'top-right'
): void {
    if ($name === '') {
        return;
    }

    // Store flash message
    if ($message !== '' && !isset($_SESSION[$name])) {
        $_SESSION[$name] = $message;
        $_SESSION[$name . '_class'] = $class;
        $_SESSION[$name . '_position'] = $position;
        return;
    }

    // Show flash message
    if ($message === '' && isset($_SESSION[$name])) {
        $class    = $_SESSION[$name . '_class'] ?? 'alert alert-success';
        $position = $_SESSION[$name . '_position'] ?? 'top-right';

        // Base style with smooth transitions and subtle shadow
        $style = "position:fixed; z-index:1090; padding:12px 20px; margin:15px; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,0.15); transition: opacity 0.4s ease, transform 0.4s ease; display:flex; align-items:center; gap:12px;";

        $style .= match ($position) {
            'top-right'     => "top:0; right:0;",
            'top-left'      => "top:0; left:0;",
            'bottom-right'  => "bottom:0; right:0;",
            'bottom-left'   => "bottom:0; left:0;",
            'middle-left'   => "top:50%; left:0; transform:translateY(-50%);",
            'middle-right'  => "top:50%; right:0; transform:translateY(-50%);",
            'middle-top'    => "top:0; left:50%; transform:translateX(-50%);",
            'middle-bottom' => "bottom:0; left:50%; transform:translateX(-50%);",
            default         => "top:0; right:0;",
        };

        $flashId = 'msg-flash-' . uniqid();
        $msgText = htmlspecialchars((string)$_SESSION[$name], ENT_QUOTES, 'UTF-8');
        $fullClass = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');

        echo <<<HTML
        <div class="{$fullClass} alert-dismissible fade show" id="{$flashId}" style="{$style}" role="alert">
            <span>{$msgText}</span>
            <button type="button" class="btn-close" style="padding: 0; margin-left: 8px; font-size: 0.8rem;" onclick="dismissFlashAlert('{$flashId}')" aria-label="Close"></button>
        </div>
        <script>
            (function() {
                function initFlashAutoDismiss() {
                    const el = document.getElementById('{$flashId}');
                    if (!el) return;
                    setTimeout(function() {
                        dismissFlashAlert('{$flashId}');
                    }, 3000);
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initFlashAutoDismiss);
                } else {
                    initFlashAutoDismiss();
                }
            })();
            if (typeof window.dismissFlashAlert !== 'function') {
                window.dismissFlashAlert = function(id) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.style.opacity = '0';
                        el.style.transform = (el.style.transform || '') + ' scale(0.95)';
                        setTimeout(function() {
                            if (el && el.parentNode) {
                                el.parentNode.removeChild(el);
                            }
                        }, 400);
                    }
                };
            }
        </script>
        HTML;

        // Clear session
        unset($_SESSION[$name], $_SESSION[$name . '_class'], $_SESSION[$name . '_position']);
    }
}
