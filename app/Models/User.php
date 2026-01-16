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
            // So sánh mật khẩu nguyên bản
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
            $data['password'], // Lưu mật khẩu dưới dạng nguyên bản
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
        try {
            // If all fields provided (for admin updates)
            if (isset($data['username']) && isset($data['email']) && isset($data['role'])) {
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
            } else {
                // For member profile updates
                $sql = "
                    UPDATE users 
                    SET full_name = ?, phone = ?, address = ?, email = ?
                    WHERE id = ?
                ";
                
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    $data['full_name'],
                    $data['phone'] ?? null,
                    $data['address'] ?? null,
                    $data['email'],
                    $id
                ]);
            }
        } catch (PDOException $e) {
            error_log("User Update Error: " . $e->getMessage());
            return false;
        }
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

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($id, $password_hash) {
        try {
            // Lưu mật khẩu dưới dạng nguyên bản thay vì hash
            $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            return $stmt->execute([$password_hash, $id]);
        } catch (PDOException $e) {
            error_log("User Update Password Error: " . $e->getMessage());
            return false;
        }
    }
}