<?php
// Debug script to see what's happening during login
session_start();

// Check if this is a POST request (login attempt)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST request detected<br>";
    echo "Username: " . ($_POST['username'] ?? 'NOT SET') . "<br>";
    echo "Password: " . (isset($_POST['password']) ? 'SET' : 'NOT SET') . "<br>";
    
    // Check if session is already started with user info
    if (isset($_SESSION['user_id'])) {
        echo "Session already has user_id: " . $_SESSION['user_id'] . "<br>";
        echo "Role: " . $_SESSION['role'] . "<br>";
    } else {
        echo "No user in session<br>";
    }
    
    echo "Current URL: " . $_SERVER['REQUEST_URI'] . "<br>";
    echo "Referrer: " . ($_SERVER['HTTP_REFERER'] ?? 'NONE') . "<br>";
    
    // Try to process login manually
    require_once 'app/Models/User.php';
    $userModel = new User();
    $user = $userModel->authenticate($_POST['username'] ?? '', $_POST['password'] ?? '');
    
    if ($user) {
        echo "Manual authentication successful!<br>";
        echo "User role: " . $user['role'] . "<br>";
        echo "User status: " . $user['status'] . "<br>";
    } else {
        echo "Manual authentication failed!<br>";
    }
    
    exit; // Stop execution to see the debug output
}

// If not a POST request, show a simple message
echo "This is not a POST request. Current URL: " . $_SERVER['REQUEST_URI'];
?>