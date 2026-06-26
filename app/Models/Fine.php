<?php
require_once __DIR__ . '/../Core/Database.php';

class Fine {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->db->prepare("
            SELECT f.*, u.full_name, ls.borrow_date, ls.due_date
            FROM fines f
            JOIN users u ON f.user_id = u.id
            JOIN loan_slips ls ON f.loan_id = ls.id
            ORDER BY f.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT f.*, u.full_name, ls.borrow_date, ls.due_date
            FROM fines f
            JOIN users u ON f.user_id = u.id
            JOIN loan_slips ls ON f.loan_id = ls.id
            WHERE f.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($user_id) {
        $stmt = $this->db->prepare("
            SELECT f.*, ls.borrow_date, ls.due_date, ls.return_date
            FROM fines f
            JOIN loan_slips ls ON f.loan_id = ls.id
            WHERE f.user_id = ?
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalUnpaidByUser($user_id) {
        $stmt = $this->db->prepare(
            "SELECT SUM(amount) as total FROM fines WHERE user_id = ? AND status = 'unpaid'"
        );
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function create($data) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO fines (user_id, loan_id, amount, reason, status, notes)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $data['user_id'],
                $data['loan_id'],
                $data['amount'],
                $data['reason'] ?? 'overdue',
                $data['status'] ?? 'unpaid',
                $data['notes'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Fine Create Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE fines SET status = ?, paid_date = ?, notes = ? WHERE id = ?"
            );
            return $stmt->execute([
                $data['status'],
                $data['paid_date'] ?? null,
                $data['notes'] ?? null,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Fine Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function markAsPaid($id) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE fines SET status = 'paid', paid_date = CURDATE() WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Fine Mark Paid Error: " . $e->getMessage());
            return false;
        }
    }

    public function getOverdueFines() {
        $stmt = $this->db->prepare("
            SELECT f.*, u.full_name, u.email, ls.borrow_date, ls.due_date
            FROM fines f
            JOIN users u ON f.user_id = u.id
            JOIN loan_slips ls ON f.loan_id = ls.id
            WHERE f.status = 'unpaid'
            ORDER BY f.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calculateOverdueFine($due_date, $fine_per_day = 5000) {
        $due = new DateTime($due_date);
        $today = new DateTime();
        
        if ($today > $due) {
            $interval = $today->diff($due);
            $days_overdue = $interval->days;
            return $days_overdue * $fine_per_day;
        }
        
        return 0;
    }
}
