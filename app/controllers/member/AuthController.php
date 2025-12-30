<?php
require_once __DIR__ . '/../../models/member/UserModel.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        require_once __DIR__ . '/../../views/member/auth/login.php';
    }

    public function handleLogin()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            die('Sai tài khoản hoặc mật khẩu');
        }

        if ($user['role'] !== 'member' || $user['status'] !== 'active') {
            die('Tài khoản chưa được kích hoạt');
        }

        $_SESSION['user'] = [
    'id' => $user['id'],
    'full_name' => $user['full_name'],
    'username' => $user['username'],
    'email' => $user['email'],
    'phone' => $user['phone'],
    'address' => $user['address'],
    'role' => $user['role'],
    'status' => $user['status'],
    'max_borrow_limit' => $user['max_borrow_limit'],
    'current_borrow_count' => $user['current_borrow_count']
];


        header('Location: ' . BASE_URL . '/member/dashboard');
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . BASE_URL . '/member/auth/login');
    }
}
