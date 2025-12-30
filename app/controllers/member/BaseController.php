<?php
class BaseController
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../../index.php?controller=auth&action=showLogin');
            exit;
        }

        // Ensure the user is a member
        if ($_SESSION['role'] !== 'member') {
            header('Location: ../../index.php?controller=book');
            exit;
        }
    }
}
