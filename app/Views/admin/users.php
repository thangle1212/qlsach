<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Quản lý người dùng</h2>
                <a href="index.php?controller=admin&action=createUser" class="btn btn-success">
                    <i class="fas fa-plus"></i> Thêm người dùng
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
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
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= $u['id'] ?></td>
                                    <td><?= htmlspecialchars($u['username']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'librarian' ? 'primary' : 'secondary') ?>">
                                            <?= $u['role'] === 'admin' ? 'Quản trị viên' : ($u['role'] === 'librarian' ? 'Thủ thư' : 'Thành viên') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $u['status'] === 'active' ? 'success' : ($u['status'] === 'inactive' ? 'secondary' : 'warning') ?>">
                                            <?= $u['status'] === 'active' ? 'Hoạt động' : ($u['status'] === 'inactive' ? 'Không hoạt động' : 'Chờ kích hoạt') ?>
                                        </span>
                                    </td>
                                    <td><?= $u['created_at'] ?></td>
                                    <td>
                                        <a href="index.php?controller=admin&action=editUser&id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <a href="index.php?controller=admin&action=deleteUser&id=<?= $u['id'] ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>