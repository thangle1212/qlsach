<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Thông tin thành viên</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Thông tin cá nhân</h5>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>ID:</strong></div>
                        <div class="col-sm-8"><?=$user['id']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Tên đăng nhập:</strong></div>
                        <div class="col-sm-8"><?=$user['username']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Họ tên:</strong></div>
                        <div class="col-sm-8"><?=$user['full_name']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Email:</strong></div>
                        <div class="col-sm-8"><?=$user['email']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Số điện thoại:</strong></div>
                        <div class="col-sm-8"><?=$user['phone'] ?? 'Chưa cập nhật'?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Địa chỉ:</strong></div>
                        <div class="col-sm-8"><?=$user['address'] ?? 'Chưa cập nhật'?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Vai trò:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'librarian' ? 'primary' : 'secondary') ?>">
                                <?= $user['role'] === 'admin' ? 'Quản trị viên' : ($user['role'] === 'librarian' ? 'Thủ thư' : 'Thành viên') ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Trạng thái:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'inactive' ? 'secondary' : 'warning') ?>">
                                <?= $user['status'] === 'active' ? 'Hoạt động' : ($user['status'] === 'inactive' ? 'Không hoạt động' : 'Chờ kích hoạt') ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Số sách đang mượn:</strong></div>
                        <div class="col-sm-8"><?=$user['current_borrow_count']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Giới hạn mượn:</strong></div>
                        <div class="col-sm-8"><?=$user['max_borrow_limit']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Ngày tạo:</strong></div>
                        <div class="col-sm-8"><?=$user['created_at']?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Danh sách mượn sách</h5>
                    <?php if (!empty($userBorrowings)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên sách</th>
                                        <th>Ngày mượn</th>
                                        <th>Ngày trả</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userBorrowings as $borrowing): ?>
                                    <tr>
                                        <td><?=$borrowing['id']?></td>
                                        <td><?=htmlspecialchars($borrowing['title'])?></td>
                                        <td><?=$borrowing['borrow_date']?></td>
                                        <td><?=$borrowing['due_date']?></td>
                                        <td>
                                            <?php
                                            switch($borrowing['status']) {
                                                case 'borrowed':
                                                    echo '<span class="badge bg-warning">Đang mượn</span>';
                                                    break;
                                                case 'returned':
                                                    echo '<span class="badge bg-success">Đã trả</span>';
                                                    break;
                                                case 'overdue':
                                                    echo '<span class="badge bg-danger">Quá hạn</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge bg-secondary">' . $borrowing['status'] . '</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Thành viên chưa mượn sách nào.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>