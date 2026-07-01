<?php
// Mock server vars
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'cmsys.wis';

require_once __DIR__ . '/../APP/System/bootstrap.php';
$db = new \System\Config\QueryBuilder();

$uploadDir = ROOTPATH . 'public_html/Writables/images/test/';
@mkdir($uploadDir, 0755, true);
$filePath = $uploadDir . 'test.jpg';
file_put_contents($filePath, 'fake image data');

$db->query("INSERT INTO media_library (original_name, path, mime_type) VALUES ('test.jpg', 'Writables/images/test/test.jpg', 'image/jpeg')");
$id = $db->conn->insert_id;

echo "Inserted $id. Exists: " . (file_exists($filePath) ? 'Y' : 'N') . "\n";

$model = new \App\Models\MediaModel();
$res = $model->deleteMedia($id);

echo "Deleted $id. Res: " . ($res ? 'Y' : 'N') . "\n";
echo "Exists: " . (file_exists($filePath) ? 'Y' : 'N') . "\n";
