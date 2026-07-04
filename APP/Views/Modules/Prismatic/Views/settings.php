<?php
declare(strict_types=1);
/**
 * Prismatic Module Settings View Relay
 *
 * This file exists because the ICTM Framework's view() resolver
 * always looks up paths relative to APPPATH/Views/. Since the
 * Prismatic module lives in APP/Modules/Prismatic/, this relay file
 * reads the actual module view, applies the template parser ({{ }})
 * via the controller, and evaluates it in the correct scope.
 */
$modulePath = APPPATH . 'Modules/Prismatic/Views/settings.php';
if (file_exists($modulePath)) {
    $moduleContent = file_get_contents($modulePath);
    // Apply {{ }} template parsing (same as Controller::parseTemplate)
    $moduleContent = preg_replace('/\{\{([^}]+)\}\}/', '<?php echo $1; ?>', $moduleContent);
    // Evaluate in current scope so $this, $title, $config, $theme_name are all available
    eval('?>' . $moduleContent);
} else {
    echo '<div class="alert alert-danger">Prismatic settings view not found at: ' . htmlspecialchars($modulePath, ENT_QUOTES, 'UTF-8') . '</div>';
}
