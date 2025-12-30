<?php
require_once __DIR__ . '/../Core/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function authenticate($username, $password) {
        $user = $this->findByUsername($username);
        
        if ($user) {
            // Check if it's a proper bcrypt hash
            if (password_verify($password, $user['password_hash'])) {
                return $user;
            }
            // For testing: Check if it's a plain text match
            if ($user['password_hash'] === $password) {
                return $user;
            }
        }
        
        return false;
    }

    public function create($data) {
        $sql = "
            INSERT INTO users 
            (username, email, password_hash, full_name, phone, address, role, max_borrow_limit)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT), // Always hash new passwords
            $data['full_name'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['role'] ?? 'member',
            $data['max_borrow_limit'] ?? 5
        ]);
    }

    public function getAll() {
        $sql = "SELECT id, username, email, full_name, role, status, created_at FROM users ORDER BY id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT id, username, email, full_name, role, status FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $sql = "
            UPDATE users 
            SET username = ?, email = ?, full_name = ?, phone = ?, address = ?, role = ?, status = ?
            WHERE id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['username'],
            $data['email'],
            $data['full_name'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['role'],
            $data['status'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function changePassword($id, $newPassword) {
        $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $id
        ]);
    }
}