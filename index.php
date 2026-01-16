<?php
// Start session once at the application level if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Default controller and action
$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'showLogin';

// Special handling for login and register actions to prevent conflicts
if ($controller === 'auth' && $action === 'login') {
    // Load the AuthController and execute login action directly
    require_once "app/Controllers/AuthController.php";
    $authCtrl = new AuthController();
    $authCtrl->login();
    // The login method should redirect and exit, so we shouldn't reach here
    exit;
}

if ($controller === 'auth' && $action === 'register') {
    // Load the AuthController and execute register action directly
    require_once "app/Controllers/AuthController.php";
    $authCtrl = new AuthController();
    $authCtrl->register();
    // The register method should redirect and exit
    exit;
}

// For other auth controller actions that don't require authentication
$allowUnauthenticated = [
    ['controller' => 'auth', 'action' => 'showLogin'],
    ['controller' => 'auth', 'action' => 'showRegister'],
    ['controller' => 'auth', 'action' => 'register']
];

$isUnauthenticatedAllowed = false;
foreach ($allowUnauthenticated as $allowed) {
    if ($allowed['controller'] === $controller && $allowed['action'] === $action) {
        $isUnauthenticatedAllowed = true;
        break;
    }
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// If user is not logged in and trying to access a protected page
if (!$isLoggedIn && !$isUnauthenticatedAllowed && !($controller === 'auth' && $action === 'login')) {
    // Redirect to login page
    header("Location: index.php?controller=auth&action=showLogin");
    exit;
}

// Special handling for default route when logged in
if ($controller === 'auth' && !$isLoggedIn && $action !== 'login' && $action !== 'showRegister' && $action !== 'register') {
    // If no controller specified and not logged in, show login
    $controller = 'auth';
    $action = 'showLogin';
} elseif ($controller === 'auth' && $isLoggedIn && $action !== 'logout' && $action !== 'showRegister' && $action !== 'register') {
    // If auth controller is called but user is logged in (except for logout), redirect based on role
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: index.php?controller=admin&action=dashboard");
            exit;
        case 'librarian':
            header("Location: index.php?controller=book");
            exit;
        case 'member':
            header("Location: index.php?controller=book");
            exit;
    }
}

// Special handling for admin and settings controllers
if ($controller === 'admin' && $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Bạn không có quyền truy cập';
    header("Location: index.php?controller=book");
    exit;
}

// Special handling for settings controller (admin only)
if ($controller === 'settings' && $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Bạn không có quyền truy cập';
    header("Location: index.php?controller=book");
    exit;
}

// Load the controller
$file = "app/Controllers/" . ucfirst($controller) . "Controller.php";

if (!file_exists($file)) {
    // If controller doesn't exist, redirect to appropriate page
    if ($isLoggedIn) {
        header("Location: index.php?controller=book");
    } else {
        header("Location: index.php?controller=auth&action=showLogin");
    }
    exit;
}

require_once $file;

$class = ucfirst($controller) . "Controller";
$ctrl = new $class();

// Check if the action method exists
if (!method_exists($ctrl, $action)) {
    // If action doesn't exist, redirect to index
    header("Location: index.php?controller=$controller&action=index");
    exit;
}

// Call the action
$ctrl->$action();