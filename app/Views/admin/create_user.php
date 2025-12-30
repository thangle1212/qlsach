<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo người dùng mới - Hệ thống thư viện</title>
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
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
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
            <h2>Tạo người dùng mới</h2>
            <form method="post" action="index.php?controller=admin&action=storeUser">
                <div class="form-group">
                    <label for="username">Tên đăng nhập:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Họ và tên:</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="text" id="phone" name="phone">
                </div>
                
                <div class="form-group">
                    <label for="address">Địa chỉ:</label>
                    <input type="text" id="address" name="address">
                </div>
                
                <div class="form-group">
                    <label for="role">Vai trò:</label>
                    <select id="role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="librarian">Thủ thư</option>
                        <option value="member">Thành viên</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Trạng thái:</label>
                    <select id="status" name="status" required>
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Không hoạt động</option>
                        <option value="pending">Chờ kích hoạt</option>
                    </select>
                </div>
                
                <button type="submit">Tạo người dùng</button>
                <a href="index.php?controller=admin&action=users" class="btn">Hủy</a>
            </form>
        </div>
    </div>
</body>
</html>