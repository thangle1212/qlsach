<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Quản lý khoản phạt</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Người dùng</th>
                                    <th>Mã phiếu mượn</th>
                                    <th>Lý do phạt</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($fines)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Không có khoản phạt nào</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($fines as $f): ?>
                                        <tr>
                                            <td><?= $f['id'] ?></td>
                                            <td><?= htmlspecialchars($f['user_name'] ?? 'N/A') ?></td>
                                            <td>#<?= $f['loan_id'] ?></td>
                                            <td>
                                                <?php
                                                    $reasons = [
                                                        'overdue' => 'Quá hạn',
                                                        'lost' => 'Sách mất',
                                                        'damaged' => 'Sách hư hỏng'
                                                    ];
                                                    echo htmlspecialchars($reasons[$f['reason']] ?? $f['reason']);
                                                ?>
                                            </td>
                                            <td class="fw-bold"><?= number_format($f['amount'], 0) ?> VNĐ</td>
                                            <td>
                                                <?php if ($f['status'] == 'paid'): ?>
                                                    <span class="badge bg-success">Đã trả</span>
                                                <?php elseif ($f['status'] == 'unpaid'): ?>
                                                    <span class="badge bg-danger">Chưa trả</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Miễn</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $f['created_at'] ?></td>
                                            <td>
                                                <?php if ($f['status'] == 'paid'): ?>
                                                    <a href="index.php?controller=admin&action=deleteFine&id=<?= $f['id'] ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa khoản phạt này không?');">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </a>
                                                <?php elseif ($f['status'] == 'unpaid'): ?>
                                                    <a href="index.php?controller=admin&action=markFineAsPaid&id=<?= $f['id'] ?>" 
                                                       class="btn btn-sm btn-success me-2"
                                                       onclick="return confirm('Bạn có chắc chắn muốn đánh dấu khoản phạt này là đã nộp không?');">
                                                        <i class="fas fa-check"></i> Đánh dấu đã nộp
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="index.php?controller=admin&action=dashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại bảng điều khiển
        </a>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>