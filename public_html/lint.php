<?php
header('Content-Type: text/plain');

function lint_dir($dir) {
    if (!is_dir($dir)) return;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            lint_dir($path);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $cmd = 'php -l ' . escapeshellarg($path) . ' 2>&1';
            $output = shell_exec($cmd);
            if (!str_contains($output, 'No syntax errors detected')) {
                echo "LINT ERROR in $path:\n$output\n-------------------\n";
            }
        }
    }
}

echo "Starting Linting...\n";
lint_dir(__DIR__ . '/../APP');
echo "Linting Completed.\n";
