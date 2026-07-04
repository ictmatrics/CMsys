<?php
declare(strict_types=1);

// Define APPPATH to mock bootstrap
define('APPPATH', __DIR__ . '/APP/');

require_once APPPATH . 'System/Config/Controller.php';

class DummyController extends \System\Config\Controller {}

$controller = new DummyController();
$data = ['title' => 'Test Header'];
try {
    $output = $controller->view('admin/layout/header', $data);
    echo "RENDERED SUCCESSFULLY. length: " . strlen($output) . "\n";
    if (strpos($output, '.wrapper') !== false) {
        echo "Wrapper CSS found!\n";
    } else {
        echo "Wrapper CSS NOT found!\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
