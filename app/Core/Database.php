<?php

/**
 * Database Class - Singleton Pattern
 * Sử dụng config.php cho cấu hình database
 */
class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->conn = new PDO($dsn, DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die("Database Error: " . $e->getMessage());
            } else {
                die("Database connection failed");
            }
        }
    }

    /**
     * Singleton - Lấy instance duy nhất
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}
