<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../../models/User.php';

class ProfileController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index()
    {
        // Get user data from main system
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        $data = ['user' => $user];
        extract($data);
        require_once __DIR__ . '/../../views/member/profile/index.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../../index.php?controller=member&action=profile');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Validate input
        $errors = [];
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($full_name)) {
            $errors[] = 'Họ tên không được để trống';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        if (!empty($phone) && !preg_match('/^[0-9\-\+\s\(\)]{7,20}$/', $phone)) {
            $errors[] = 'Số điện thoại không hợp lệ';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ../../index.php?controller=member&action=profile');
            exit;
        }

        $data = [
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address
        ];

        if ($this->userModel->update($userId, $data)) {
            // Update session data
            $_SESSION['full_name'] = $full_name;
            $_SESSION['username'] = $email; // This might be the same as email or username

            $_SESSION['success'] = 'Cập nhật hồ sơ thành công';
        } else {
            $_SESSION['error'] = 'Cập nhật hồ sơ thất bại';
        }

        header('Location: ../../index.php?controller=member&action=profile');
        exit;
    }

    public function changePassword()
    {
        require_once __DIR__ . '/../../views/member/profile/change_password.php';
    }

    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../../index.php?controller=member&action=profile');
            exit;
        }

        $userId = $_SESSION['user_id'];

        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate input
        $errors = [];
        if (empty($old_password)) {
            $errors[] = 'Mật khẩu cũ không được để trống';
        }

        if (empty($new_password)) {
            $errors[] = 'Mật khẩu mới không được để trống';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        }

        if (empty($confirm_password)) {
            $errors[] = 'Xác nhận mật khẩu không được để trống';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'Xác nhận mật khẩu không khớp';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ../../index.php?controller=member&action=changePassword');
            exit;
        }

        // Get user to verify old password
        $user = $this->userModel->getById($userId);

        // Verify old password
        if (!password_verify($old_password, $user['password_hash'])) {
            $_SESSION['error'] = 'Mật khẩu cũ không chính xác';
            header('Location: ../../index.php?controller=member&action=changePassword');
            exit;
        }

        // Update password
        if ($this->userModel->updatePassword($userId, password_hash($new_password, PASSWORD_DEFAULT))) {
            $_SESSION['success'] = 'Đổi mật khẩu thành công';
        } else {
            $_SESSION['error'] = 'Đổi mật khẩu thất bại';
        }

        header('Location: ../../index.php?controller=member&action=profile');
        exit;
    }
}
