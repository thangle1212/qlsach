<?php

/**
 * ErrorHandler - Xử lý lỗi toàn cục cho ứng dụng
 * 
 * Chuyên trách:
 * - Bắt Exception và Error
 * - Trả về JSON response thay vì HTML error pages
 * - Logging lỗi vào file
 * - Phân biệt lỗi development vs production
 */

class ErrorHandler
{
    private static $logger;
    private static $isDevelopment = false;
    private static $errorLog = '';

    /**
     * Khởi tạo error handler
     * Đăng ký cho cả Exception và PHP Errors
     */
    public static function register($isDevelopment = false, $logPath = '')
    {
        self::$isDevelopment = $isDevelopment;

        if (!empty($logPath)) {
            self::$errorLog = $logPath;
        }

        // Set up exception handler
        set_exception_handler([self::class, 'handleException']);

        // Set up error handler
        set_error_handler([self::class, 'handleError']);

        // Xử lý fatal errors
        register_shutdown_function([self::class, 'handleFatalError']);
    }

    /**
     * Xử lý Exception
     */
    public static function handleException($exception)
    {
        $statusCode = 500;
        $message = $exception->getMessage();
        $code = $exception->getCode();

        // Phân loại exception dựa theo HTTP status codes
        if ($code >= 400 && $code <= 599) {
            $statusCode = $code;
        }

        // Log lỗi
        self::logError($exception);

        // Gửi JSON response
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'success' => false,
            'message' => $message,
            'status' => $statusCode,
            'error' => $code
        ];

        // Ở development mode, thêm debug info
        if (self::$isDevelopment) {
            $response['debug'] = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Xử lý PHP Error thành Exception
     */
    public static function handleError($severity, $message, $file, $line)
    {
        // Log lỗi
        self::writeLog("PHP Error [$severity] $message in $file:$line");

        // Chuyển thành Exception để xử lý thống nhất
        switch ($severity) {
            case E_ERROR:
            case E_USER_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
                throw new ErrorException($message, 500, $severity, $file, $line);
            default:
                return false; // Để PHP xử lý default
        }
    }

    /**
     * Xử lý fatal errors
     */
    public static function handleFatalError()
    {
        $error = error_get_last();

        if ($error !== null) {
            self::handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }

    /**
     * Logging lỗi
     */
    private static function logError($exception)
    {
        $message = sprintf(
            "[%s] %s (%s:%d)\n%s\n---\n",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        self::writeLog($message);
    }

    /**
     * Ghi log vào file
     */
    private static function writeLog($message)
    {
        if (empty(self::$errorLog)) {
            return;
        }

        $dir = dirname(self::$errorLog);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(self::$errorLog, $message, FILE_APPEND);
    }

    /**
     * Throw HTTP exception
     */
    public static function throw($message, $statusCode = 500)
    {
        throw new Exception($message, $statusCode);
    }

    /**
     * Xử lý 404 Not Found
     */
    public static function notFound($message = 'Resource not found')
    {
        self::throw($message, 404);
    }

    /**
     * Xử lý 401 Unauthorized
     */
    public static function unauthorized($message = 'Unauthorized')
    {
        self::throw($message, 401);
    }

    /**
     * Xử lý 403 Forbidden
     */
    public static function forbidden($message = 'Forbidden')
    {
        self::throw($message, 403);
    }

    /**
     * Xử lý 422 Validation error
     */
    public static function validationError($message = 'Validation failed', $errors = [])
    {
        http_response_code(422);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'status' => 422,
            'errors' => $errors
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
