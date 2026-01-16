<?php
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/Settings.php';

class FineService {
    private $db;
    private $settings;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->settings = new Settings();
    }

    /**
     * Create a fine record
     */
    public function createFine($userId, $loanId, $amount, $reason = 'overdue') {
        $sql = "INSERT INTO fines (user_id, loan_id, amount, reason, status) 
                VALUES (?, ?, ?, ?, 'unpaid')";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $loanId, $amount, $reason]);
    }

    /**
     * Check if a fine already exists for a loan
     */
    public function fineExistsForLoan($loanId) {
        $sql = "SELECT COUNT(*) FROM fines WHERE loan_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loanId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get fines for a user
     */
    public function getUserFines($userId) {
        $sql = "SELECT f.*, ls.borrow_date, ls.due_date
                FROM fines f
                JOIN loan_slips ls ON f.loan_id = ls.id
                WHERE f.user_id = ?
                ORDER BY f.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all fines
     */
    public function getAllFines() {
        $sql = "SELECT f.*, u.full_name as user_name, ls.borrow_date, ls.due_date
                FROM fines f
                JOIN users u ON f.user_id = u.id
                JOIN loan_slips ls ON f.loan_id = ls.id
                ORDER BY f.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get unpaid fines for a user
     */
    public function getUnpaidFines($userId) {
        $sql = "SELECT f.*, ls.borrow_date, ls.due_date
                FROM fines f
                JOIN loan_slips ls ON f.loan_id = ls.id
                WHERE f.user_id = ? AND f.status = 'unpaid'
                ORDER BY f.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total unpaid amount for a user
     */
    public function getTotalUnpaidFines($userId) {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total_unpaid
                FROM fines 
                WHERE user_id = ? AND status = 'unpaid'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)$result['total_unpaid'];
    }

    /**
     * Mark a fine as paid
     */
    public function markAsPaid($fineId) {
        $sql = "UPDATE fines SET status = 'paid' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fineId]);
    }

    /**
     * Mark a fine as waived
     */
    public function markAsWaived($fineId) {
        $sql = "UPDATE fines SET status = 'waived' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fineId]);
    }

    /**
     * Calculate overdue fines for a specific loan
     */
    public function calculateOverdueFine($loanId) {
        $sql = "SELECT ls.due_date, ls.user_id FROM loan_slips ls WHERE ls.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loanId]);
        $loan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$loan) {
            return 0;
        }

        // Check if loan is overdue
        $dueDate = new DateTime($loan['due_date']);
        $today = new DateTime();
        
        if ($today > $dueDate) {
            $overdueDays = $dueDate->diff($today)->days;
            $finePerDay = (int)$this->settings->get('fine_per_day', 5000);
            return $overdueDays * $finePerDay;
        }

        return 0;
    }
}