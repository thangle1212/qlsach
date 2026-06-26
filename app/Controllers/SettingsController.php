<?php
require_once __DIR__ . '/../Models/Settings.php';

class SettingsController {
    private $settings;

    public function __construct() {
        $this->checkAuth();
        $this->settings = new Settings();
        $this->settings->initializeDefaults();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header("Location: index.php?controller=auth&action=showLogin");
            exit;
        }
    }

    public function index() {
        $settings = [
            'max_borrow_days' => $this->settings->get('max_borrow_days'),
            'fine_per_day' => $this->settings->get('fine_per_day'),
            'max_books_per_user' => $this->settings->get('max_books_per_user')
        ];
        
        require __DIR__ . '/../Views/settings/index.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $max_borrow_days = (int)$_POST['max_borrow_days'];
            $fine_per_day = (int)$_POST['fine_per_day'];
            $max_books_per_user = (int)$_POST['max_books_per_user'];
            
            // Validate inputs
            if ($max_borrow_days <= 0 || $fine_per_day < 0 || $max_books_per_user <= 0) {
                $_SESSION['error'] = 'Giá trị không hợp lệ';
                header("Location: index.php?controller=settings&action=index");
                exit;
            }
            
            // Update settings
            $this->settings->set('max_borrow_days', $max_borrow_days);
            $this->settings->set('fine_per_day', $fine_per_day);
            $this->settings->set('max_books_per_user', $max_books_per_user);
            
            $_SESSION['success'] = 'Cập nhật cài đặt thành công';
        }
        
        header("Location: index.php?controller=settings&action=index");
    }
}