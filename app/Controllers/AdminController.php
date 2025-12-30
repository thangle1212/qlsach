<?php
require_once __DIR__ . '/../Models/User.php';

class AdminController {
    public function __construct() {
        // Check if user is authenticated and has admin role
        $this->checkAuth();
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
    }

    public function dashboard() {
        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function users() {
        $users = (new User())->getAll();
        require __DIR__ . '/../Views/admin/users.php';
    }

    public function createUser() {
        require __DIR__ . '/../Views/admin/create_user.php';
    }

    public function storeUser() {
        $data = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'full_name' => $_POST['full_name'],
            'phone' => $_POST['phone'] ?? null,
            'address' => $_POST['address'] ?? null,
            'role' => $_POST['role'],
            'status' => $_POST['status']
        ];

        if ((new User())->create($data)) {
            $_SESSION['success'] = 'Tạo người dùng thành công';
        } else {
            $_SESSION['error'] = 'Tạo người dùng thất bại';
        }
        
        header("Location: index.php?controller=admin&action=users");
    }

    public function editUser() {
        $id = $_GET['id'];
        $user = (new User())->findById($id);
        require __DIR__ . '/../Views/admin/edit_user.php';
    }

    public function updateUser() {
        $id = $_GET['id'];
        $data = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'full_name' => $_POST['full_name'],
            'phone' => $_POST['phone'] ?? null,
            'address' => $_POST['address'] ?? null,
            'role' => $_POST['role'],
            'status' => $_POST['status']
        ];

        if ((new User())->update($id, $data)) {
            $_SESSION['success'] = 'Cập nhật người dùng thành công';
        } else {
            $_SESSION['error'] = 'Cập nhật người dùng thất bại';
        }
        
        header("Location: index.php?controller=admin&action=users");
    }

    public function deleteUser() {
        $id = $_GET['id'];
        
        if ((new User())->delete($id)) {
            $_SESSION['success'] = 'Xóa người dùng thành công';
        } else {
            $_SESSION['error'] = 'Xóa người dùng thất bại';
        }
        
        header("Location: index.php?controller=admin&action=users");
    }
}