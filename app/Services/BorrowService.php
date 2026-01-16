<?php
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/Settings.php';
require_once __DIR__ . '/../Models/Book.php';
require_once __DIR__ . '/../Models/User.php';

class BorrowService {
    private $db;
    private $settings;
    private $bookModel;
    private $userModel;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->settings = new Settings();
        $this->bookModel = new Book();
        $this->userModel = new User();
    }

    /**
     * Process a new book borrowing
     * 
     * @param int $userId ID of the user borrowing books
     * @param array $bookIds Array of book IDs to borrow
     * @param string $dueDate Due date for the borrowed books
     * @param int|null $librarianId ID of the librarian processing the loan (optional)
     * @return bool True if successful, false otherwise
     */
    public function borrowBooks($userId, $bookIds, $dueDate, $librarianId = null) {
        try {
            $this->db->beginTransaction();

            // Validate inputs
            if (empty($userId) || empty($bookIds) || empty($dueDate)) {
                throw new Exception("Missing required parameters for borrowing");
            }

            // Get user info
            $user = $this->userModel->getById($userId);
            if (!$user) {
                throw new Exception("User not found");
            }

            // Check if user is active
            if ($user['status'] !== 'active') {
                throw new Exception("User account is not active");
            }

            // Get current borrow count for the user
            $currentBorrowCount = $this->getCurrentBorrowCount($userId);
            
            // Get max borrow limit from settings
            $maxBorrowDays = (int)$this->settings->get('max_borrow_days', 14);
            $maxBooksPerUser = (int)$this->settings->get('max_books_per_user', 5);
            $maxBorrowLimit = min($user['max_borrow_limit'], $maxBooksPerUser);

            // Validate borrow limit
            if (($currentBorrowCount + count($bookIds)) > $maxBorrowLimit) {
                throw new Exception("Exceeds user's borrow limit of {$maxBorrowLimit}");
            }

            // Validate each book and check availability
            foreach ($bookIds as $bookId) {
                $book = $this->bookModel->getById($bookId);
                
                if (!$book) {
                    throw new Exception("Book with ID {$bookId} not found");
                }

                // Check if book is available
                if ($book['available_copies'] <= 0) {
                    throw new Exception("Book '{$book['title']}' is not available");
                }

                // Check if book is a reference book (cannot be borrowed)
                if ($book['is_reference']) {
                    throw new Exception("Reference book '{$book['title']}' cannot be borrowed");
                }

                // Check if user already has this book borrowed
                if ($this->isUserBorrowingBook($userId, $bookId)) {
                    throw new Exception("User is already borrowing book '{$book['title']}'");
                }
            }

            // Create loan slip record
            $loanSlipId = $this->createLoanSlip($userId, $librarianId, $dueDate);

            // Create loan items for each book
            foreach ($bookIds as $bookId) {
                $this->createLoanItem($loanSlipId, $bookId);
                
                // Update book availability
                $this->updateBookAvailability($bookId, -1);
            }

            // Update user's current borrow count
            $this->updateUserBorrowCount($userId, count($bookIds));

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("BorrowService::borrowBooks Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a loan slip record
     */
    private function createLoanSlip($userId, $librarianId, $dueDate) {
        $sql = "INSERT INTO loan_slips (user_id, librarian_id, borrow_date, due_date, status) 
                VALUES (?, ?, CURDATE(), ?, 'active')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $librarianId, $dueDate]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Create a loan item record
     */
    private function createLoanItem($loanId, $bookId) {
        $sql = "INSERT INTO loan_items (loan_id, book_id, quantity, status) 
                VALUES (?, ?, 1, 'borrowed')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loanId, $bookId]);
    }

    /**
     * Update book availability
     */
    private function updateBookAvailability($bookId, $change) {
        $sql = "UPDATE books SET available_copies = available_copies + ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$change, $bookId]);
    }

    /**
     * Update user's current borrow count
     */
    private function updateUserBorrowCount($userId, $change) {
        $sql = "UPDATE users SET current_borrow_count = current_borrow_count + ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$change, $userId]);
    }

    /**
     * Check if user is already borrowing a specific book
     */
    private function isUserBorrowingBook($userId, $bookId) {
        $sql = "SELECT COUNT(*) FROM loan_items li
                JOIN loan_slips ls ON li.loan_id = ls.id
                WHERE ls.user_id = ? AND li.book_id = ? AND li.status = 'borrowed'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $bookId]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get current borrow count for a user
     */
    private function getCurrentBorrowCount($userId) {
        $sql = "SELECT current_borrow_count FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetchColumn() ?: 0;
    }

    /**
     * Get active loans for a user
     */
    public function getUserActiveLoans($userId) {
        $sql = "SELECT ls.*, u.full_name as user_name, lib.full_name as librarian_name
                FROM loan_slips ls
                LEFT JOIN users u ON ls.user_id = u.id
                LEFT JOIN users lib ON ls.librarian_id = lib.id
                WHERE ls.user_id = ? AND ls.status = 'active'
                ORDER BY ls.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get loan details with books
     */
    public function getLoanDetails($loanId) {
        $sql = "SELECT li.*, b.title, b.isbn, ls.due_date, ls.borrow_date, ls.status as loan_status
                FROM loan_items li
                JOIN loan_slips ls ON li.loan_id = ls.id
                JOIN books b ON li.book_id = b.id
                WHERE li.loan_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loanId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all active loans
     */
    public function getAllActiveLoans() {
        $sql = "SELECT ls.*, u.full_name as user_name, lib.full_name as librarian_name
                FROM loan_slips ls
                LEFT JOIN users u ON ls.user_id = u.id
                LEFT JOIN users lib ON ls.librarian_id = lib.id
                WHERE ls.status = 'active'
                ORDER BY ls.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get overdue loans
     */
    public function getOverdueLoans() {
        $sql = "SELECT ls.*, u.full_name as user_name, lib.full_name as librarian_name,
                       DATEDIFF(CURDATE(), ls.due_date) as overdue_days
                FROM loan_slips ls
                LEFT JOIN users u ON ls.user_id = u.id
                LEFT JOIN users lib ON ls.librarian_id = lib.id
                WHERE ls.status = 'active' AND ls.due_date < CURDATE()
                ORDER BY ls.due_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get loan slip by ID
     */
    public function getLoanSlipById($loanId) {
        $sql = "SELECT ls.*, u.full_name as user_name, lib.full_name as librarian_name
                FROM loan_slips ls
                LEFT JOIN users u ON ls.user_id = u.id
                LEFT JOIN users lib ON ls.librarian_id = lib.id
                WHERE ls.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loanId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Renew a loan by extending the due date
     */
    public function renewLoan($loanId) {
        try {
            $settings = new \Settings();
            $maxBorrowDays = (int)$settings->get('max_borrow_days', 14);

            // Get current due date and extend it
            $sql = "SELECT due_date FROM loan_slips WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$loanId]);
            $loan = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$loan) {
                throw new Exception("Loan slip not found");
            }

            $newDueDate = date('Y-m-d', strtotime($loan['due_date'] . " +{$maxBorrowDays} days"));

            $updateSql = "UPDATE loan_slips SET due_date = ? WHERE id = ?";
            $stmt = $this->db->prepare($updateSql);
            return $stmt->execute([$newDueDate, $loanId]);
        } catch (Exception $e) {
            error_log("BorrowService::renewLoan Error: " . $e->getMessage());
            throw $e;
        }
    }
}