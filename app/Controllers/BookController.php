<?php
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Author.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Publisher.php';

class BookController {
    private $book;

    public function __construct() {
        // Check if session is already active to avoid the notice
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->checkAuth();
        $this->book = new Book();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
    }

    // ================= DANH SÁCH =================
    public function index() {
        $search = $_GET['search'] ?? '';
        $books = !empty($search)
            ? $this->book->search($search)
            : $this->book->getAll();

        require __DIR__ . '/../views/books/index.php';
    }

    // ================= CREATE =================
    public function create() {
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền';
            header("Location: index.php?controller=book");
            exit;
        }

        $authors    = (new Author())->getAll();
        $categories = (new Category())->getAll();
        $publishers = (new Publisher())->getAll();

        require __DIR__ . '/../views/books/create.php';
    }

    public function store() {
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền';
            header("Location: index.php?controller=book");
            exit;
        }

        $this->book->insert($_POST);
        $_SESSION['success'] = 'Thêm sách thành công';
        header("Location: index.php?controller=book");
    }

    // ================= EDIT =================
    public function edit() {
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền';
            header("Location: index.php?controller=book");
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=book");
            exit;
        }

        $book = $this->book->find($id);
        if (!$book) {
            $_SESSION['error'] = 'Sách không tồn tại';
            header("Location: index.php?controller=book");
            exit;
        }

        // ⭐ BẮT BUỘC PHẢI CÓ
        $authors    = (new Author())->getAll();
        $categories = (new Category())->getAll();
        $publishers = (new Publisher())->getAll();

        require __DIR__ . '/../views/books/edit.php';
    }

    // ================= UPDATE =================
    public function update() {
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền';
            header("Location: index.php?controller=book");
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=book");
            exit;
        }

        if (empty($_POST['title']) || empty($_POST['total_copies'])) {
            $_SESSION['error'] = 'Thiếu thông tin bắt buộc';
            header("Location: index.php?controller=book&action=edit&id=$id");
            exit;
        }

        $data = [
            'title' => $_POST['title'],
            'isbn' => $_POST['isbn'] ?? null,
            'author_id' => $_POST['author_id'] ?? null,
            'publisher_id' => $_POST['publisher_id'] ?? null,
            'category_id' => $_POST['category_id'] ?? null,
            'total_copies' => (int)$_POST['total_copies'],
            'publication_year' => $_POST['publication_year'] ?? null,
            'pages' => $_POST['pages'] ?? null,
            'description' => $_POST['description'] ?? null
        ];

        $this->book->update($id, $data);
        $_SESSION['success'] = 'Cập nhật sách thành công';

        header("Location: index.php?controller=book&action=edit&id=$id");
    }

    // ================= DELETE =================
    public function delete() {
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền';
            header("Location: index.php?controller=book");
            exit;
        }

        if (!$this->book->delete($_GET['id'])) {
            $_SESSION['error'] = "Không thể xoá sách đang được mượn";
        }

        header("Location: index.php?controller=book");
    }

    public function show() {
    if (!isset($_GET['id'])) {
        header("Location: index.php?controller=book");
        exit;
    }

    $id = (int)$_GET['id'];

    $book = $this->book->findWithRelations($id);

    if (!$book) {
        $_SESSION['error'] = 'Không tìm thấy sách';
        header("Location: index.php?controller=book");
        exit;
    }

    require __DIR__ . '/../views/books/show.php';
}

}
