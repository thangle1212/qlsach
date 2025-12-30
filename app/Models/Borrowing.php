<?php
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/Settings.php'; // Include Settings model

class Borrowing {
    private $db;
    private $settings;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->settings = new Settings();
        // Only initialize defaults if settings table exists
        try {
            $this->settings->initializeDefaults();
        } catch (Exception $e) {
            // If settings table doesn't exist, skip initialization
            // This will be handled when the table is created
        }
    }

    public function getAll() {
        $sql = "
            SELECT br.*, u.full_name, b.title
            FROM borrowings br
            JOIN users u ON br.user_id = u.id
            JOIN books b ON br.book_id = b.id
            ORDER BY br.id DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ======================
       MƯỢN SÁCH
    ====================== */
    public function borrow($user_id, $book_id, $due_date, $librarian_id = null) {
        $this->db->beginTransaction();

        // 1. kiểm tra còn sách
        $stmt = $this->db->prepare("
            SELECT available_copies FROM books WHERE id=?
        ");
        $stmt->execute([$book_id]);
        if ($stmt->fetchColumn() <= 0) {
            throw new Exception("Sách đã hết");
        }

        // 2. kiểm tra giới hạn mượn sách của người dùng
        // Only check settings if the table exists
        $max_books_per_user = 5; // Default value
        try {
            $max_books_per_user = (int)$this->settings->get('max_books_per_user');
        } catch (Exception $e) {
            // Use default if settings table doesn't exist
        }
        
        $stmt = $this->db->prepare("
            SELECT current_borrow_count, max_borrow_limit FROM users WHERE id=?
        ");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user['current_borrow_count'] >= min($user['max_borrow_limit'], $max_books_per_user)) {
            throw new Exception("Đã đạt giới hạn mượn sách");
        }

        // 3. tạo phiếu mượn
        $this->db->prepare("
            INSERT INTO borrowings(user_id, book_id, borrow_date, due_date, librarian_id)
            VALUES (?, ?, CURDATE(), ?, ?)
        ")->execute([$user_id, $book_id, $due_date, $librarian_id]); // Record who processed the borrowing

        // 4. giảm số lượng sách
        $this->db->prepare("
            UPDATE books SET available_copies = available_copies - 1
            WHERE id=?
        ")->execute([$book_id]);

        // 5. tăng số sách user đang mượn
        $this->db->prepare("
            UPDATE users SET current_borrow_count = current_borrow_count + 1
            WHERE id=?
        ")->execute([$user_id]);

        $this->db->commit();
    }

    /* ======================
       TRẢ SÁCH
    ====================== */
    public function returnBook($id) {
        $this->db->beginTransaction();

        $stmt = $this->db->prepare("
            SELECT br.*, u.current_borrow_count FROM borrowings br
            JOIN users u ON br.user_id = u.id
            WHERE br.id=?
        ");
        $stmt->execute([$id]);
        $borrowing = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$borrowing) {
            throw new Exception("Không tìm thấy phiếu mượn");
        }

        // Check if the book is overdue and calculate fine if needed
        $due_date = new DateTime($borrowing['due_date']);
        $return_date = new DateTime($borrowing['return_date'] ?: date('Y-m-d'));
        $overdue_days = 0;
        
        if ($return_date > $due_date) {
            $interval = $due_date->diff($return_date);
            $overdue_days = $interval->days;
        }

        // Calculate fine if overdue (only if settings table exists)
        $fine_amount = 0;
        if ($overdue_days > 0) {
            $fine_per_day = 5000; // Default value
            try {
                $fine_per_day = (int)$this->settings->get('fine_per_day');
            } catch (Exception $e) {
                // Use default if settings table doesn't exist
            }
            
            $fine_amount = $overdue_days * $fine_per_day;
            
            // Add fine record
            $this->db->prepare("
                INSERT INTO fines (user_id, borrowing_id, amount, reason, status)
                VALUES (?, ?, ?, 'overdue', 'unpaid')
            ")->execute([$borrowing['user_id'], $id, $fine_amount]);
        }

        // cập nhật phiếu mượn
        $this->db->prepare("
            UPDATE borrowings
            SET status='returned', return_date=CURDATE()
            WHERE id=?
        ")->execute([$id]);

        // tăng lại số lượng sách
        $this->db->prepare("
            UPDATE books SET available_copies = available_copies + 1
            WHERE id=?
        ")->execute([$borrowing['book_id']]);

        // giảm số sách user đang mượn
        $this->db->prepare("
            UPDATE users SET current_borrow_count = current_borrow_count - 1
            WHERE id=?
        ")->execute([$borrowing['user_id']]);

        $this->db->commit();
    }
    
    /* ======================
       KIỂM TRA PHIẾU MƯỢN QUÁ HẠN
    ====================== */
    public function getOverdueBorrowings() {
        $sql = "
            SELECT br.*, u.full_name, b.title,
            DATEDIFF(CURDATE(), br.due_date) AS overdue_days
            FROM borrowings br
            JOIN users u ON br.user_id = u.id
            JOIN books b ON br.book_id = b.id
            WHERE br.status = 'borrowed' AND br.due_date < CURDATE()
            ORDER BY br.due_date ASC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUserId($user_id) {
        $stmt = $this->db->prepare("
            SELECT br.*, b.title, b.isbn, b.cover_image
            FROM borrowings br
            JOIN books b ON br.book_id = b.id
            WHERE br.user_id = ?
            ORDER BY br.borrow_date DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT br.*, u.full_name, b.title
            FROM borrowings br
            JOIN users u ON br.user_id = u.id
            JOIN books b ON br.book_id = b.id
            WHERE br.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkActiveBorrow($user_id, $book_id) {
        $stmt = $this->db->prepare(
            "SELECT id FROM borrowings
             WHERE user_id = ? AND book_id = ? AND status = 'borrowed'"
        );
        $stmt->execute([$user_id, $book_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function renew($id) {
        try {
            // Get current due date
            $stmt = $this->db->prepare("SELECT due_date FROM borrowings WHERE id = ?");
            $stmt->execute([$id]);
            $borrowing = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$borrowing) {
                return false;
            }

            // Extend due date by max_borrow_days
            $max_days = 14; // Default
            try {
                $max_days = (int)$this->settings->get('max_borrow_days');
            } catch (Exception $e) {
                // Use default
            }

            $new_due_date = date('Y-m-d', strtotime($borrowing['due_date'] . " +$max_days days"));

            $stmt = $this->db->prepare("UPDATE borrowings SET due_date = ? WHERE id = ?");
            return $stmt->execute([$new_due_date, $id]);
        } catch (PDOException $e) {
            error_log("Borrowing Renew Error: " . $e->getMessage());
            return false;
        }
    }

    public function getActiveBorrowingsCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM borrowings WHERE status = 'borrowed'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}