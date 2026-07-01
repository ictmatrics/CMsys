<?php
$files = [
    'e:/Webserver/cmsys.wis/APP/Controllers/InstallController.php',
    'e:/Webserver/cmsys.wis/APP/Controllers/FrontendController.php',
    'e:/Webserver/cmsys.wis/APP/Controllers/AuthController.php',
    'e:/Webserver/cmsys.wis/APP/Controllers/AdminController.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $lines = file($file);
    foreach ($lines as &$line) {
        if (strpos($line, "header('Location: ' . pathto(") !== false) {
            $line = preg_replace("/header\('Location: ' \. pathto\((.*)\)\);/", "redirect($1);", $line);
        }
    }
    file_put_contents($file, implode("", $lines));
    echo "Replaced in " . basename($file) . "\n";
}
echo "Done\n";
