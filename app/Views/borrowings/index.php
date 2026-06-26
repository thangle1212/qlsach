<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Quản lý mượn – trả</h2>
                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'librarian'): ?>
                <a href="index.php?controller=borrowing&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tạo phiếu mượn
                </a>
                <?php endif; ?>
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
                                    <th>Người mượn</th>
                                    <th>Người xử lý</th>
                                    <th>Ngày mượn</th>
                                    <th>Ngày hẹn trả</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeLoans as $loan): ?>
                                <tr>
                                    <td><?= $loan['id'] ?></td>
                                    <td>
                                        <a href="index.php?controller=borrowing&action=viewMember&user_id=<?= $loan['user_id'] ?>">
                                            <?= htmlspecialchars($loan['user_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($loan['librarian_name'] ?? 'N/A') ?></td>
                                    <td><?= $loan['borrow_date'] ?></td>
                                    <td><?= $loan['due_date'] ?></td>
                                    <td>
                                        <?php
                                        switch($loan['status']) {
                                            case 'active':
                                                echo '<span class="badge bg-warning">Đang mượn</span>';
                                                break;
                                            case 'completed':
                                                echo '<span class="badge bg-success">Đã hoàn tất</span>';
                                                break;
                                            case 'overdue':
                                                echo '<span class="badge bg-danger">Quá hạn</span>';
                                                break;
                                            default:
                                                echo '<span class="badge bg-secondary">' . $loan['status'] . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="index.php?controller=borrowing&action=viewLoanDetails&id=<?= $loan['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <?php if ($loan['status'] === 'active'): ?>
                                            <a href="index.php?controller=borrowing&action=viewReturnForm&id=<?= $loan['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-undo"></i> Trả sách
                                            </a>
                                            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'librarian'): ?>
                                            <a href="index.php?controller=borrowing&action=renew&id=<?= $loan['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-sync"></i> Gia hạn
                                            </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>

                                <?php if (empty($activeLoans)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Không có dữ liệu</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Overdue Loans Section - Only for admin/librarian -->
                    <?php if (!empty($overdueLoans) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'librarian')): ?>
                    <hr>
                    <h4>Danh sách quá hạn</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Người mượn</th>
                                    <th>Người xử lý</th>
                                    <th>Ngày mượn</th>
                                    <th>Ngày hẹn trả</th>
                                    <th>Số ngày quá hạn</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overdueLoans as $loan): ?>
                                <tr>
                                    <td><?= $loan['id'] ?></td>
                                    <td>
                                        <a href="index.php?controller=borrowing&action=viewMember&user_id=<?= $loan['user_id'] ?>">
                                            <?= htmlspecialchars($loan['user_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($loan['librarian_name'] ?? 'N/A') ?></td>
                                    <td><?= $loan['borrow_date'] ?></td>
                                    <td><?= $loan['due_date'] ?></td>
                                    <td><?= $loan['overdue_days'] ?> ngày</td>
                                    <td>
                                        <span class="badge bg-danger">Quá hạn</span>
                                    </td>
                                    <td>
                                        <a href="index.php?controller=borrowing&action=viewLoanDetails&id=<?= $loan['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="index.php?controller=borrowing&action=viewReturnForm&id=<?= $loan['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-undo"></i> Trả sách
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>