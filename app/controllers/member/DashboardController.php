<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../../models/member/UserModel.php';

class DashboardController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $user = $_SESSION['user'];
        $borrowings = $userModel->getCurrentBorrowings($user['id']);
        $totalUnpaidFines = $userModel->getTotalUnpaidFines($user['id']);

        $data = [
            'user' => $user,
            'borrowings' => $borrowings,
            'totalUnpaidFines' => $totalUnpaidFines
        ];

        extract($data);
        require_once __DIR__ . '/../../views/member/dashboard.php';
    }
}
