<?php
require_once __DIR__ . '/../Models/Category.php';

class CategoryController {
    private $category;

    public function __construct() {
        $this->checkAuth();
        $this->category = new Category();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
        // Only admin and librarian can manage categories
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header("Location: index.php?controller=book");
            exit;
        }
    }

    public function index() {
        $categories = $this->category->getAll();
        require __DIR__ . '/../Views/categories/index.php';
    }

    public function create() {
        require __DIR__ . '/../Views/categories/create.php';
    }

    public function store() {
        $data = [
            'name' => $_POST['name'],
            'description' => isset($_POST['description']) ? $_POST['description'] : null
        ];

        if ($this->category->create($data)) {
            $_SESSION['success'] = 'Tạo danh mục thành công';
        } else {
            $_SESSION['error'] = 'Tạo danh mục thất bại';
        }

        header("Location: index.php?controller=category");
    }

    public function edit() {
        $id = $_GET['id'];
        $category = $this->category->getById($id);
        require __DIR__ . '/../Views/categories/edit.php';
    }

    public function update() {
        $id = $_GET['id'];
        $data = [
            'name' => $_POST['name'],
            'description' => isset($_POST['description']) ? $_POST['description'] : null
        ];

        if ($this->category->update($id, $data)) {
            $_SESSION['success'] = 'Cập nhật danh mục thành công';
        } else {
            $_SESSION['error'] = 'Cập nhật danh mục thất bại';
        }

        header("Location: index.php?controller=category");
    }

    public function delete() {
        $id = $_GET['id'];

        if ($this->category->delete($id)) {
            $_SESSION['success'] = 'Xóa danh mục thành công';
        } else {
            $_SESSION['error'] = 'Xóa danh mục thất bại';
        }

        header("Location: index.php?controller=category");
    }
}