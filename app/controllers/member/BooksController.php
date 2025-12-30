<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../../models/Book.php';
require_once __DIR__ . '/../../models/Reservation.php';

class BooksController extends BaseController
{
    private $bookModel;
    private $reservationModel;

    public function __construct()
    {
        parent::__construct();
        $this->bookModel = new Book();
        $this->reservationModel = new Reservation();
    }

    public function index()
    {
        $books = $this->bookModel->getAll();
        require_once __DIR__ . '/../../views/member/books/index.php';
    }

    public function reserve()
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

        // Check if book exists
        $book = $this->bookModel->getById($bookId);
        if (!$book) {
            $_SESSION['error'] = 'Sách không tồn tại';
            header('Location: ../../index.php?controller=book');
            exit;
        }

        // Check if already reserved
        $exists = $this->reservationModel->checkActiveReservation($userId, $bookId);
        if ($exists) {
            $_SESSION['error'] = 'Bạn đã đặt trước sách này rồi';
            header('Location: ../../index.php?controller=book');
            exit;
        }

        // Create reservation
        $data = [
            'user_id' => $userId,
            'book_id' => $bookId,
            'reservation_date' => date('Y-m-d'),
            'expiry_date' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'pending'
        ];

        if ($this->reservationModel->create($data)) {
            $_SESSION['success'] = 'Đặt trước sách thành công';
        } else {
            $_SESSION['error'] = 'Đặt trước sách thất bại';
        }

        header('Location: ../../index.php?controller=member&action=dashboard');
        exit;
    }
}
