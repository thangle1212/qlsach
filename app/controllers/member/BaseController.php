<?php
class BaseController
{
    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/member/auth/login');
            exit;
        }
    }
}
