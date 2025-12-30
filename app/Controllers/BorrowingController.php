<?php
require_once __DIR__ . '/../Models/Borrowing.php';
require_once __DIR__ . '/../Models/Book.php';
require_once __DIR__ . '/../Models/User.php'; // Add User model

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
}