<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Book.php';
require_once __DIR__ . '/../Models/Borrowing.php';
require_once __DIR__ . '/../Models/Reservation.php';
require_once __DIR__ . '/../Models/Fine.php';
require_once __DIR__ . '/../Services/BorrowService.php';
require_once __DIR__ . '/../Services/ReturnService.php';
require_once __DIR__ . '/../Services/FineService.php';

class MemberController {
    private $user;
    private $book;
    private $borrowing;
    private $reservation;
    private $fine;

    public function __construct() {
        $this->checkAuth();
        $this->user = new User();
        $this->book = new Book();
        $this->borrowing = new Borrowing();
        $this->reservation = new Reservation();
        $this->fine = new Fine();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
        // Only members can access member functions
        if ($_SESSION['role'] !== 'member') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header("Location: index.php?controller=book");
            exit;
        }
    }

    // Dashboard
    public function dashboard() {
        $user_id = $_SESSION['user_id'];
        $user = $this->user->getById($user_id);

        // Get current borrowings using new service
        $borrowService = new \BorrowService();
        $activeLoans = $borrowService->getUserActiveLoans($user_id);

        // Get detailed loan information
        $loanDetails = [];
        if (!empty($activeLoans)) {
            foreach ($activeLoans as $loan) {
                $loanDetails[$loan['id']] = $borrowService->getLoanDetails($loan['id']);
            }
        }

        // Get reservations
        $reservations = $this->reservation->getByUserId($user_id);

        // Get fines using new service
        $fineService = new \FineService();
        $fines = $fineService->getUserFines($user_id);

        // Calculate total unpaid fines
        $totalUnpaidFines = $fineService->getTotalUnpaidFines($user_id);

        require __DIR__ . '/../Views/member/dashboard.php';
    }

    // View profile
    public function profile() {
        $user_id = $_SESSION['user_id'];
        $user = $this->user->getById($user_id);
        require __DIR__ . '/../Views/member/profile/index.php';
    }

    // Update profile
    public function updateProfile() {
        $errors = $this->validateProfile($_POST);
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: index.php?controller=member&action=profile");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $data = [
            'full_name' => trim($_POST['full_name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']) ?? null,
            'address' => trim($_POST['address']) ?? null
        ];

        if ($this->user->update($user_id, $data)) {
            $_SESSION['success'] = 'Cập nhật hồ sơ thành công';
        } else {
            $_SESSION['error'] = 'Cập nhật hồ sơ thất bại';
        }
        
        header("Location: index.php?controller=member&action=profile");
        exit;
    }

    // Change password
    public function changePassword() {
        require __DIR__ . '/../Views/member/profile/change_password.php';
    }

    public function updatePassword() {
        $errors = $this->validatePassword($_POST);
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: index.php?controller=member&action=changePassword");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $user = $this->user->getById($user_id);

        // Verify old password
        if (!password_verify($_POST['old_password'], $user['password_hash'])) {
            $_SESSION['error'] = 'Mật khẩu cũ không chính xác';
            header("Location: index.php?controller=member&action=changePassword");
            exit;
        }

        $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        
        if ($this->user->updatePassword($user_id, $new_password)) {
            $_SESSION['success'] = 'Đổi mật khẩu thành công';
        } else {
            $_SESSION['error'] = 'Đổi mật khẩu thất bại';
        }

        header("Location: index.php?controller=member&action=profile");
        exit;
    }

    // View borrowing history
    public function borrowingHistory() {
        $user_id = $_SESSION['user_id'];
        $borrowService = new \BorrowService();
        $loans = $borrowService->getUserActiveLoans($user_id);

        // Get all loans (both active and completed)
        $db = \Database::getInstance();
        $sql = "SELECT ls.*, u.full_name as user_name, lib.full_name as librarian_name
                FROM loan_slips ls
                LEFT JOIN users u ON ls.user_id = u.id
                LEFT JOIN users lib ON ls.librarian_id = lib.id
                WHERE ls.user_id = ?
                ORDER BY ls.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([$user_id]);
        $allLoans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/member/borrowing_history.php';
    }

    // View books for members
    public function books() {
        $search = $_GET['search'] ?? '';

        if (!empty($search)) {
            // Search books by title
            $books = $this->book->search($search);
        } else {
            $books = $this->book->getAll();
        }

        require __DIR__ . '/../Views/member/books/index.php';
    }

    // Borrow book
    public function borrow() {
        $book_id = null;

        // Check if book_id is in POST or GET
        if (isset($_POST['book_id']) && !empty($_POST['book_id'])) {
            $book_id = intval($_POST['book_id']);
        } elseif (isset($_GET['book_id']) && !empty($_GET['book_id'])) {
            $book_id = intval($_GET['book_id']);
        }

        if (!$book_id) {
            $_SESSION['error'] = 'ID sách không hợp lệ';
            header("Location: index.php?controller=book");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Check if book exists
        $book = $this->book->getById($book_id);
        if (!$book) {
            $_SESSION['error'] = 'Sách không tồn tại';
            header("Location: index.php?controller=book");
            exit;
        }

        // Check if book is available
        if ($book['available_copies'] <= 0) {
            $_SESSION['error'] = 'Sách này không còn sẵn có';
            header("Location: index.php?controller=book");
            exit;
        }

        // Check if user already borrowed this book (not returned yet)
        $exists = $this->checkActiveBorrowForMember($user_id, $book_id);
        if ($exists) {
            $_SESSION['error'] = 'Bạn đã mượn sách này rồi';
            header("Location: index.php?controller=book");
            exit;
        }

        // Get max borrow days from settings
        $max_days = 14; // Default
        try {
            $settings = new \Settings();
            $max_days = (int)$settings->get('max_borrow_days', 14);
        } catch (Exception $e) {
            // Use default if settings table doesn't exist
        }

        $due_date = date('Y-m-d', strtotime("+$max_days days"));

        try {
            // Use the new BorrowService
            $borrowService = new \BorrowService();
            $borrowService->borrowBooks($user_id, [$book_id], $due_date, null); // librarian_id is null for member-initiated borrowing
            $_SESSION['success'] = 'Mượn sách thành công';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Mượn sách thất bại: ' . $e->getMessage();
        }

        header("Location: index.php?controller=member&action=dashboard");
        exit;
    }

    // Method to allow borrowing multiple books from the book list
    public function borrowMultiple() {
        if (!isset($_POST['book_ids']) || empty($_POST['book_ids'])) {
            $_SESSION['error'] = 'Chưa chọn sách để mượn';
            header("Location: index.php?controller=book");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $book_ids = array_filter(array_map('intval', $_POST['book_ids'])); // Convert to integers and remove zeros

        if (empty($book_ids)) {
            $_SESSION['error'] = 'Chưa chọn sách để mượn';
            header("Location: index.php?controller=book");
            exit;
        }

        // Validate each book
        foreach ($book_ids as $book_id) {
            if ($book_id <= 0) {
                $_SESSION['error'] = 'ID sách không hợp lệ';
                header("Location: index.php?controller=book");
                exit;
            }

            $book = $this->book->getById($book_id);
            if (!$book) {
                $_SESSION['error'] = "Sách ID {$book_id} không tồn tại";
                header("Location: index.php?controller=book");
                exit;
            }

            // Check if book is available
            if ($book['available_copies'] <= 0) {
                $_SESSION['error'] = "Sách '{$book['title']}' không còn sẵn có";
                header("Location: index.php?controller=book");
                exit;
            }

            // Check if user already borrowed this book (not returned yet)
            $exists = $this->checkActiveBorrowForMember($user_id, $book_id);
            if ($exists) {
                $_SESSION['error'] = "Bạn đã mượn sách '{$book['title']}' rồi";
                header("Location: index.php?controller=book");
                exit;
            }
        }

        // Get max borrow days from settings
        $max_days = 14; // Default
        try {
            $settings = new \Settings();
            $max_days = (int)$settings->get('max_borrow_days', 14);
        } catch (Exception $e) {
            // Use default if settings table doesn't exist
        }

        $due_date = date('Y-m-d', strtotime("+$max_days days"));

        try {
            // Use the new BorrowService
            $borrowService = new \BorrowService();
            $borrowService->borrowBooks($user_id, $book_ids, $due_date, null); // librarian_id is null for member-initiated borrowing
            $_SESSION['success'] = 'Mượn sách thành công';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Mượn sách thất bại: ' . $e->getMessage();
        }

        header("Location: index.php?controller=member&action=dashboard");
        exit;
    }

    // View reservations
    public function reservations() {
        $user_id = $_SESSION['user_id'];
        $reservations = $this->reservation->getByUserId($user_id);
        require __DIR__ . '/../Views/member/reservations.php';
    }

    // Make reservation
    public function reserve() {
        if (!isset($_POST['book_id']) || empty($_POST['book_id'])) {
            $_SESSION['error'] = 'ID sách không hợp lệ';
            header("Location: index.php?controller=book");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $book_id = intval($_POST['book_id']);
        
        // Check if book exists
        $book = $this->book->getById($book_id);
        if (!$book) {
            $_SESSION['error'] = 'Sách không tồn tại';
            header("Location: index.php?controller=book");
            exit;
        }

        // Check if already reserved
        $exists = $this->reservation->checkActiveReservation($user_id, $book_id);
        if ($exists) {
            $_SESSION['error'] = 'Bạn đã đặt trước sách này rồi';
            header("Location: index.php?controller=book");
            exit;
        }

        // Create reservation
        $data = [
            'user_id' => $user_id,
            'book_id' => $book_id,
            'reservation_date' => date('Y-m-d'),
            'expiry_date' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'pending'
        ];

        if ($this->reservation->create($data)) {
            $_SESSION['success'] = 'Đặt trước sách thành công';
        } else {
            $_SESSION['error'] = 'Đặt trước sách thất bại';
        }

        header("Location: index.php?controller=book");
        exit;
    }

    // Cancel reservation
    public function cancelReservation() {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['error'] = 'ID đặt trước không hợp lệ';
            header("Location: index.php?controller=member&action=reservations");
            exit;
        }

        $reservation_id = intval($_GET['id']);
        $user_id = $_SESSION['user_id'];

        $reservation = $this->reservation->getById($reservation_id);
        if (!$reservation || $reservation['user_id'] != $user_id) {
            $_SESSION['error'] = 'Đặt trước không tồn tại';
            header("Location: index.php?controller=member&action=reservations");
            exit;
        }

        // Only allow cancellation if the reservation is still pending
        if ($reservation['status'] !== 'pending') {
            $_SESSION['error'] = 'Chỉ có thể hủy đặt trước đang chờ xử lý';
            header("Location: index.php?controller=member&action=reservations");
            exit;
        }

        if ($this->reservation->cancel($reservation_id)) {
            $_SESSION['success'] = 'Hủy đặt trước thành công';
        } else {
            $_SESSION['error'] = 'Hủy đặt trước thất bại';
        }

        header("Location: index.php?controller=member&action=reservations");
        exit;
    }

    // View fines
    public function fines() {
        $user_id = $_SESSION['user_id'];
        $fineService = new \FineService();
        $fines = $fineService->getUnpaidFines($user_id);
        $totalUnpaid = $fineService->getTotalUnpaidFines($user_id);
        require __DIR__ . '/../Views/member/fines.php';
    }

    private function validateProfile($data) {
        $errors = [];

        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Họ và tên không được để trống';
        } elseif (strlen($data['full_name']) > 100) {
            $errors['full_name'] = 'Họ và tên không vượt quá 100 ký tự';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'Email không được để trống';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }

        if (!empty($data['phone'])) {
            if (!preg_match('/^[0-9\-\+\s\(\)]{7,20}$/', $data['phone'])) {
                $errors['phone'] = 'Số điện thoại không hợp lệ';
            }
        }

        return $errors;
    }

    private function validatePassword($data) {
        $errors = [];

        if (empty($data['old_password'])) {
            $errors['old_password'] = 'Mật khẩu cũ không được để trống';
        }

        if (empty($data['new_password'])) {
            $errors['new_password'] = 'Mật khẩu mới không được để trống';
        } elseif (strlen($data['new_password']) < 6) {
            $errors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        }

        if (empty($data['confirm_password'])) {
            $errors['confirm_password'] = 'Xác nhận mật khẩu không được để trống';
        } elseif ($data['new_password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Xác nhận mật khẩu không khớp';
        }

        return $errors;
    }

    /**
     * Check if user is actively borrowing a specific book
     */
    private function checkActiveBorrowForMember($userId, $bookId) {
        $db = \Database::getInstance();

        $sql = "SELECT COUNT(*) FROM loan_items li
                JOIN loan_slips ls ON li.loan_id = ls.id
                WHERE ls.user_id = ? AND li.book_id = ? AND li.status = 'borrowed'";

        $stmt = $db->prepare($sql);
        $stmt->execute([$userId, $bookId]);

        return $stmt->fetchColumn() > 0;
    }
}
