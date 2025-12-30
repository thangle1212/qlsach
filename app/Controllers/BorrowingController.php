<?php
require_once __DIR__ . '/../Models/Borrowing.php';
require_once __DIR__ . '/../Models/Book.php';
require_once __DIR__ . '/../Models/User.php'; // Add User model
require_once __DIR__ . '/../Models/Reservation.php'; // Add Reservation model

class BorrowingController {
    public function __construct() {
        $this->checkAuth();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
    }

    public function index() {
        $borrowings = (new Borrowing())->getAll();
        require __DIR__ . '/../Views/borrowings/index.php';
    }

    public function create() {
        // Only allow admin and librarian to create borrowings
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=borrowing");
            exit;
        }
        
        $books = (new Book())->getAll();
        $users = (new User())->getAll(); // Get all users to display in dropdown
        require __DIR__ . '/../Views/borrowings/create.php';
    }

    public function store() {
        // Only allow admin and librarian to create borrowings
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=borrowing");
            exit;
        }
        
        (new Borrowing())->borrow(
            $_POST['user_id'],
            $_POST['book_id'],
            $_POST['due_date'],
            $_SESSION['user_id'] // Pass the librarian ID who processed the borrowing
        );
        header("Location: index.php?controller=borrowing");
    }

    public function return() {
        // Only allow admin and librarian to process returns
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=borrowing");
            exit;
        }

        (new Borrowing())->returnBook($_GET['id']);
        header("Location: index.php?controller=borrowing");
    }

    public function renew() {
        // Only allow admin and librarian to renew borrowings
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=borrowing");
            exit;
        }

        if ((new Borrowing())->renew($_GET['id'])) {
            $_SESSION['success'] = 'Gia hạn mượn sách thành công';
        } else {
            $_SESSION['error'] = 'Gia hạn mượn sách thất bại';
        }

        header("Location: index.php?controller=borrowing");
    }

    public function viewMember() {
        // Only allow admin and librarian to view member information
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=borrowing");
            exit;
        }

        $user_id = $_GET['user_id'];
        $user = (new User())->getById($user_id);
        $userBorrowings = (new Borrowing())->getByUserId($user_id);

        require __DIR__ . '/../Views/borrowings/member_info.php';
    }

    public function reservations() {
        // Only allow admin and librarian to manage reservations
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=borrowing");
            exit;
        }

        $reservations = (new Reservation())->getAll();
        require __DIR__ . '/../Views/borrowings/reservations.php';
    }

    public function approveReservation() {
        // Only allow admin and librarian to approve reservations
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=borrowing");
            exit;
        }

        $reservation_id = $_GET['id'];
        $reservation = (new Reservation())->getById($reservation_id);

        if (!$reservation) {
            $_SESSION['error'] = 'Đặt trước không tồn tại';
            header("Location: index.php?controller=borrowing&action=reservations");
            exit;
        }

        // Update reservation status to 'available'
        $result = (new Reservation())->update($reservation_id, [
            'status' => 'available',
            'priority' => 1
        ]);

        if ($result) {
            $_SESSION['success'] = 'Duyệt đặt trước thành công';
        } else {
            $_SESSION['error'] = 'Duyệt đặt trước thất bại';
        }

        header("Location: index.php?controller=borrowing&action=reservations");
    }

    public function rejectReservation() {
        // Only allow admin and librarian to reject reservations
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=borrowing");
            exit;
        }

        $reservation_id = $_GET['id'];
        $reservation = (new Reservation())->getById($reservation_id);

        if (!$reservation) {
            $_SESSION['error'] = 'Đặt trước không tồn tại';
            header("Location: index.php?controller=borrowing&action=reservations");
            exit;
        }

        // Cancel the reservation
        $result = (new Reservation())->cancel($reservation_id);

        if ($result) {
            $_SESSION['success'] = 'Từ chối đặt trước thành công';
        } else {
            $_SESSION['error'] = 'Từ chối đặt trước thất bại';
        }

        header("Location: index.php?controller=borrowing&action=reservations");
    }
}