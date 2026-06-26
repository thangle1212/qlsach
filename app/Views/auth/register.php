<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Hệ thống thư viện</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 400px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        small {
            font-size: 12px;
        }
    </style>
    <script>
        // Validate password match on form submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');
            
            form.addEventListener('submit', function(e) {
                if (passwordField.value !== confirmPasswordField.value) {
                    e.preventDefault();
                    alert('Mật khẩu xác nhận không khớp!');
                    confirmPasswordField.focus();
                    return false;
                }
                if (passwordField.value.length < 6) {
                    e.preventDefault();
                    alert('Mật khẩu phải có ít nhất 6 ký tự!');
                    return false;
                }
            });
        });
    </script>
</head>
<body>
    <div class="register-container">
        <h2 style="text-align: center;">Đăng ký tài khoản</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?=$_SESSION['error']?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?=$_SESSION['success']?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <form method="post" action="index.php?controller=auth&action=register">
            <div class="form-group">
                <label for="full_name">Họ và tên:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo isset($registerFormData['full_name']) ? htmlspecialchars($registerFormData['full_name']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" value="<?php echo isset($registerFormData['username']) ? htmlspecialchars($registerFormData['username']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($registerFormData['email']) ? htmlspecialchars($registerFormData['email']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required minlength="6">
                <small style="color: #666; display: block; margin-top: 3px;">Tối thiểu 6 ký tự</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu:</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại (tùy chọn):</label>
                <input type="text" id="phone" name="phone" value="<?php echo isset($registerFormData['phone']) ? htmlspecialchars($registerFormData['phone']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ (tùy chọn):</label>
                <input type="text" id="address" name="address" value="<?php echo isset($registerFormData['address']) ? htmlspecialchars($registerFormData['address']) : ''; ?>">
            </div>

            <button type="submit">Đăng ký</button>
        </form>
        
        <div class="login-link">
            <a href="index.php?controller=auth&action=showLogin">Đã có tài khoản? Đăng nhập ngay</a>
        </div>
    </div>
</body>
</html>