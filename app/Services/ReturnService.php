<?php
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/Settings.php';
require_once __DIR__ . '/../Models/Book.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/FineService.php';

class ReturnService {
    private $db;
    private $settings;
    private $bookModel;
    private $userModel;
    private $fineService;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->settings = new Settings();
        $this->bookModel = new Book();
        $this->userModel = new User();
        $this->fineService = new FineService();
    }

    /**
     * Process a book return
     * 
     * @param int $loanSlipId ID of the loan slip being returned
     * @param array $returnItems Array of items to return [loan_item_id => quantity]
     * @param int|null $librarianId ID of the librarian processing the return
     * @param string|null $note Optional note for the return
     * @return bool True if successful, false otherwise
     */
    public function returnBooks($loanSlipId, $returnItems, $librarianId = null, $note = null) {
        try {
            $this->db->beginTransaction();

            // Validate inputs
            if (empty($loanSlipId) || empty($returnItems)) {
                throw new Exception("Missing required parameters for return");
            }

            // Get the loan slip
            $loanSlip = $this->getLoanSlip($loanSlipId);
            if (!$loanSlip) {
                throw new Exception("Loan slip not found");
            }

            if ($loanSlip['status'] !== 'active') {
                throw new Exception("Cannot return books from a non-active loan");
            }

            // Validate return items and check quantities
            foreach ($returnItems as $loanItemId => $quantity) {
                if ($quantity <= 0) {
                    throw new Exception("Return quantity must be greater than 0");
                }

                $loanItem = $this->getLoanItem($loanItemId);
                if (!$loanItem) {
                    throw new Exception("Loan item not found: {$loanItemId}");
                }

                if ($loanItem['loan_id'] != $loanSlipId) {
                    throw new Exception("Loan item does not belong to this loan slip");
                }

                if ($loanItem['status'] !== 'borrowed') {
                    throw new Exception("Item is not in borrowed status");
                }

                if ($quantity > ($loanItem['quantity'] - $loanItem['returned_quantity'])) {
                    throw new Exception("Return quantity exceeds borrowed quantity for item {$loanItemId}");
                }
            }

            // Create return slip
            $returnSlipId = $this->createReturnSlip($loanSlipId, $librarianId, $note);

            // Process each return item
            $booksReturned = [];
            foreach ($returnItems as $loanItemId => $quantity) {
                $this->processReturnItem($returnSlipId, $loanItemId, $quantity);
                
                // Get book info for later use
                $loanItem = $this->getLoanItem($loanItemId);
                $bookInfo = $this->bookModel->getById($loanItem['book_id']);
                
                $booksReturned[] = [
                    'book_id' => $loanItem['book_id'],
                    'title' => $bookInfo['title'],
                    'quantity' => $quantity
                ];
                
                // Update book availability
                $this->updateBookAvailability($loanItem['book_id'], $quantity);
            }

            // Update loan item status and check if loan is fully returned
            $fullyReturned = $this->checkAndUpdateLoanStatus($loanSlipId);

            // Update user's current borrow count
            $totalReturned = array_sum(array_column($booksReturned, 'quantity'));
            $this->updateUserBorrowCount($loanSlip['user_id'], -$totalReturned);

            // Check for overdue fines
            $this->handleOverdueFines($loanSlipId, $loanSlip);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("ReturnService::returnBooks Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a return slip record
     */
    private function createReturnSlip($loanId, $librarianId, $note) {
        $sql = "INSERT INTO return_slips (loan_id, return_date, librarian_id, note) 
                VALUES (?, CURDATE(), ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loanId, $librarianId, $note]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Process a return item
     */
    private function processReturnItem($returnId, $loanItemId, $quantity) {
        // Insert return item record
        $sql = "INSERT INTO return_items (return_id, loan_item_id, quantity) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$returnId, $loanItemId, $quantity]);

        // Update the loan item with returned quantity
        $updateSql = "UPDATE loan_items SET returned_quantity = returned_quantity + ?, 
                      status = CASE 
                          WHEN (quantity - returned_quantity - ?) <= 0 THEN 'returned'
                          ELSE 'borrowed'
                      END
                      WHERE id = ?";
        
        $stmt = $this->db->prepare($updateSql);
        $stmt->execute([$quantity, $quantity, $loanItemId]);
    }

    /**
     * Check if loan is fully returned and update status
     */
    private function checkAndUpdateLoanStatus($loanSlipId) {
        // Check if all items in the loan are returned
        $sql = "SELECT COUNT(*) as borrowed_count FROM loan_items 
                WHERE loan_id = ? AND status = 'borrowed'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loanSlipId]);
        $borrowedCount = $stmt->fetchColumn();

        if ($borrowedCount == 0) {
            // All items returned, update loan slip status to completed
            $updateSql = "UPDATE loan_slips SET status = 'completed' WHERE id = ?";
            $stmt = $this->db->prepare($updateSql);
            $stmt->execute([$loanSlipId]);
            return true;
        }
        
        return false;
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
     * Handle overdue fines for returned items
     */
    private function handleOverdueFines($loanSlipId, $loanSlip) {
        // Check if the loan was overdue
        if ($loanSlip['due_date'] < date('Y-m-d')) {
            // Calculate overdue days
            $dueDate = new DateTime($loanSlip['due_date']);
            $returnDate = new DateTime(date('Y-m-d'));
            $overdueDays = $dueDate->diff($returnDate)->days;

            if ($overdueDays > 0) {
                // Check if fine already exists for this loan
                $fineExists = $this->fineService->fineExistsForLoan($loanSlipId);
                
                if (!$fineExists) {
                    // Calculate fine amount
                    $finePerDay = (int)$this->settings->get('fine_per_day', 5000);
                    $fineAmount = $overdueDays * $finePerDay;

                    // Create fine record
                    $this->fineService->createFine($loanSlip['user_id'], $loanSlipId, $fineAmount, 'overdue');
                }
            }
        }
    }

    /**
     * Get loan slip by ID
     */
    private function getLoanSlip($loanSlipId) {
        $sql = "SELECT * FROM loan_slips WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loanSlipId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get loan item by ID
     */
    private function getLoanItem($loanItemId) {
        $sql = "SELECT * FROM loan_items WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loanItemId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get return details for a loan
     */
    public function getReturnDetails($loanId) {
        $sql = "SELECT rs.*, u.full_name as librarian_name
                FROM return_slips rs
                LEFT JOIN users u ON rs.librarian_id = u.id
                WHERE rs.loan_id = ?
                ORDER BY rs.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loanId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all returns for a user
     */
    public function getUserReturns($userId) {
        $sql = "SELECT rs.*, ls.borrow_date, ls.due_date, u.full_name as user_name
                FROM return_slips rs
                JOIN loan_slips ls ON rs.loan_id = ls.id
                LEFT JOIN users u ON ls.user_id = u.id
                WHERE ls.user_id = ?
                ORDER BY rs.return_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get loan items that can be returned (still in borrowed status)
     */
    public function getReturnableItems($loanSlipId) {
        $sql = "SELECT li.*, b.title, (li.quantity - li.returned_quantity) as returnable_quantity
                FROM loan_items li
                JOIN books b ON li.book_id = b.id
                WHERE li.loan_id = ? AND li.status = 'borrowed' AND (li.quantity - li.returned_quantity) > 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loanSlipId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}