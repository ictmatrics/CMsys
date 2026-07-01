<?php
require_once __DIR__ . '/../APP/System/bootstrap.php';
$db = new \System\Config\QueryBuilder();

// Create fake file
$uploadDir = ROOTPATH . 'public_html/Writables/images/test/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$filePath = $uploadDir . 'test.jpg';
file_put_contents($filePath, 'fake image data');

// Insert record
$db->query("INSERT INTO media_library (original_name, path, mime_type) VALUES ('test.jpg', 'Writables/images/test/test.jpg', 'image/jpeg')");
$id = $db->conn->insert_id;

echo "Inserted fake media with ID $id\n";

$model = new \App\Models\MediaModel();
$result = $model->deleteMedia($id);

echo "Delete result: " . ($result ? 'success' : 'fail') . "\n";
echo "File exists? " . (file_exists($filePath) ? 'yes' : 'no') . "\n";
