<?php require_once __DIR__ . '/layout.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Lịch sử mượn sách</h2>
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
                                    <th>Mã phiếu</th>
                                    <th>Sách</th>
                                    <th>Ngày mượn</th>
                                    <th>Ngày hết hạn</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($allLoans)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Bạn chưa mượn sách nào</td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $borrowService = new \BorrowService();
                                    foreach ($allLoans as $loan):
                                        $loanDetails = $borrowService->getLoanDetails($loan['id']);
                                    ?>
                                        <?php if (!empty($loanDetails)): ?>
                                            <?php foreach ($loanDetails as $detail): ?>
                                            <tr>
                                                <td>#<?= $loan['id'] ?></td>
                                                <td><?= htmlspecialchars($detail['title']) ?></td>
                                                <td><?= $loan['borrow_date'] ?></td>
                                                <td><?= $loan['due_date'] ?></td>
                                                <td>
                                                    <?php if ($loan['status'] == 'completed'): ?>
                                                        <span class="badge bg-success">Đã trả</span>
                                                    <?php elseif ($loan['status'] == 'active'): ?>
                                                        <span class="badge bg-warning">Đang mượn</span>
                                                    <?php elseif ($loan['status'] == 'overdue'): ?>
                                                        <span class="badge bg-danger">Quá hạn</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><?= $loan['status'] ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="index.php?controller=borrowing&action=viewLoanDetails&id=<?= $loan['id'] ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Xem
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td>#<?= $loan['id'] ?></td>
                                                <td colspan="5" class="text-center">Không có sách nào trong phiếu mượn này</td>
                                            </tr>
                                        <?php endif; ?>
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
