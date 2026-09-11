<?php declare(strict_types=1);

/**
 * --------------------------------------------------------------------------
 * The Bootstrapper
 * --------------------------------------------------------------------------
 *
 * This file is the main entry point for the application. It sets up the
 * environment, loads the autoloader, defines key constants, and handles
 * the routing of the request to the appropriate controller and action.
 */

use Config\Autoload;
use Config\Paths;
use System\Router;
use System\RouteNotFoundException;

// --------------------------------------------------------------------------
// Define Application Constants
// --------------------------------------------------------------------------
// These constants are used to locate key directories throughout the application.

// Create an instance of the Paths class to get directory locations.
$paths = new Paths();

if (!defined('APPPATH')) {
    define('APPPATH', realpath(rtrim($paths->appDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
}

if (!defined('ROOTPATH')) {
    define('ROOTPATH', realpath(APPPATH . '../') . DIRECTORY_SEPARATOR);
}

if (!defined('SYSTEMPATH')) {
    define('SYSTEMPATH', realpath(rtrim($paths->systemDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
}

if (!defined('WRITEPATH')) {
    define('WRITEPATH', realpath(rtrim($paths->writableDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
}

// --------------------------------------------------------------------------
// Load Configuration and Autoloader
// --------------------------------------------------------------------------

require_once APPPATH . 'Config/Constants.php';

// Load the Autoloader class and register it.
if (!class_exists(Autoload::class, false)) {
    require_once APPPATH . 'Config/Autoload.php';
}

$loader = new Autoload();
$loader->register();

// NOTE: Global helper functions like `encodedto()` and `encoded()` have been
// removed. A better practice is to place them in a namespaced helper file,
// e.g., 'App/Helpers/StringHelpers.php', and use a `require_once` there
// or let the autoloader handle them if they are in a class.
//
// Example:
// require_once APPPATH . 'Helpers/StringHelpers.php';


// --------------------------------------------------------------------------
// Initialize Router and Handle Request
// --------------------------------------------------------------------------

$router = new Router();

// Load the route definitions.
require_once APPPATH . 'Config/Route.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestUri = is_string($requestUri) ? $requestUri : '/';

// Strip the subdirectory path from the request URI for correct routing in subdirectories
$basePath = parse_url(BASE_URL, PHP_URL_PATH);
if (is_string($basePath)) {
    $basePath = rtrim($basePath, '/');
    if ($basePath !== '' && ($requestUri === $basePath || strpos($requestUri, $basePath . '/') === 0)) {
        $requestUri = substr($requestUri, strlen($basePath));
    }
}

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    // Route the request. This will throw an exception if no route is found.
    $routeInfo = $router->route($requestUri, $requestMethod);

    // Extract controller, action, and parameters.
    $controllerName = $routeInfo['controller'];
    $action = $routeInfo['action'];
    $params = $routeInfo['params'];

    // Construct the fully qualified controller class name. This now correctly
    // converts an underscore-separated name to a namespaced class name.
    // For example, 'about_AboutController' becomes 'App\Controllers\about\AboutController'.
    $controllerClassName = '\\App\\Controllers\\' . str_replace('_', '\\', $controllerName);

    if (!class_exists($controllerClassName)) {
        throw new Exception("Controller '{$controllerClassName}' not found.");
    }
    
    $controller = new $controllerClassName();
    
    if (!method_exists($controller, $action)) {
        throw new Exception("Action '{$action}' not found in controller '{$controllerClassName}'.");
    }

    call_user_func_array([$controller, $action], $params);

} catch (RouteNotFoundException $e) {
    // Handle 404 (Not Found) errors.
    header('HTTP/1.1 404 Not Found', true, 404);

    // Add a specific error message if a POST request fails to route.
    if ($requestMethod === 'POST') {
        error_log('POST route not found for URI: ' . $requestUri);
    }
    header('location:'.BASE_URL. DIRECTORY_SEPARATOR .'404.php');
   //require_once ROOTPATH . 'public_html' . DIRECTORY_SEPARATOR . '404.php';
    exit;

} catch (Exception $e) {
    // Handle other general application errors.
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    $errorMessage = $e->getMessage();
    require_once ROOTPATH . 'public_html' . DIRECTORY_SEPARATOR . '500.php';
    exit;
}
