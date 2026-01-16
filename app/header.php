<?php
// This is a partial view that can be included in other pages
// It provides a consistent header with navigation based on user role
// Note: Session should already be started by index.php

define('BASE_URL', '/qlsach/');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống quản lý thư viện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 1.5em;
            font-weight: 600;
        }

        .user-info {
            text-align: right;
        }

        .nav {
            background-color: white;
            padding: 0;
            margin: 0;
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .nav li {
            margin: 0;
        }

        .nav a {
            display: block;
            color: #495057;
            text-decoration: none;
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav a:hover,
        .nav a.active {
            background-color: #e9ecef;
            color: #007bff;
        }

        .nav a i {
            margin-right: 8px;
        }

        .container {
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 20px;
            border: none;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px !important;
            margin: -25px -25px 20px -25px;
            border: none;
        }

        .btn {
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge {
            border-radius: 12px;
            padding: 5px 10px;
            font-size: 0.8em;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
            border: 1px solid #ced4da;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-book"></i> Hệ thống quản lý thư viện</h1>
                <div class="user-info">
                    <p class="mb-0">
                        Xin chào, <strong><?= $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Người dùng' ?></strong>
                        <span class="badge bg-light text-dark">(<?= $_SESSION['role'] ?>)</span>
                    </p>
                    <a href="index.php?controller=auth&action=logout" class="text-warning">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?controller=admin&action=dashboard">
                                    <i class="fas fa-tachometer-alt"></i> Bảng điều khiển
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?controller=admin&action=users">
                                    <i class="fas fa-users"></i> Quản lý người dùng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?controller=admin&action=statistics">
                                    <i class="fas fa-chart-bar"></i> Thống kê báo cáo
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?controller=admin&action=fines">
                                    <i class="fas fa-money-bill-wave"></i> Quản lý phạt
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?controller=settings&action=index">
                                    <i class="fas fa-cog"></i> Cài đặt
                                </a>
                            </li>
                        <?php elseif ($_SESSION['role'] === 'member'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?controller=member&action=dashboard">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?controller=member&action=profile">
                                    <i class="fas fa-user"></i> Hồ sơ
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=book">
                                <i class="fas fa-book"></i> Quản lý sách
                            </a>
                        </li>
                        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'librarian'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-cogs"></i> Quản lý hệ thống
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="index.php?controller=author">
                                            <i class="fas fa-user-pen"></i> Tác giả
                                        </a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=publisher">
                                            <i class="fas fa-building"></i> Nhà xuất bản
                                        </a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=category">
                                            <i class="fas fa-tags"></i> Danh mục
                                        </a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'librarian'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=borrowing">
                                <i class="fas fa-exchange-alt"></i> Quản lý mượn trả
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'librarian'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?controller=borrowing&action=reservations">
                                    <i class="fas fa-calendar-check"></i> Quản lý đặt trước
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($_SESSION['role'] === 'member'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?controller=member&action=borrowingHistory">
                                    <i class="fas fa-history"></i> Lịch sử mượn
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?controller=member&action=reservations">
                                    <i class="fas fa-calendar-check"></i> Đặt trước
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?controller=member&action=fines">
                                    <i class="fas fa-money-bill-wave"></i> Phạt
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <div class="container mt-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>