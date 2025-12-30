<?php
require_once __DIR__ . '/../Core/Database.php';

class Settings {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        try {
            $sql = "SELECT * FROM settings ORDER BY id";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If table doesn't exist, return empty array
            if ($e->getCode() == '42S02' || strpos($e->getMessage(), 'doesn\'t exist') !== false) {
                return [];
            }
            throw $e;
        }
    }

    public function get($key) {
        try {
            $stmt = $this->db->prepare("SELECT value FROM settings WHERE `key` = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['value'] : null;
        } catch (PDOException $e) {
            // If table doesn't exist, return null
            if ($e->getCode() == '42S02' || strpos($e->getMessage(), 'doesn\'t exist') !== false) {
                return null;
            }
            throw $e;
        }
    }

    public function set($key, $value) {
        try {
            $existing = $this->get($key);
            
            if ($existing !== null) {
                // Update existing setting
                $sql = "UPDATE settings SET value = ? WHERE `key` = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$value, $key]);
            } else {
                // Insert new setting
                $sql = "INSERT INTO settings (`key`, value) VALUES (?, ?)";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$key, $value]);
            }
        } catch (PDOException $e) {
            // If table doesn't exist, we can't set the value
            if ($e->getCode() == '42S02' || strpos($e->getMessage(), 'doesn\'t exist') !== false) {
                return false;
            }
            throw $e;
        }
    }

    public function initializeDefaults() {
        // Check if table exists first
        try {
            $this->db->query("SELECT 1 FROM settings LIMIT 1");
        } catch (PDOException $e) {
            if ($e->getCode() == '42S02' || strpos($e->getMessage(), 'doesn\'t exist') !== false) {
                // Table doesn't exist, return without initializing
                return;
            }
            throw $e;
        }
        
        $defaults = [
            'max_borrow_days' => 14,
            'fine_per_day' => 5000,
            'max_books_per_user' => 5
        ];

        foreach ($defaults as $key => $value) {
            if ($this->get($key) === null) {
                $this->set($key, $value);
            }
        }
    }
}