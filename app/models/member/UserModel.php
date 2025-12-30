<?php
class UserModel
{
    private $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCurrentBorrowings($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM borrowings WHERE user_id = ? AND return_date IS NULL");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return []; // Return empty array if table doesn't exist
        }
    }

    public function getTotalUnpaidFines($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT SUM(amount) as total FROM fines WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0; // Return 0 if table doesn't exist
        }
    }
}
