<?php
require_once 'BaseController.php';

class ProfileController extends BaseController
{
    public function index()
    {
        // Assuming user data is in session
        $user = $_SESSION['user'];
        $data = ['user' => $user];
        extract($data);
        require_once __DIR__ . '/../../views/member/profile/index.php';
    }

    public function update()
    {
        // Placeholder for updating profile
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Update logic here
            echo "Hồ sơ đã được cập nhật.";
        }
    }
}
