<?php

/**
 * ====================================================
 * QL Sách - Entry Point (Index.php)
 * ====================================================
 * Tất cả requests đều được xử lý từ đây
 * Hỗ trợ cả Clean URLs và Query Parameters
 */

// ===== START SESSION =====
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ===== LOAD CONFIG =====
require_once 'config.php';

// ===== LOAD ROUTER CLASS =====
require_once ROUTER_PATH . 'Router.php';

// ===== USE GLOBAL REQUEST & RESPONSE =====
// $request và $response được tạo trong config.php
// Có thể sử dụng ở controllers: global $request, $response;

// ===== INITIALIZE CORS (Bước 4) =====
CORS::init();

// ===== AUTHENTICATION MIDDLEWARE =====
$allowUnauthenticated = [
    'auth' => ['showLogin', 'showRegister', 'login', 'register']
];

$isLoggedIn = isset($_SESSION['user_id']);

// Lấy controller và action từ query params (fallback)
$controller = strtolower($request->getQuery('controller') ?? 'auth');
$action = $request->getQuery('action') ?? 'index';

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$rewriteUrl = $_GET['url'] ?? '';
$pathFromUri = parse_url($requestUri, PHP_URL_PATH) ?? '';
$requestPath = trim($rewriteUrl !== '' ? $rewriteUrl : $pathFromUri, '/');
$requestPath = ltrim(str_replace('/qlsach', '', $requestPath), '/');
$requestPath = preg_replace('#^index\.php#', '', $requestPath);
$requestPath = ltrim($requestPath, '/');
$isApiRequest = strpos($requestPath, 'api/') === 0;

// Kiểm tra nếu user chưa đăng nhập và truy cập trang protected
$allowedActions = $allowUnauthenticated[$controller] ?? [];
if (!$isLoggedIn && !$isApiRequest && !in_array($action, $allowedActions, true)) {
    redirect('index.php?controller=auth&action=showLogin');
}

// ===== INITIALIZE ROUTER =====
$router = new Router();

// ===== LOAD ROUTES DEFINITION =====
require_once ROUTER_PATH . 'Route.php';

// ===== RUN ROUTER =====
$router->run();
