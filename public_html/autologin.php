<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Get admin user from database to use correct ID
require_once __DIR__ . '/../APP/System/bootstrap.php';
$db = new \System\Config\QueryBuilder();
$admin = $db->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
if (!empty($admin)) {
    $user = $admin[0];
    $_SESSION['ICTM_Auth'] = [
        'user_id' => (int)$user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role' => $user['role']
    ];
} else {
    $_SESSION['ICTM_Auth'] = [
        'user_id' => 1,
        'username' => 'admin',
        'email' => 'admin@example.com',
        'role' => 'admin'
    ];
}

header('Location: ' . pathto('admin/themes'));
exit();
