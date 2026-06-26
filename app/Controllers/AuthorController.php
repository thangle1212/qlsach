<?php
require_once __DIR__ . '/../Models/Author.php';

class AuthorController {
    private $author;

    public function __construct() {
        $this->checkAuth();
        $this->author = new Author();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
        // Only admin and librarian can manage authors
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header("Location: index.php?controller=book");
            exit;
        }
    }

    public function index() {
        $authors = $this->author->getAll();
        require __DIR__ . '/../Views/authors/index.php';
    }

    public function create() {
        require __DIR__ . '/../Views/authors/create.php';
    }

    public function store() {
        $data = [
            'name' => $_POST['name'],
            'biography' => isset($_POST['biography']) ? $_POST['biography'] : null,
            'nationality' => isset($_POST['nationality']) ? $_POST['nationality'] : null,
            'birth_year' => isset($_POST['birth_year']) ? $_POST['birth_year'] : null,
            'death_year' => isset($_POST['death_year']) ? $_POST['death_year'] : null
        ];

        if ($this->author->create($data)) {
            $_SESSION['success'] = 'Tạo tác giả thành công';
        } else {
            $_SESSION['error'] = 'Tạo tác giả thất bại';
        }

        header("Location: index.php?controller=author");
    }

    public function edit() {
        $id = $_GET['id'];
        $author = $this->author->getById($id);
        require __DIR__ . '/../Views/authors/edit.php';
    }

    public function update() {
        $id = $_GET['id'];
        $data = [
            'name' => $_POST['name'],
            'biography' => isset($_POST['biography']) ? $_POST['biography'] : null,
            'nationality' => isset($_POST['nationality']) ? $_POST['nationality'] : null,
            'birth_year' => isset($_POST['birth_year']) ? $_POST['birth_year'] : null,
            'death_year' => isset($_POST['death_year']) ? $_POST['death_year'] : null
        ];

        if ($this->author->update($id, $data)) {
            $_SESSION['success'] = 'Cập nhật tác giả thành công';
        } else {
            $_SESSION['error'] = 'Cập nhật tác giả thất bại';
        }

        header("Location: index.php?controller=author");
    }

    public function delete() {
        $id = $_GET['id'];

        if ($this->author->delete($id)) {
            $_SESSION['success'] = 'Xóa tác giả thành công';
        } else {
            $_SESSION['error'] = 'Xóa tác giả thất bại';
        }

        header("Location: index.php?controller=author");
    }
}