<?php

/**
 * Logger - Ghi log hoạt động của ứng dụng
 * 
 * Sử dụng để ghi:
 * - User actions (CRUD operations)
 * - System events
 * - Debug information
 * - API requests
 * 
 * Ví dụ:
 * Logger::info('User created a new book', ['user_id' => 5, 'book_id' => 10]);
 * Logger::error('Database connection failed');
 */

class Logger
{
    // Log levels
    const DEBUG = 'DEBUG';
    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const CRITICAL = 'CRITICAL';

    private static $logDir = '';
    private static $maxFileSize = 10485760; // 10MB

    /**
     * Setup logger với thư mục lưu logs
     * 
     * @param string $logDir Đường dẫn thư mục logs
     * @param int $maxFileSize Kích thước tối đa mỗi file (bytes)
     */
    public static function setup($logDir, $maxFileSize = 10485760)
    {
        self::$logDir = rtrim($logDir, '/');
        self::$maxFileSize = $maxFileSize;

        // Tạo thư mục logs nếu không tồn tại
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
    }

    /**
     * Ghi debug log
     */
    public static function debug($message, $context = [])
    {
        self::write(self::DEBUG, $message, $context);
    }

    /**
     * Ghi info log
     */
    public static function info($message, $context = [])
    {
        self::write(self::INFO, $message, $context);
    }

    /**
     * Ghi warning log
     */
    public static function warning($message, $context = [])
    {
        self::write(self::WARNING, $message, $context);
    }

    /**
     * Ghi error log
     */
    public static function error($message, $context = [])
    {
        self::write(self::ERROR, $message, $context);
    }

    /**
     * Ghi critical log
     */
    public static function critical($message, $context = [])
    {
        self::write(self::CRITICAL, $message, $context);
    }

    /**
     * Ghi log theo level
     */
    private static function write($level, $message, $context = [])
    {
        if (empty(self::$logDir)) {
            return; // Logger chưa được config
        }

        // Format log entry
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logEntry = "[$timestamp] [$level] $message$contextStr\n";

        // Xác định file log
        $logFile = self::$logDir . '/' . $level . '_' . date('Y-m-d') . '.log';

        // Rotate log file nếu vượt quá maxFileSize
        if (file_exists($logFile) && filesize($logFile) > self::$maxFileSize) {
            $rotatedFile = self::$logDir . '/' . $level . '_' . date('Y-m-d_H-i-s') . '.log';
            rename($logFile, $rotatedFile);
            gzip_file($rotatedFile); // Compress nếu có thể
        }

        // Ghi log
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Lấy logs theo level
     * 
     * @param string $level Log level (DEBUG, INFO, WARNING, ERROR, CRITICAL)
     * @param int $lines Số dòng cuối cùng
     * @return array
     */
    public static function getLogs($level, $lines = 100)
    {
        if (empty(self::$logDir)) {
            return [];
        }

        $logFile = self::$logDir . '/' . $level . '_' . date('Y-m-d') . '.log';

        if (!file_exists($logFile)) {
            return [];
        }

        $fileContent = file($logFile);
        return array_slice($fileContent, -$lines);
    }

    /**
     * Log user action (login, create, update, delete, etc.)
     */
    public static function activity($action, $resource, $userId = null, $data = [])
    {
        if (empty($userId) && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }

        $context = [
            'action' => $action,
            'resource' => $resource,
            'user_id' => $userId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if (!empty($data)) {
            $context['data'] = $data;
        }

        self::write('ACTIVITY', "$action on $resource", $context);
    }

    /**
     * Log API request
     */
    public static function request($method, $endpoint, $statusCode, $responseTime = 0)
    {
        $context = [
            'method' => $method,
            'endpoint' => $endpoint,
            'status' => $statusCode,
            'response_time_ms' => round($responseTime * 1000, 2),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_id' => $_SESSION['user_id'] ?? 'guest'
        ];

        self::write('REQUEST', "API call: $method $endpoint", $context);
    }
}

/**
 * Helper function để compress file
 */
if (!function_exists('gzip_file')) {
    function gzip_file($filePath)
    {
        if (!extension_loaded('zlib')) {
            return false;
        }

        $gzipPath = $filePath . '.gz';
        $content = file_get_contents($filePath);
        file_put_contents('compress.zlib://' . $gzipPath, $content);
        unlink($filePath);

        return file_exists($gzipPath);
    }
}
