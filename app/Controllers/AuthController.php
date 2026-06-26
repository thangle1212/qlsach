<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Settings.php';

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
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $fullName = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');

            // Validate required fields
            if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }

            // Validate password length
            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }

            // Validate password confirmation
            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Mật khẩu xác nhận không khớp';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Địa chỉ email không hợp lệ';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }

            // Validate username length and format
            if (strlen($username) < 3 || strlen($username) > 50) {
                $_SESSION['error'] = 'Tên đăng nhập phải từ 3 đến 50 ký tự';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }

            if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $_SESSION['error'] = 'Tên đăng nhập chỉ chứa chữ, số và dấu gạch dưới';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }

            // Check if username already exists
            if ($this->user->findByUsername($username)) {
                $_SESSION['error'] = 'Tên đăng nhập đã tồn tại';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }

            // Check if email already exists
            if ($this->checkEmailExists($email)) {
                $_SESSION['error'] = 'Email đã được sử dụng';
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }

            // Prepare data for creation
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'full_name' => $fullName,
                'phone' => !empty($phone) ? $phone : null,
                'address' => !empty($address) ? $address : null,
                'role' => 'member'
            ];

            // Create new user
            try {
                if ($this->user->create($data)) {
                    $_SESSION['success'] = 'Đăng ký thành công! Tài khoản của bạn đang chờ kích activated từ quản trị viên.';
                    header("Location: index.php?controller=auth&action=showLogin");
                    exit;
                } else {
                    $_SESSION['error'] = 'Đăng ký thất bại. Vui lòng thử lại.';
                    header("Location: index.php?controller=auth&action=showRegister");
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header("Location: index.php?controller=auth&action=showRegister");
                exit;
            }
        } else {
            // Show registration form
            $this->showRegister();
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