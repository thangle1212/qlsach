<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';

$url = $_GET['url'] ?? 'member/auth/login';
$url = rtrim($url, '/');
$url = explode('/', $url);

if ($url[0] === 'member') {
    $controllerName = ucfirst($url[1] ?? 'Dashboard') . 'Controller';
    $method = $url[2] ?? 'index';
} else {
    $controllerName = ucfirst($url[0] ?? 'Auth') . 'Controller';
    $method = $url[1] ?? 'login';
}

$controllerPath = __DIR__ . '/app/controllers/member/' . $controllerName . '.php';

if (file_exists($controllerPath)) {
    require_once $controllerPath;
    $controller = new $controllerName();

    $method = $url[2] ?? 'index';
    if (method_exists($controller, $method)) {
        $controller->$method();
    } else {
        die('Method not found');
    }
} else {
    die('Controller not found');
}
