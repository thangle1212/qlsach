<?php require_once __DIR__ . '/layout.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Khoản phạt</h2>
        </div>
    </div>

    <?php if ($totalUnpaid > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Bạn có <?= number_format($totalUnpaid) ?> VNĐ phạt chưa trả</strong>
            <br>Vui lòng liên hệ thư viện để thanh toán.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Mã phiếu mượn</th>
                                    <th>Lý do phạt</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($fines)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Bạn không có khoản phạt nào</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($fines as $f): ?>
                                        <tr>
                                            <td><?= $f['id'] ?></td>
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
        <a href="index.php?controller=member&action=dashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
