<?php
echo '<h2>File Info</h2>';
$paths = [
    'E:/Webserver/cmsys.wis/APP/Views/Themes/classic/header.php',
    'E:\Webserver\cmsys.wis\APP\Views\Themes\classic\header.php',
];
foreach ($paths as $p) {
    echo '<p>Path: ' . htmlspecialchars($p) . '</p>';
    echo '<p>Exists: ' . (file_exists($p) ? 'YES' : 'NO') . '</p>';
    if (file_exists($p)) {
        echo '<p>Size: ' . filesize($p) . ' bytes</p>';
        echo '<p>mtime: ' . date('Y-m-d H:i:s', filemtime($p)) . '</p>';
        echo '<p>Line 1: ' . htmlspecialchars(substr(file_get_contents($p), 0, 100)) . '</p>';
    }
    echo '<hr>';
}
echo '<p>__FILE__: ' . htmlspecialchars(__FILE__) . '</p>';
echo '<p>getcwd(): ' . htmlspecialchars(getcwd()) . '</p>';
