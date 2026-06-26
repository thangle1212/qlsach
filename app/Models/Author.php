<?php
require_once __DIR__ . '/../Core/Database.php';

class Author {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM authors ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM authors WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO authors (name, biography, nationality, birth_year, death_year) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $data['name'],
                $data['biography'] ?? null,
                $data['nationality'] ?? null,
                $data['birth_year'] ?? null,
                $data['death_year'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Author Create Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE authors SET name = ?, biography = ?, nationality = ?, birth_year = ?, death_year = ? 
                 WHERE id = ?"
            );
            return $stmt->execute([
                $data['name'],
                $data['biography'] ?? null,
                $data['nationality'] ?? null,
                $data['birth_year'] ?? null,
                $data['death_year'] ?? null,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Author Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM authors WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Author Delete Error: " . $e->getMessage());
            return false;
        }
    }

    public function search($keyword) {
        $stmt = $this->db->prepare("SELECT * FROM authors WHERE name LIKE ? ORDER BY name");
        $stmt->execute(['%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
