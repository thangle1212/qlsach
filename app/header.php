<?php
// This is a partial view that can be included in other pages
// It provides a consistent header with navigation based on user role
// Note: Session should already be started by index.php
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống quản lý thư viện</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #343a40;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.5em;
        }
        .user-info {
            text-align: right;
        }
        .nav {
            background-color: #007bff;
            padding: 0;
            margin: 0;
            list-style: none;
            display: flex;
        }
        .nav li {
            margin: 0;
        }
        .nav a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 14px 20px;
        }
        .nav a:hover {
            background-color: #0056b3;
        }
        .container {
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hệ thống quản lý thư viện</h1>
        <div class="user-info">
            <?php if (isset($_SESSION['user_id'])): ?>
                <p>Xin chào, <?=$_SESSION['full_name']?> (<?=$_SESSION['role']?>)</p>
                <a href="index.php?controller=auth&action=logout" style="color: #ffc107;">Đăng xuất</a>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <ul class="nav">
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a href="index.php?controller=admin&action=dashboard">Bảng điều khiển</a></li>
            <li><a href="index.php?controller=admin&action=users">Quản lý người dùng</a></li>
            <li><a href="index.php?controller=settings&action=index">Cài đặt</a></li>
        <?php endif; ?>
        <li><a href="index.php?controller=book">Quản lý sách</a></li>
        <li><a href="index.php?controller=borrowing">Quản lý mượn trả</a></li>
    </ul>
    <?php endif; ?>
    
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?=$_SESSION['success']?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?=$_SESSION['error']?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>