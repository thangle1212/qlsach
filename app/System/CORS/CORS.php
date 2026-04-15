<?php

/**
 * CORS - Xử lý Cross-Origin Resource Sharing
 * 
 * Cho phép frontend từ domain khác truy cập API
 * 
 * Ví dụ:
 * CORS::enableFor(['http://localhost:3000', 'https://example.com']);
 * CORS::init();
 */

class CORS
{
    private static $allowedOrigins = [];
    private static $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];
    private static $allowedHeaders = ['Content-Type', 'Authorization', 'X-Requested-With'];
    private static $credentialsAllowed = true;
    private static $maxAge = 86400; // 24 hours

    /**
     * Cấu hình CORS để cho phép từ domain(s) cụ thể
     * 
     * @param array $origins Mảng URLs được phép (VD: ['http://localhost:3000', 'https://example.com'])
     */
    public static function enableFor($origins = ['*'])
    {
        self::$allowedOrigins = $origins;
    }

    /**
     * Thêm domain vào danh sách cho phép
     */
    public static function allowOrigin($origin)
    {
        if (!in_array($origin, self::$allowedOrigins)) {
            self::$allowedOrigins[] = $origin;
        }
    }

    /**
     * Cấu hình HTTP methods được phép
     */
    public static function allowMethods($methods = [])
    {
        self::$allowedMethods = $methods;
    }

    /**
     * Cấu hình headers được phép
     */
    public static function allowHeaders($headers = [])
    {
        self::$allowedHeaders = $headers;
    }

    /**
     * Cho phép credentials (cookies, auth headers)
     */
    public static function allowCredentials($allow = true)
    {
        self::$credentialsAllowed = $allow;
    }

    /**
     * Cài đặt cache time cho CORS preflight requests
     */
    public static function setMaxAge($seconds = 86400)
    {
        self::$maxAge = $seconds;
    }

    /**
     * Khởi tạo CORS headers
     * Gọi từ index.php trước khi dispatch route
     */
    public static function init()
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        // Kiểm tra origin có được phép không
        if (!self::isOriginAllowed($origin)) {
            return; // Không gửi CORS headers
        }

        // Gửi CORS headers
        header("Access-Control-Allow-Origin: " . $origin);
        header("Access-Control-Allow-Methods: " . implode(', ', self::$allowedMethods));
        header("Access-Control-Allow-Headers: " . implode(', ', self::$allowedHeaders));

        if (self::$credentialsAllowed) {
            header("Access-Control-Allow-Credentials: true");
        }

        header("Access-Control-Max-Age: " . self::$maxAge);

        // Xử lý preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Kiểm tra origin có được phép không
     */
    private static function isOriginAllowed($origin)
    {
        // Nếu cho phép tất cả (*)
        if (in_array('*', self::$allowedOrigins)) {
            return true;
        }

        // Kiểm tra origin cụ thể
        return in_array($origin, self::$allowedOrigins);
    }

    /**
     * Lấy danh sách origins được phép
     */
    public static function getAllowedOrigins()
    {
        return self::$allowedOrigins;
    }

    /**
     * Lấy headers CORS hiện tại
     */
    public static function getHeaders()
    {
        return [
            'Access-Control-Allow-Origin' => implode(', ', self::$allowedOrigins),
            'Access-Control-Allow-Methods' => implode(', ', self::$allowedMethods),
            'Access-Control-Allow-Headers' => implode(', ', self::$allowedHeaders),
            'Access-Control-Allow-Credentials' => self::$credentialsAllowed ? 'true' : 'false',
            'Access-Control-Max-Age' => self::$maxAge
        ];
    }
}
