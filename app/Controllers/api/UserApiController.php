<?php

require_once __DIR__ . '/BaseApiController.php';
require_once __DIR__ . '/../../Models/User.php';

class UserApiController extends BaseApiController
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Lấy danh sách người dùng (Chỉ Admin mới có quyền truy cập)
     * GET /api/users
     */
    public function index()
    {
        try {
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin') {
                return $this->error('Bạn không có quyền xem danh sách người dùng', [], 403);
            }

            $users = $this->userModel->getAll();
            
            // Loại bỏ các thông tin nhạy cảm
            foreach ($users as &$user) {
                unset($user['password_hash']);
            }

            return $this->success(['users' => $users], 'Danh sách người dùng');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * Lấy chi tiết thông tin người dùng (Admin hoặc chính người dùng tự xem)
     * GET /api/users/{id}
     */
    public function show($params = [])
    {
        try {
            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID người dùng', [], 422);
            }

            $role = $_SESSION['role'] ?? null;
            $currentUserId = $_SESSION['user_id'] ?? null;

            if ($role !== 'admin' && $currentUserId != $id) {
                return $this->error('Bạn không có quyền xem thông tin người dùng này', [], 403);
            }

            $user = $this->userModel->getById($id);
            if (!$user) {
                return $this->error('Người dùng không tồn tại', [], 404);
            }

            unset($user['password_hash']);

            return $this->success(['user' => $user], 'Chi tiết người dùng');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * Tạo người dùng mới (Chỉ Admin mới có quyền)
     * POST /api/users
     */
    public function store($params = [])
    {
        try {
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin') {
                return $this->error('Bạn không có quyền tạo người dùng mới', [], 403);
            }

            $username = trim($this->request->get('username') ?? '');
            $email = trim($this->request->get('email') ?? '');
            $password = $this->request->get('password');
            $fullName = trim($this->request->get('full_name') ?? '');
            $phone = trim($this->request->get('phone') ?? '');
            $address = trim($this->request->get('address') ?? '');
            $userRole = trim($this->request->get('role') ?? 'member');
            $maxBorrowLimit = $this->request->get('max_borrow_limit') !== null ? (int)$this->request->get('max_borrow_limit') : 5;

            // Kiểm tra các trường bắt buộc
            if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
                return $this->error('Vui lòng điền đầy đủ các thông tin bắt buộc (username, email, password, full_name)', [], 422);
            }

            // Kiểm tra định dạng email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->error('Địa chỉ email không hợp lệ', [], 422);
            }

            // Kiểm tra độ dài và định dạng tên đăng nhập
            if (strlen($username) < 3 || strlen($username) > 50) {
                return $this->error('Tên đăng nhập phải từ 3 đến 50 ký tự', [], 422);
            }

            if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                return $this->error('Tên đăng nhập chỉ được chứa chữ, số và dấu gạch dưới', [], 422);
            }

            if (strlen($password) < 6) {
                return $this->error('Mật khẩu phải chứa ít nhất 6 ký tự', [], 422);
            }

            // Kiểm tra trùng username hoặc email
            if ($this->userModel->findByUsername($username)) {
                return $this->error('Tên đăng nhập đã tồn tại trong hệ thống', [], 422);
            }

            // Kiểm tra trùng email trực tiếp qua Database
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return $this->error('Email đã tồn tại trong hệ thống', [], 422);
            }

            // Mã hóa mật khẩu trước khi lưu trữ
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $data = [
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword, // Lưu pass đã bcrypt
                'full_name' => $fullName,
                'phone' => !empty($phone) ? $phone : null,
                'address' => !empty($address) ? $address : null,
                'role' => $userRole,
                'max_borrow_limit' => $maxBorrowLimit
            ];

            if ($this->userModel->create($data)) {
                $newUserId = $db->lastInsertId();
                return $this->success(['user_id' => $newUserId], 'Tạo người dùng thành công', 201);
            } else {
                return $this->error('Tạo người dùng thất bại', [], 400);
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * Cập nhật thông tin người dùng (Admin cập nhật bất kỳ, Member cập nhật chính mình)
     * PUT /api/users/{id}
     */
    public function update($params = [])
    {
        try {
            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID người dùng', [], 422);
            }

            $role = $_SESSION['role'] ?? null;
            $currentUserId = $_SESSION['user_id'] ?? null;

            if ($role !== 'admin' && $currentUserId != $id) {
                return $this->error('Bạn không có quyền cập nhật thông tin người dùng này', [], 403);
            }

            $user = $this->userModel->getById($id);
            if (!$user) {
                return $this->error('Người dùng không tồn tại', [], 404);
            }

            // Xử lý đổi mật khẩu nếu trường password được gửi lên
            $password = $this->request->get('password');
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    return $this->error('Mật khẩu mới phải chứa ít nhất 6 ký tự', [], 422);
                }
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                if (!$this->userModel->updatePassword($id, $hashedPassword)) {
                    return $this->error('Cập nhật mật khẩu thất bại', [], 400);
                }
            }

            // Xử lý cập nhật các thông tin khác
            if ($role === 'admin') {
                $username = trim($this->request->get('username') ?? $user['username']);
                $email = trim($this->request->get('email') ?? $user['email']);
                $fullName = trim($this->request->get('full_name') ?? $user['full_name']);
                $phone = $this->request->get('phone') !== null ? trim($this->request->get('phone')) : $user['phone'];
                $address = $this->request->get('address') !== null ? trim($this->request->get('address')) : $user['address'];
                $userRole = trim($this->request->get('role') ?? $user['role']);
                $status = trim($this->request->get('status') ?? $user['status']);

                if (empty($username) || empty($email) || empty($fullName)) {
                    return $this->error('Thông tin username, email, full_name không được để trống', [], 422);
                }

                $db = Database::getInstance();
                $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                $stmt->execute([$username, $id]);
                if ($stmt->fetch()) {
                    return $this->error('Tên đăng nhập đã được sử dụng bởi người dùng khác', [], 422);
                }

                $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $id]);
                if ($stmt->fetch()) {
                    return $this->error('Email đã được sử dụng bởi người dùng khác', [], 422);
                }

                $data = [
                    'username' => $username,
                    'email' => $email,
                    'full_name' => $fullName,
                    'phone' => $phone,
                    'address' => $address,
                    'role' => $userRole,
                    'status' => $status
                ];
            } else {
                // Member cập nhật thông tin cá nhân
                $email = trim($this->request->get('email') ?? $user['email']);
                $fullName = trim($this->request->get('full_name') ?? $user['full_name']);
                $phone = $this->request->get('phone') !== null ? trim($this->request->get('phone')) : $user['phone'];
                $address = $this->request->get('address') !== null ? trim($this->request->get('address')) : $user['address'];

                if (empty($email) || empty($fullName)) {
                    return $this->error('Thông tin email và full_name không được để trống', [], 422);
                }

                $db = Database::getInstance();
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $id]);
                if ($stmt->fetch()) {
                    return $this->error('Email đã được sử dụng bởi người dùng khác', [], 422);
                }

                $data = [
                    'full_name' => $fullName,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address
                ];
            }

            if ($this->userModel->update($id, $data)) {
                return $this->success(['user_id' => $id], 'Cập nhật thông tin thành công');
            } else {
                return $this->error('Cập nhật thông tin thất bại', [], 400);
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * Xóa người dùng (Chỉ Admin mới có quyền)
     * DELETE /api/users/{id}
     */
    public function destroy($params = [])
    {
        try {
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin') {
                return $this->error('Bạn không có quyền xóa người dùng', [], 403);
            }

            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID người dùng', [], 422);
            }

            $currentUserId = $_SESSION['user_id'] ?? null;
            if ($currentUserId == $id) {
                return $this->error('Bạn không thể tự xóa tài khoản của chính mình', [], 400);
            }

            $user = $this->userModel->getById($id);
            if (!$user) {
                return $this->error('Người dùng không tồn tại', [], 404);
            }

            // Kiểm tra xem người dùng có đang mượn sách chưa trả không
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT COUNT(*) FROM loan_slips WHERE user_id = ? AND status = 'active'");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                return $this->error('Không thể xóa người dùng đang có phiếu mượn chưa trả', [], 400);
            }

            // Sử dụng Transaction để xóa an toàn tất cả các ràng buộc liên quan
            $db->beginTransaction();
            try {
                // Xóa đặt trước sách
                $db->prepare("DELETE FROM reservations WHERE user_id = ?")->execute([$id]);
                // Xóa phiếu phạt
                $db->prepare("DELETE FROM fines WHERE user_id = ?")->execute([$id]);
                
                // Lấy tất cả các ID phiếu mượn của người dùng này
                $stmtSlips = $db->prepare("SELECT id FROM loan_slips WHERE user_id = ?");
                $stmtSlips->execute([$id]);
                $slipIds = $stmtSlips->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($slipIds)) {
                    $placeholders = implode(',', array_fill(0, count($slipIds), '?'));
                    // Xóa chi tiết trả sách liên quan
                    $db->prepare("DELETE FROM return_items WHERE loan_item_id IN (SELECT id FROM loan_items WHERE loan_id IN ($placeholders))")->execute($slipIds);
                    // Xóa phiếu trả sách
                    $db->prepare("DELETE FROM return_slips WHERE loan_id IN ($placeholders)")->execute($slipIds);
                    // Xóa chi tiết mượn sách
                    $db->prepare("DELETE FROM loan_items WHERE loan_id IN ($placeholders)")->execute($slipIds);
                    // Xóa phiếu mượn sách
                    $db->prepare("DELETE FROM loan_slips WHERE user_id = ?")->execute([$id]);
                }
                
                if ($this->userModel->delete($id)) {
                    $db->commit();
                    return $this->success(['user_id' => $id], 'Xóa người dùng thành công');
                } else {
                    $db->rollBack();
                    return $this->error('Xóa người dùng thất bại', [], 400);
                }
            } catch (Exception $ex) {
                $db->rollBack();
                return $this->error('Không thể xóa người dùng do lỗi ràng buộc dữ liệu: ' . $ex->getMessage(), [], 400);
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }
}
