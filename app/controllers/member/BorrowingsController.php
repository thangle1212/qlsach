<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../../models/Borrowing.php';
require_once __DIR__ . '/../../models/Book.php';
require_once __DIR__ . '/../../models/User.php';

class BorrowingsController extends BaseController
{
    private $borrowingModel;
    private $bookModel;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->borrowingModel = new Borrowing();
        $this->bookModel = new Book();
        $this->userModel = new User();
    }

    public function current()
    {
        $userId = $_SESSION['user_id'];
        $borrowings = $this->borrowingModel->getByUserId($userId);
        // Filter to only show current (active) borrowings
        $currentBorrowings = array_filter($borrowings, function($borrowing) {
            return $borrowing['status'] === 'borrowed';
        });

        $data = ['borrowings' => $currentBorrowings];
        extract($data);
        require_once __DIR__ . '/../../views/member/borrowings/current.php';
    }

    public function history()
    {
        $userId = $_SESSION['user_id'];
        $borrowings = $this->borrowingModel->getByUserId($userId);

        $data = ['borrowings' => $borrowings];
        extract($data);
        require_once __DIR__ . '/../../views/member/borrowings/history.php';
    }

    public function borrow()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../../index.php?controller=book');
            exit;
        }

        $bookId = $_POST['book_id'] ?? null;
        if (!$bookId) {
            $_SESSION['error'] = 'ID sách không hợp lệ';
            header('Location: ../../index.php?controller=book');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Check if user already borrowed this book (not returned yet)
        $exists = $this->borrowingModel->checkActiveBorrow($userId, $bookId);
        if ($exists) {
            $_SESSION['error'] = 'Bạn đã mượn sách này rồi';
            header('Location: ../../index.php?controller=book');
            exit;
        }

        // Check if book is available
        $book = $this->bookModel->getById($bookId);
        if (!$book || $book['available_copies'] <= 0) {
            $_SESSION['error'] = 'Sách này không còn sẵn có';
            header('Location: ../../index.php?controller=book');
            exit;
        }

        // Get max borrow days from settings
        $max_days = 14; // Default
        $due_date = date('Y-m-d', strtotime("+$max_days days"));

        // Create borrowing
        $data = [
            'user_id' => $userId,
            'book_id' => $bookId,
            'borrow_date' => date('Y-m-d'),
            'due_date' => $due_date,
            'librarian_id' => null,
            'status' => 'borrowed'
        ];

        if ($this->borrowingModel->create($data)) {
            // Update available copies
            $this->bookModel->updateAvailableCopies($bookId, $book['available_copies'] - 1);
            $_SESSION['success'] = 'Mượn sách thành công';
        } else {
            $_SESSION['error'] = 'Mượn sách thất bại';
        }

        header('Location: ../../index.php?controller=member&action=dashboard');
        exit;
    }
}
