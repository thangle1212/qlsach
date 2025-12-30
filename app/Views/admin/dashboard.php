<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển Admin - Hệ thống thư viện</title>
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
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            margin-top: 10px;
            color: #666;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-success {
            background-color: #28a745;
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
        <h1>Hệ thống quản lý thư viện - Admin</h1>
        <div class="user-info">
            <p>Xin chào, <?=$_SESSION['full_name']?> (<?=$_SESSION['role']?>)</p>
            <a href="index.php?controller=auth&action=logout" style="color: #ffc107;">Đăng xuất</a>
        </div>
    </div>
    
    <ul class="nav">
        <li><a href="index.php?controller=admin&action=dashboard">Bảng điều khiển</a></li>
        <li><a href="index.php?controller=admin&action=users">Quản lý người dùng</a></li>
        <li><a href="index.php?controller=book">Quản lý sách</a></li>
        <li><a href="index.php?controller=borrowing">Quản lý mượn trả</a></li>
    </ul>
    
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?=$_SESSION['success']?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?=$_SESSION['error']?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="card">
            <h2>Bảng điều khiển Admin</h2>
            <p>Chào mừng bạn đến với bảng điều khiển quản trị hệ thống thư viện.</p>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="stat-label">Tổng số sách</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="stat-label">Tổng số người dùng</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="stat-label">Sách đang mượn</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="stat-label">Sách quá hạn</div>
            </div>
        </div>
        
        <div class="card">
            <h3>Hoạt động gần đây</h3>
            <p>Chức năng này sẽ được cập nhật sau...</p>
        </div>
    </div>
</body>
</html>