<?php require_once __DIR__ . '/layout.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Sách đặt trước</h2>
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
                                    <th>Ngày đặt</th>
                                    <th>Ngày hết hạn</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reservations)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Bạn chưa đặt trước sách nào</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reservations as $r): ?>
                                        <tr>
                                            <td><?= $r['id'] ?></td>
                                            <td><?= htmlspecialchars($r['title']) ?></td>
                                            <td><?= $r['reservation_date'] ?></td>
                                            <td><?= $r['expiry_date'] ?></td>
                                            <td>
                                                <?php if ($r['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                <?php elseif ($r['status'] == 'available'): ?>
                                                    <span class="badge bg-success">Có sẵn</span>
                                                <?php elseif ($r['status'] == 'confirmed'): ?>
                                                    <span class="badge bg-info">Đã xác nhận</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Hủy</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($r['status'] === 'pending'): ?>
                                                    <a href="index.php?controller=member&action=cancelReservation&id=<?= $r['id'] ?>"
                                                       class="btn btn-sm btn-danger" onclick="return confirm('Hủy đặt trước?')">
                                                        <i class="fas fa-times"></i> Hủy
                                                    </a>
                                                <?php elseif ($r['status'] === 'available'): ?>
                                                    <span class="text-success">Sách đã sẵn sàng</span>
                                                <?php elseif ($r['status'] === 'cancelled'): ?>
                                                    <span class="text-muted">Đã hủy</span>
                                                <?php else: ?>
                                                    <span class="text-info">Đang xử lý</span>
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
