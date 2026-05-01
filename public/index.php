<?php

session_start();

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/pbpictures/qhsdr/public/');

require_once BASE_PATH . '/app/config/db.php';
require_once BASE_PATH . '/app/core/Auth.php';
require_once BASE_PATH . '/app/core/Csrf.php';
require_once BASE_PATH . '/app/core/AuditLog.php';
require_once BASE_PATH . '/app/core/Flash.php';
require_once BASE_PATH . '/app/core/Sort.php';
require_once BASE_PATH . '/app/core/Paginator.php';
require_once BASE_PATH . '/app/core/RateLimit.php';

// Get URL segments
$url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$segments = explode('/', $url);

$controllerName = !empty($segments[0]) ? $segments[0] : 'auth';
$action         = isset($segments[1]) ? $segments[1] : 'index';

// Map URL segment → [file, class]
$controllerMap = [
    'auth'      => ['AuthController.php',     'AuthController'],
    'users'     => ['UserController.php',     'UserController'],
    'roles'     => ['RoleController.php',     'RoleController'],
    'audit'     => ['AuditController.php',    'AuditController'],
    'locations' => ['LocationController.php', 'LocationController'],
    'machines'  => ['MachineController.php',  'MachineController'],
    'shifts'    => ['ShiftController.php',    'ShiftController'],
    'defects'   => ['DefectController.php',   'DefectController'],
    'datafeed'  => ['DataFeedController.php', 'DataFeedController'],
    'dashboard' => ['DashboardController.php','DashboardController'],
];

if (isset($controllerMap[$controllerName])) {
    [$file, $className] = $controllerMap[$controllerName];
    require_once BASE_PATH . '/app/controllers/' . $file;

    if (class_exists($className)) {
        $controller = new $className();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            http_response_code(404);
            echo "Action not found.";
        }
    }
} else {
    http_response_code(404);
    echo "Page not found.";
}
