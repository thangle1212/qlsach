<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Quản lý đặt trước sách</h2>
                <a href="index.php?controller=borrowing" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
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
                                    <th>Người đặt</th>
                                    <th>Sách</th>
                                    <th>Ngày đặt</th>
                                    <th>Ngày hết hạn</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $r): ?>
                                <tr>
                                    <td><?= $r['id'] ?></td>
                                    <td><?= htmlspecialchars($r['full_name']) ?></td>
                                    <td><?= htmlspecialchars($r['title']) ?></td>
                                    <td><?= $r['reservation_date'] ?></td>
                                    <td><?= $r['expiry_date'] ?></td>
                                    <td>
                                        <?php
                                        switch($r['status']) {
                                            case 'pending':
                                                echo '<span class="badge bg-warning">Chờ xử lý</span>';
                                                break;
                                            case 'available':
                                                echo '<span class="badge bg-success">Có sẵn</span>';
                                                break;
                                            case 'cancelled':
                                                echo '<span class="badge bg-danger">Đã hủy</span>';
                                                break;
                                            case 'expired':
                                                echo '<span class="badge bg-secondary">Hết hạn</span>';
                                                break;
                                            default:
                                                echo '<span class="badge bg-secondary">' . $r['status'] . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($r['status'] === 'pending'): ?>
                                            <a href="index.php?controller=borrowing&action=approveReservation&id=<?= $r['id'] ?>" 
                                               class="btn btn-sm btn-success"
                                               onclick="return confirm('Bạn có chắc chắn muốn duyệt yêu cầu đặt trước này?')">
                                                <i class="fas fa-check"></i> Duyệt
                                            </a>
                                            <a href="index.php?controller=borrowing&action=rejectReservation&id=<?= $r['id'] ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Bạn có chắc chắn muốn từ chối yêu cầu đặt trước này?')">
                                                <i class="fas fa-times"></i> Từ chối
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Không thể xử lý</span>
                                        <?php endif; ?>
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