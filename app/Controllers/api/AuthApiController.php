<?php

require_once __DIR__ . '/BaseApiController.php';
require_once __DIR__ . '/../../Models/User.php';

class AuthApiController extends BaseApiController
{
    private $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }

    public function login($params = [])
    {
        $username = trim($this->request->get('username') ?? '');
        $password = trim($this->request->get('password') ?? '');

        if (empty($username) || empty($password)) {
            return $this->error('Vui lòng cung cấp username và password', [], 422);
        }

        $user = $this->user->authenticate($username, $password);
        if (!$user) {
            return $this->error('Tên đăng nhập hoặc mật khẩu không đúng', [], 401);
        }

        if (isset($user['status']) && $user['status'] !== 'active') {
            return $this->error('Tài khoản chưa được kích hoạt', [], 403);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'] ?? null;

        unset($user['password_hash']);

        return $this->success([
            'user' => $user
        ], 'Đăng nhập thành công');
    }

    public function logout($params = [])
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        return $this->success([], 'Đăng xuất thành công');
    }
}
