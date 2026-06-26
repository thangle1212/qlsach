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

        // Use the main system's session structure
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        header('Location: ../../index.php?controller=book');
    }

    public function logout()
    {
        // Clear all session variables like in the main system
        $_SESSION = array();

        // Also delete the session cookie if it exists
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy the session
        session_destroy();

        header('Location: ../../index.php?controller=auth&action=showLogin');
    }
}
