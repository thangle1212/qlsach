<?php

/**
 * Config File - Cấu hình chính của ứng dụng
 * Tương tự PHP-MVC-REST-API
 */

// ===== PATHS =====
define('ROOT_PATH', dirname(__FILE__) . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('SYSTEM_PATH', APP_PATH . 'System/');
define('CONTROLLERS_PATH', APP_PATH . 'Controllers/');
define('MODELS_PATH', APP_PATH . 'Models/');
define('VIEWS_PATH', APP_PATH . 'Views/');
define('ROUTER_PATH', ROOT_PATH . 'Router/');
define('UPLOAD_PATH', ROOT_PATH . 'Upload/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');

// ===== DATABASE =====
define('DB_HOST', 'localhost');
define('DB_NAME', 'library_management');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_PREFIX', 'sm_');  // Prefix cho bảng database (nếu cần)

// ===== URL & HTTP =====
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('HTTP_URL', 'http://' . $_SERVER['HTTP_HOST'] . $scriptName . '/');
define('BASE_URL', dirname($_SERVER['SCRIPT_NAME']) . '/');

// ===== APPLICATION SETTINGS =====
define('APP_NAME', 'Quản Lý Thư Viện');
define('APP_DEBUG', true);  // true = hiển thị lỗi, false = ẩn
define('APP_TIMEZONE', 'Asia/Ho_Chi_Minh');

// ===== AUTHENTICATION =====
define('SESSION_TIMEOUT', 3600);  // 1 giờ tính bằng giây
define('SESSION_NAME', 'qlsach_session');

// Thiết lập timezone
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set(APP_TIMEZONE);
}

// ===== LOAD HELPER FUNCTIONS =====
require_once APP_PATH . 'Helper/public.php';

// ===== LOAD CORE CLASSES =====
require_once APP_PATH . 'Core/Database.php';
require_once SYSTEM_PATH . 'Database/Model.php';

// Load HTTP classes (Request, Response)
require_once SYSTEM_PATH . 'Http/Request.php';
require_once SYSTEM_PATH . 'Http/Response.php';

// ===== LOAD MIDDLEWARE & ERROR HANDLING (Bước 4) =====
require_once SYSTEM_PATH . 'Error/ErrorHandler.php';
require_once SYSTEM_PATH . 'Middleware/Middleware.php';
require_once SYSTEM_PATH . 'Middleware/AuthMiddleware.php';
require_once SYSTEM_PATH . 'Logger/Logger.php';
require_once SYSTEM_PATH . 'CORS/CORS.php';

// ===== INITIALIZE ERROR HANDLER =====
ErrorHandler::register(APP_DEBUG, UPLOAD_PATH . 'logs/error.log');

// ===== INITIALIZE LOGGER =====
Logger::setup(UPLOAD_PATH . 'logs');

// ===== CONFIGURE CORS =====
// Chọn một trong các cách sau:
// Cách 1: Cho phép tất cả origins
// CORS::enableFor(['*']);
// 
// Cách 2: Cho phép origins cụ thể
CORS::enableFor([
    'http://localhost',
    'http://localhost:3000',
    'http://localhost:8080',
    'http://127.0.0.1',
    $_SERVER['HTTP_HOST'] ?? ''
]);

// Global instances - có thể sử dụng ở khắp nơi
global $request, $response;
$request = new \Http\Request();
$response = new \Http\Response();

// Hàm helper để load config từ bất cứ đâu
function config($key = null)
{
    $configs = [
        'database' => [
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASS,
            'charset' => DB_CHARSET,
            'prefix' => DB_PREFIX,
        ],
        'app' => [
            'name' => APP_NAME,
            'debug' => APP_DEBUG,
            'timezone' => APP_TIMEZONE,
        ],
        'paths' => [
            'root' => ROOT_PATH,
            'app' => APP_PATH,
            'controllers' => CONTROLLERS_PATH,
            'models' => MODELS_PATH,
            'views' => VIEWS_PATH,
            'upload' => UPLOAD_PATH,
        ]
    ];

    if ($key === null) {
        return $configs;
    }

    $keys = explode('.', $key);
    $result = $configs;

    foreach ($keys as $k) {
        if (isset($result[$k])) {
            $result = $result[$k];
        } else {
            return null;
        }
    }

    return $result;
}
