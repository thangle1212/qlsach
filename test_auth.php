<?php
// Simple test to check if authentication is working
session_start();

require_once 'app/Models/User.php';

$username = 'admin';
$password = 'hashed_password1'; // Use your actual password

$userModel = new User();
$user = $userModel->authenticate($username, $password);

if ($user) {
    echo "Authentication successful!<br>";
    echo "User ID: " . $user['id'] . "<br>";
    echo "Username: " . $user['username'] . "<br>";
    echo "Role: " . $user['role'] . "<br>";
    echo "Status: " . $user['status'] . "<br>";
} else {
    echo "Authentication failed!<br>";
    echo "Username tried: " . $username . "<br>";
    echo "Password tried: " . $password . "<br>";
}
?>