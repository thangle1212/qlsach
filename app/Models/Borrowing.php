<?php
require_once __DIR__ . '/../Core/Database.php';

/**
 * Legacy Borrowing Model - Updated to work with new schema
 * This model maintains compatibility with existing code while using the new database structure
 */
class Borrowing {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get all loan slips (equivalent to old borrowings)
     */
    public function getAll() {
        $sql = "
            SELECT ls.*, u.full_name, b.title
            FROM loan_slips ls
            JOIN users u ON ls.user_id = u.id
            JOIN loan_items li ON ls.id = li.loan_id
            JOIN books b ON li.book_id = b.id
            GROUP BY ls.id
            ORDER BY ls.id DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get borrowing by user ID (for backward compatibility)
     */
    public function getByUserId($user_id) {
        $sql = "
            SELECT ls.*, u.full_name, b.title, b.isbn, b.cover_image
            FROM loan_slips ls
            JOIN users u ON ls.user_id = u.id
            JOIN loan_items li ON ls.id = li.loan_id
            JOIN books b ON li.book_id = b.id
            WHERE ls.user_id = ?
            ORDER BY ls.borrow_date DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if user is actively borrowing a specific book (for backward compatibility)
     */
    public function checkActiveBorrow($user_id, $book_id) {
        $sql = "SELECT COUNT(*) FROM loan_items li
                JOIN loan_slips ls ON li.loan_id = ls.id
                WHERE ls.user_id = ? AND li.book_id = ? AND li.status = 'borrowed'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $book_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get borrowing by ID (for backward compatibility)
     */
    public function getById($id) {
        $sql = "
            SELECT ls.*, u.full_name, b.title
            FROM loan_slips ls
            JOIN users u ON ls.user_id = u.id
            JOIN loan_items li ON ls.id = li.loan_id
            JOIN books b ON li.book_id = b.id
            WHERE ls.id = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all overdue borrowings (for backward compatibility)
     */
    public function getOverdueBorrowings() {
        $sql = "
            SELECT ls.*, u.full_name, b.title,
            DATEDIFF(CURDATE(), ls.due_date) AS overdue_days
            FROM loan_slips ls
            JOIN users u ON ls.user_id = u.id
            JOIN loan_items li ON ls.id = li.loan_id
            JOIN books b ON li.book_id = b.id
            WHERE ls.status = 'active' AND ls.due_date < CURDATE()
            GROUP BY ls.id
            ORDER BY ls.due_date ASC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Borrow books (legacy method - now uses BorrowService)
     * @deprecated Use BorrowService instead
     */
    public function borrow($user_id, $book_id, $due_date, $librarian_id = null) {
        // This method is deprecated. Use BorrowService instead.
        // Maintained for backward compatibility.
        $borrowService = new \BorrowService();
        $borrowService->borrowBooks($user_id, [$book_id], $due_date, $librarian_id);
    }

    /**
     * Return book (legacy method - now uses ReturnService)
     * @deprecated Use ReturnService instead
     */
    public function returnBook($id) {
        // This method is deprecated. Use ReturnService instead.
        // Maintained for backward compatibility.
        $returnService = new \ReturnService();
        
        // Get returnable items for this loan
        $returnableItems = $returnService->getReturnableItems($id);
        
        if (empty($returnableItems)) {
            throw new Exception("No items to return for this loan");
        }
        
        // Return all items
        $returnItems = [];
        foreach ($returnableItems as $item) {
            $returnItems[$item['id']] = $item['returnable_quantity'];
        }
        
        $returnService->returnBooks($id, $returnItems, null);
    }

    /**
     * Renew borrowing (legacy method)
     * @deprecated Use renewal in BorrowingController instead
     */
    public function renew($id) {
        try {
            // Get current due date and extend it
            $sql = "SELECT due_date FROM loan_slips WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $loan = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$loan) {
                return false;
            }
            
            $settings = new \Settings();
            $maxBorrowDays = (int)$settings->get('max_borrow_days', 14);
            $newDueDate = date('Y-m-d', strtotime($loan['due_date'] . " +{$maxBorrowDays} days"));
            
            $updateSql = "UPDATE loan_slips SET due_date = ? WHERE id = ?";
            $stmt = $this->db->prepare($updateSql);
            return $stmt->execute([$newDueDate, $id]);
        } catch (PDOException $e) {
            error_log("Borrowing Renew Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get active borrowings count
     */
    public function getActiveBorrowingsCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM loan_slips WHERE status = 'active'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}