<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu - Thư Viện</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2 class="mb-4">Đổi mật khẩu</h2>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <p class="mb-0">- <?= htmlspecialchars($error) ?></p>
                        <?php endforeach; unset($_SESSION['errors']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?controller=member&action=updatePassword">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu cũ *</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới *</label>
                        <input type="password" class="form-control" name="new_password" required minlength="6">
                        <small class="form-text text-muted">Tối thiểu 6 ký tự</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu mới *</label>
                        <input type="password" class="form-control" name="confirm_password" required minlength="6">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                        <a href="index.php?controller=member&action=profile" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
