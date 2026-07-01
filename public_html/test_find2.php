<?php
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'cmsys.wis';
session_start();
$_SESSION['ICTM_Auth'] = true;
$_SESSION['user_id'] = 1;

require_once __DIR__ . '/index.php';

$model = new \App\Models\MediaModel();

$media = $model->getAllMedia();
if (empty($media)) {
    echo "No media found.\n";
} else {
    $id = (int)$media[0]['id'];
    echo "Latest media ID: $id\n";
    $single = $model->find_single('media_library', $id);
    if ($single) {
        echo "Found single: " . json_encode($single) . "\n";
        
        $filePath = ROOTPATH . 'public_html/' . ltrim($single->path, '/\\');
        echo "FilePath: $filePath\n";
        echo "FileExists: " . (file_exists($filePath) ? 'yes' : 'no') . "\n";
        
        $res = $model->delete('media_library', $id);
        echo "Delete record: " . ($res ? 'yes' : 'no') . "\n";
    } else {
        echo "NOT FOUND single!\n";
    }
}
