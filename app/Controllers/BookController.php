<?php
require_once __DIR__ . '/../Models/Book.php';

class BookController {
    private $book;

    public function __construct() {
        $this->checkAuth();
        $this->book = new Book();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
    }

    public function index() {
        $books = $this->book->getAll();
        require __DIR__ . '/../Views/books/index.php';
    }

    public function create() {
        // Only allow admin and librarian to create books
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=book");
            exit;
        }
        require __DIR__ . '/../Views/books/create.php';
    }

    public function store() {
        // Only allow admin and librarian to create books
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=book");
            exit;
        }
        
        $this->book->insert($_POST);
        header("Location: index.php?controller=book");
    }

    public function edit() {
        // Only allow admin and librarian to edit books
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=book");
            exit;
        }
        
        $book = $this->book->find($_GET['id']);
        require __DIR__ . '/../Views/books/edit.php';
    }

    public function update() {
        // Only allow admin and librarian to update books
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=book");
            exit;
        }
        
        $this->book->update($_GET['id'], $_POST);
        header("Location: index.php?controller=book");
    }

    public function delete() {
        // Only allow admin and librarian to delete books
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header("Location: index.php?controller=book");
            exit;
        }
        
        if (!$this->book->delete($_GET['id'])) {
            $_SESSION['error'] = "Không thể xoá sách đang được mượn";
            header("Location: index.php?controller=book");
            exit;
        }
        header("Location: index.php?controller=book");
    }
}