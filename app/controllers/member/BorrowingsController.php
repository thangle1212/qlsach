<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../../models/member/UserModel.php';

class BorrowingsController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function current()
    {
        $user = $_SESSION['user'];
        $borrowings = $this->userModel->getCurrentBorrowings($user['id']);
        $data = ['borrowings' => $borrowings];
        extract($data);
        require_once __DIR__ . '/../../views/member/borrowings/current.php';
    }

    public function borrow()
    {
        // Placeholder for borrowing a book
        $bookId = $_GET['book_id'] ?? null;
        if ($bookId) {
            // Logic to borrow
            echo "Đã mượn sách ID: $bookId";
        } else {
            echo "Lỗi: Không có book_id";
        }
    }
}
