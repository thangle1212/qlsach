<?php
require_once __DIR__ . '/../Models/User.php';

class AuthController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function showLogin() {
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập tên đăng nhập và mật khẩu';
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }

        $user = $this->user->authenticate($username, $password);

        if ($user) {
            if ($user['status'] !== 'active') {
                $_SESSION['error'] = 'Tài khoản của bạn chưa được kích hoạt';
                header("Location: index.php?controller=auth&action=showLogin");
                exit;
            }

            // Store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            // Determine redirect URL based on role
            $redirectUrl = '';
            switch ($user['role']) {
                case 'admin':
                    $redirectUrl = 'index.php?controller=admin&action=dashboard';
                    break;
                case 'librarian':
                    $redirectUrl = 'index.php?controller=book';
                    break;
                case 'member':
                    $redirectUrl = 'index.php?controller=book';
                    break;
                default:
                    $redirectUrl = 'index.php?controller=book';
            }
            
            // Use JavaScript redirect as fallback
            echo "<script> window.location.href = '$redirectUrl'; </script>";
            exit;
        } else {
            $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng';
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
    }

    public function logout() {
        // Clear all session variables
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
        
        header("Location: index.php?controller=auth&action=showLogin");
        exit;
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'full_name' => $_POST['full_name'],
                'phone' => $_POST['phone'] ?? null,
                'address' => $_POST['address'] ?? null,
                'role' => 'member'
            ];

            // Validate input
            if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }

            // Check if username or email already exists
            $existingUser = (new User())->findByUsername($data['username']);
            if ($existingUser) {
                $_SESSION['error'] = 'Tên đăng nhập đã tồn tại';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }

            $existingUserByEmail = $this->checkEmailExists($data['email']);
            if ($existingUserByEmail) {
                $_SESSION['error'] = 'Email đã tồn tại';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }

            if ($this->user->create($data)) {
                $_SESSION['success'] = 'Đăng ký thành công. Vui lòng chờ quản trị viên kích hoạt tài khoản.';
                header("Location: index.php?controller=auth&action=showLogin");
            } else {
                $_SESSION['error'] = 'Đăng ký thất bại. Vui lòng thử lại.';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }
        }
    }

    private function checkEmailExists($email) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function showRegister() {
        require __DIR__ . '/../Views/auth/register.php';
    }
}