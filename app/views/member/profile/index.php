<?php require_once __DIR__ . '/../layout.php'; ?>

<?php $pageTitle = 'Hồ sơ cá nhân'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-user"></i> Hồ sơ cá nhân</h5>
            </div>
            <div class="card-body">
                <form action="/qlisach/member/profile/update" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?= htmlspecialchars($user['username']) ?>" disabled>
                            <div class="form-text">Tên đăng nhập không thể thay đổi</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        <div class="form-text">Email không thể thay đổi</div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Điện thoại</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                            value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Vai trò</label>
                        <input type="text" class="form-control" id="role" name="role"
                            value="<?php
                                    switch ($user['role']) {
                                        case 'admin':
                                            echo 'Quản trị viên';
                                            break;
                                        case 'librarian':
                                            echo 'Thủ thư';
                                            break;
                                        case 'member':
                                            echo 'Thành viên';
                                            break;
                                        default:
                                            echo $user['role'];
                                    }
                                    ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <input type="text" class="form-control" id="status" name="status"
                            value="<?php
                                    switch ($user['status']) {
                                        case 'active':
                                            echo 'Hoạt động';
                                            break;
                                        case 'inactive':
                                            echo 'Không hoạt động';
                                            break;
                                        case 'pending':
                                            echo 'Chờ kích hoạt';
                                            break;
                                        default:
                                            echo $user['status'];
                                    }
                                    ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-primary">Cập nhật hồ sơ</button>
                </form>
            </div>
        </div>
    </div>
</div>

</main>

<footer class="bg-light text-center py-3 mt-5">
    <div class="container">
        <p>&copy; 2025 Hệ thống Quản lý Thư viện. Tất cả quyền được bảo lưu.</p>
    </div>
</footer>

</body>

</html>
</div>