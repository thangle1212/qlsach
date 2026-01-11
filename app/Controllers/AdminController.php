<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/Book.php';
require_once __DIR__ . '/../Models/Borrowing.php';

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

        // Borrowing statistics
        $totalBorrowings = count($borrowingModel->getAll());
        $activeBorrowings = $this->getActiveBorrowingsCount();
        $overdueBorrowings = count($borrowingModel->getOverdueBorrowings());

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

        if ((new User())->create($data)) {
            $_SESSION['success'] = 'Tạo người dùng thành công';
        } else {
            $_SESSION['error'] = 'Tạo người dùng thất bại';
        }

        header("Location: index.php?controller=admin&action=users");
    }

    public function editUser()
    {
        $id = $_GET['id'];
        $user = (new User())->findById($id);
        require __DIR__ . '/../Views/admin/edit_user.php';
    }

    public function updateUser()
    {
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

        // Borrowing statistics
        $totalBorrowings = count($borrowingModel->getAll());
        $activeBorrowings = $this->getActiveBorrowingsCount();
        $overdueBorrowings = count($borrowingModel->getOverdueBorrowings());

        require __DIR__ . '/../Views/admin/statistics.php';
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
        $stmt = $db->prepare("SELECT COUNT(*) FROM borrowings WHERE status = 'borrowed'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getTopBorrowedBooks()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT b.title, COUNT(br.id) as borrow_count
            FROM books b
            LEFT JOIN borrowings br ON b.id = br.book_id
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
            SELECT u.full_name, COUNT(br.id) as borrow_count
            FROM users u
            LEFT JOIN borrowings br ON u.id = br.user_id
            GROUP BY u.id, u.full_name
            ORDER BY borrow_count DESC
            LIMIT 10
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
