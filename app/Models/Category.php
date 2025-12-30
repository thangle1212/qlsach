<?php
require_once __DIR__ . '/../Core/Database.php';

class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO categories (name, parent_id, description) VALUES (?, ?, ?)");
            return $stmt->execute([
                $data['name'],
                $data['parent_id'] ?? null,
                $data['description'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Category Create Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("UPDATE categories SET name = ?, parent_id = ?, description = ? WHERE id = ?");
            return $stmt->execute([
                $data['name'],
                $data['parent_id'] ?? null,
                $data['description'] ?? null,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Category Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Category Delete Error: " . $e->getMessage());
            return false;
        }
    }

    public function getByParentId($parent_id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name");
        $stmt->execute([$parent_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
