<?php
class Database {
    private static $instance = null;
    private $conn;

    private $host = "localhost";
    private $db   = "library_management";
    private $user = "root";
    private $pass = "";
    private $charset = "utf8mb4";

    private function __construct() {
        try {
            $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("DB Error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}
