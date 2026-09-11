<?php

// Check PHP version
$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run ICTM Framework. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );
    exit($message);
}

// Define the path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

// Load paths configuration file
require_once FCPATH . '../APP/Config/Paths.php';

// Initialize Paths
$paths = new Config\Paths();

// Load the bootstrap file
require_once rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 * Dispatch the request to the controller and action
 *
 * @param string $controllerName
 * @param string $action
 * @param array $params
 */


//  $controller = new $controllerName();
// call_user_func_array([$controller, $action], $params);