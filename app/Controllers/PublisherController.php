<?php
require_once __DIR__ . '/../Models/Publisher.php';

class PublisherController {
    private $publisher;

    public function __construct() {
        $this->checkAuth();
        $this->publisher = new Publisher();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
        // Only admin and librarian can manage publishers
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'librarian') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header("Location: index.php?controller=book");
            exit;
        }
    }

    public function index() {
        $publishers = $this->publisher->getAll();
        require __DIR__ . '/../Views/publishers/index.php';
    }

    public function create() {
        require __DIR__ . '/../Views/publishers/create.php';
    }

    public function store() {
        $data = [
            'name' => $_POST['name'],
            'address' => isset($_POST['address']) ? $_POST['address'] : null,
            'phone' => isset($_POST['phone']) ? $_POST['phone'] : null,
            'email' => isset($_POST['email']) ? $_POST['email'] : null,
            'website' => isset($_POST['website']) ? $_POST['website'] : null
        ];

        if ($this->publisher->create($data)) {
            $_SESSION['success'] = 'Tạo nhà xuất bản thành công';
        } else {
            $_SESSION['error'] = 'Tạo nhà xuất bản thất bại';
        }

        header("Location: index.php?controller=publisher");
    }

    public function edit() {
        $id = $_GET['id'];
        $publisher = $this->publisher->getById($id);
        require __DIR__ . '/../Views/publishers/edit.php';
    }

    public function update() {
        $id = $_GET['id'];
        $data = [
            'name' => $_POST['name'],
            'address' => isset($_POST['address']) ? $_POST['address'] : null,
            'phone' => isset($_POST['phone']) ? $_POST['phone'] : null,
            'email' => isset($_POST['email']) ? $_POST['email'] : null,
            'website' => isset($_POST['website']) ? $_POST['website'] : null
        ];

        if ($this->publisher->update($id, $data)) {
            $_SESSION['success'] = 'Cập nhật nhà xuất bản thành công';
        } else {
            $_SESSION['error'] = 'Cập nhật nhà xuất bản thất bại';
        }

        header("Location: index.php?controller=publisher");
    }

    public function delete() {
        $id = $_GET['id'];

        if ($this->publisher->delete($id)) {
            $_SESSION['success'] = 'Xóa nhà xuất bản thành công';
        } else {
            $_SESSION['error'] = 'Xóa nhà xuất bản thất bại';
        }

        header("Location: index.php?controller=publisher");
    }
}