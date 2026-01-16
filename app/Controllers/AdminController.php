<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/Book.php';
require_once __DIR__ . '/../Models/Borrowing.php';
require_once __DIR__ . '/../Services/BorrowService.php';
require_once __DIR__ . '/../Services/FineService.php';

class AdminController
{
    public function __construct()
    {
        // Check if user is authenticated and has admin role
        $this->checkAuth();
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
    }

    public function dashboard()
    {
        $userModel = new User();
        $bookModel = new Book();
        $borrowingModel = new Borrowing();

        // User statistics
        $totalUsers = count($userModel->getAll());
        $activeUsers = $this->getActiveUsersCount();
        $inactiveUsers = $totalUsers - $activeUsers;

        // Book statistics
        $totalBooks = count($bookModel->getAll());
        $availableBooks = $this->getAvailableBooksCount();
        $borrowedBooks = $totalBooks - $availableBooks;

        // Borrowing statistics using new schema
        $totalBorrowings = $this->getTotalBorrowingsCount();
        $activeBorrowings = $this->getActiveBorrowingsCount();
        $overdueBorrowings = $this->getOverdueBorrowingsCount();

        // Statistics for charts
        $topBorrowedBooks = $this->getTopBorrowedBooks();
        $topActiveUsers = $this->getTopActiveUsers();

        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function users()
    {
        $users = (new User())->getAll();
        require __DIR__ . '/../Views/admin/users.php';
    }

    public function createUser()
    {
        require __DIR__ . '/../Views/admin/create_user.php';
    }

    public function storeUser()
    {
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

        try {
            if ((new User())->create($data)) {
                $_SESSION['success'] = 'Tạo người dùng thành công';
                header("Location: index.php?controller=admin&action=users");
            } else {
                $_SESSION['error'] = 'Tạo người dùng thất bại';
                header("Location: index.php?controller=admin&action=users");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php?controller=admin&action=createUser");
        }
    }

    public function editUser()
    {
        $id = $_GET['id'];
        $user = (new User())->getById($id); // Lấy thông tin đầy đủ bao gồm mật khẩu
        require __DIR__ . '/../Views/admin/edit_user.php';
    }

    public function updateUser()
    {
        $id = $_GET['id'];

        // Handle password change if provided
        if (!empty($_POST['password'])) {
            // Update password - lưu trực tiếp mật khẩu nguyên bản
            $userModel = new User();
            if (!$userModel->updatePassword($id, $_POST['password'])) {
                $_SESSION['error'] = 'Cập nhật mật khẩu thất bại';
                header("Location: index.php?controller=admin&action=editUser&id=$id");
                exit;
            }
        }

        // Update user info (excluding password fields)
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

    public function deleteUser()
    {
        $id = $_GET['id'];

        if ((new User())->delete($id)) {
            $_SESSION['success'] = 'Xóa người dùng thành công';
        } else {
            $_SESSION['error'] = 'Xóa người dùng thất bại';
        }

        header("Location: index.php?controller=admin&action=users");
    }

    public function statistics()
    {
        // Get statistics data
        $userModel = new User();
        $bookModel = new Book();
        $borrowingModel = new Borrowing();

        // User statistics
        $totalUsers = count($userModel->getAll());
        $activeUsers = $this->getActiveUsersCount();
        $inactiveUsers = $totalUsers - $activeUsers;

        // Book statistics
        $totalBooks = count($bookModel->getAll());
        $availableBooks = $this->getAvailableBooksCount();
        $borrowedBooks = $totalBooks - $availableBooks;

        // Borrowing statistics using new schema
        $totalBorrowings = $this->getTotalBorrowingsCount();
        $activeBorrowings = $this->getActiveBorrowingsCount();
        $overdueBorrowings = $this->getOverdueBorrowingsCount();

        require __DIR__ . '/../Views/admin/statistics.php';
    }

    public function fines()
    {
        $fineService = new \FineService();
        $fines = $fineService->getAllFines();
        require __DIR__ . '/../Views/admin/fines.php';
    }

    public function markFineAsPaid()
    {
        $fineId = $_GET['id'];
        $fineService = new \FineService();

        if ($fineService->markAsPaid($fineId)) {
            $_SESSION['success'] = 'Đánh dấu phạt đã nộp thành công';
        } else {
            $_SESSION['error'] = 'Đánh dấu phạt đã nộp thất bại';
        }

        header("Location: index.php?controller=admin&action=fines");
    }

    public function deleteFine()
    {
        $fineId = $_GET['id'];
        $fineService = new \FineService();

        if ($fineService->deleteFine($fineId)) {
            $_SESSION['success'] = 'Xóa khoản phạt thành công';
        } else {
            $_SESSION['error'] = 'Xóa khoản phạt thất bại';
        }

        header("Location: index.php?controller=admin&action=fines");
    }

    private function getActiveUsersCount()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE status = 'active'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getAvailableBooksCount()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT SUM(available_copies) FROM books");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getActiveBorrowingsCount()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM loan_slips WHERE status = 'active'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getTopBorrowedBooks()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT b.title, COUNT(li.id) as borrow_count
            FROM books b
            LEFT JOIN loan_items li ON b.id = li.book_id
            LEFT JOIN loan_slips ls ON li.loan_id = ls.id
            WHERE ls.id IS NOT NULL  -- Only count books that have been borrowed
            GROUP BY b.id, b.title
            ORDER BY borrow_count DESC
            LIMIT 10
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTopActiveUsers()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT u.full_name, COUNT(ls.id) as borrow_count
            FROM users u
            LEFT JOIN loan_slips ls ON u.id = ls.user_id
            GROUP BY u.id, u.full_name
            ORDER BY borrow_count DESC
            LIMIT 10
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTotalBorrowingsCount()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM loan_slips");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getOverdueBorrowingsCount()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM loan_slips WHERE status = 'active' AND due_date < CURDATE()");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
