<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - Hệ thống thư viện</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 0 2px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
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
        .actions {
            margin-bottom: 20px;
        }
        .actions a {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
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
        
        <div class="actions">
            <a href="index.php?controller=admin&action=createUser">➕ Thêm người dùng</a>
        </div>
        
        <div class="card">
            <h2>Quản lý người dùng</h2>
            <table border="1" cellpadding="8" cellspacing="0" width="100%">
                <tr>
                    <th>ID</th>
                    <th>Tên đăng nhập</th>
                    <th>Email</th>
                    <th>Họ tên</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
                
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                    <td><?= $u['role'] ?></td>
                    <td><?= $u['status'] ?></td>
                    <td><?= $u['created_at'] ?></td>
                    <td>
                        <a href="index.php?controller=admin&action=editUser&id=<?= $u['id'] ?>" class="btn btn-warning">Sửa</a>
                        <a href="index.php?controller=admin&action=deleteUser&id=<?= $u['id'] ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>