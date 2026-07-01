<?php
require_once __DIR__ . '/../APP/System/bootstrap.php';
$model = new \App\Models\MediaModel();

// Get the latest media ID
$media = $model->getAllMedia();
if (empty($media)) {
    echo "No media found.\n";
} else {
    $id = (int)$media[0]['id'];
    echo "Latest media ID: $id\n";
    $single = $model->find_single('media_library', $id);
    if ($single) {
        echo "Found single: " . json_encode($single) . "\n";
    } else {
        echo "NOT FOUND single!\n";
    }
}
