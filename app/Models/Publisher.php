<?php
require_once __DIR__ . '/../Core/Database.php';

class Publisher {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM publishers ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM publishers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO publishers (name, address, phone, email, website) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $data['name'],
                $data['address'] ?? null,
                $data['phone'] ?? null,
                $data['email'] ?? null,
                $data['website'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Publisher Create Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE publishers SET name = ?, address = ?, phone = ?, email = ?, website = ? 
                 WHERE id = ?"
            );
            return $stmt->execute([
                $data['name'],
                $data['address'] ?? null,
                $data['phone'] ?? null,
                $data['email'] ?? null,
                $data['website'] ?? null,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Publisher Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM publishers WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Publisher Delete Error: " . $e->getMessage());
            return false;
        }
    }

    public function search($keyword) {
        $stmt = $this->db->prepare("SELECT * FROM publishers WHERE name LIKE ? OR email LIKE ? ORDER BY name");
        $stmt->execute(['%' . $keyword . '%', '%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
