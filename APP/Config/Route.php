<?php

declare(strict_types=1);

/**
 * --------------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------------
 *
 * This file is where you define all of your application's routes. It is
 * included by the bootstrapper and should be used to add routes to the
 * $router instance.
 *
 */

// Frontend & Installer Routes
$router->get('', 'HomeController@index');
$router->get('/', 'HomeController@index');
$router->get('/install', 'InstallController@index');
$router->post('/install', 'InstallController@run');
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@doLogin');
$router->get('/logout', 'AuthController@logout');

// Dynamic Content Display Routes
$router->get('/category/{slug}', 'FrontendController@category');
$router->get('/tag/{slug}', 'FrontendController@tag');
$router->post('/comment/add', 'FrontendController@addComment');
$router->get('/cpt/{type}/{slug}', 'FrontendController@cptEntry');
$router->get('/{slug}', 'FrontendController@detail');

// Administration Control Panel Routes
$router->get('/admin', 'AdminController@index');
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/settings', 'AdminController@settings');
$router->post('/admin/settings', 'AdminController@saveSettings');

// Posts & Pages Management
$router->get('/admin/posts', 'AdminController@posts');
$router->get('/admin/post/add', 'AdminController@postForm');
$router->get('/admin/post/edit/{id}', 'AdminController@postForm');
$router->post('/admin/post/save', 'AdminController@savePost');
$router->post('/admin/post/delete', 'AdminController@deletePost');

$router->get('/admin/pages', 'AdminController@pages');
$router->get('/admin/page/add', 'AdminController@pageForm');
$router->get('/admin/page/edit/{id}', 'AdminController@pageForm');
$router->post('/admin/page/save', 'AdminController@savePage');
$router->post('/admin/page/delete', 'AdminController@deletePage');

// Page builder editor
$router->get('/admin/page-editor/{id}', 'AdminController@pageEditor');
$router->post('/admin/page-editor/save', 'AdminController@savePageEditor');

// Taxonomy: Categories & Tags
$router->get('/admin/categories', 'AdminController@categories');
$router->post('/admin/category/save', 'AdminController@saveCategory');
$router->post('/admin/category/delete', 'AdminController@deleteCategory');

$router->get('/admin/tags', 'AdminController@tags');
$router->post('/admin/tag/save', 'AdminController@saveTag');
$router->post('/admin/tag/delete', 'AdminController@deleteTag');

// Media Library
$router->get('/admin/media', 'AdminController@media');
$router->post('/admin/media/upload', 'AdminController@uploadMedia');
$router->post('/admin/media/delete', 'AdminController@deleteMedia');

// Dynamic Custom Menus
$router->get('/admin/menus', 'AdminController@menus');
$router->post('/admin/menu/save', 'AdminController@saveMenu');
$router->post('/admin/menu/delete', 'AdminController@deleteMenu');
$router->post('/admin/menu/items/save', 'AdminController@saveMenuItems');

// Widgets & Sidebars
$router->get('/admin/widgets', 'AdminController@widgets');
$router->post('/admin/widgets/save', 'AdminController@saveWidgets');

// Themes & Modules Lifecycle Management
$router->get('/admin/themes', 'AdminController@themes');
$router->post('/admin/theme/upload', 'AdminController@uploadTheme');
$router->get('/admin/theme/activate/{name}/{scope}', 'AdminController@activateTheme');
$router->post('/admin/theme/delete', 'AdminController@deleteTheme');
$router->get('/admin/theme/customize/{name}', 'AdminController@customizeTheme');
$router->post('/admin/theme/customize/{name}', 'AdminController@saveThemeCustomization');

$router->get('/admin/modules', 'AdminController@modules');
$router->post('/admin/module/upload', 'AdminController@uploadModule');
$router->get('/admin/module/toggle/{name}', 'AdminController@toggleModule');
$router->post('/admin/module/delete', 'AdminController@deleteModule');

// User Accounts & RBAC
$router->get('/admin/users', 'AdminController@users');
$router->post('/admin/user/save', 'AdminController@saveUser');
$router->post('/admin/user/delete', 'AdminController@deleteUser');

// Custom Post Types Configuration
$router->get('/admin/cpts', 'AdminController@cpts');
$router->post('/admin/cpt/save', 'AdminController@saveCpt');
$router->post('/admin/cpt/delete', 'AdminController@deleteCpt');
$router->get('/admin/cpt/entries/{type}', 'AdminController@cptEntries');
$router->get('/admin/cpt/entry/add/{type}', 'AdminController@cptEntryForm');
$router->get('/admin/cpt/entry/edit/{type}/{id}', 'AdminController@cptEntryForm');
$router->post('/admin/cpt/entry/save', 'AdminController@saveCptEntry');
$router->post('/admin/cpt/entry/delete', 'AdminController@deleteCptEntry');

// System Settings, Notifications and Import & Export
$router->get('/admin/notifications', 'AdminController@notifications');
$router->post('/admin/notifications', 'AdminController@saveNotifications');

$router->get('/admin/import-export', 'AdminController@importExport');
$router->post('/admin/export', 'AdminController@exportDatabase');
$router->post('/admin/import', 'AdminController@importDatabase');

// AJAX API Endpoint
$router->post('/admin/api/update-status', 'AdminController@updateStatusApi');

