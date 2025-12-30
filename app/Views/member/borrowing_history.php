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
                                    <th>ID</th>
                                    <th>Sách</th>
                                    <th>Ngày mượn</th>
                                    <th>Ngày hết hạn</th>
                                    <th>Ngày trả</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($borrowings)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Bạn chưa mượn sách nào</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($borrowings as $b): ?>
                                        <tr>
                                            <td><?= $b['id'] ?></td>
                                            <td><?= htmlspecialchars($b['title']) ?></td>
                                            <td><?= $b['borrow_date'] ?></td>
                                            <td><?= $b['due_date'] ?></td>
                                            <td><?= $b['return_date'] ?? 'Chưa trả' ?></td>
                                            <td>
                                                <?php if ($b['status'] == 'returned'): ?>
                                                    <span class="badge bg-success">Đã trả</span>
                                                <?php elseif ($b['status'] == 'borrowed'): ?>
                                                    <span class="badge bg-warning">Đang mượn</span>
                                                <?php elseif ($b['status'] == 'overdue'): ?>
                                                    <span class="badge bg-danger">Quá hạn</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= $b['status'] ?></span>
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
        <a href="index.php?controller=member&action=dashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
