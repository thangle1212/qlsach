<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Borrowing.php';
require_once __DIR__ . '/../../models/Fine.php';

class DashboardController extends BaseController
{
    public function index()
    {
        $userId = $_SESSION['user_id'];
        $userModel = new User();
        $borrowingModel = new Borrowing();
        $fineModel = new Fine();

        $user = $userModel->getById($userId);
        $borrowings = $borrowingModel->getByUserId($userId);
        $totalUnpaidFines = $fineModel->getTotalUnpaidByUser($userId);

        $data = [
            'user' => $user,
            'borrowings' => $borrowings,
            'totalUnpaidFines' => $totalUnpaidFines
        ];

        extract($data);
        require_once __DIR__ . '/../../views/member/dashboard.php';
    }
}
