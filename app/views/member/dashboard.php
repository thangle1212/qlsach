<?php require_once __DIR__ . '/layout.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Dashboard Thành viên</h2>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-2">Xin chào</h6>
                            <h4 class="card-title"><?= htmlspecialchars($user['full_name']) ?></h4>
                        </div>
                        <i class="fas fa-user fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-2">Sách đang mượn</h6>
                            <h4 class="card-title"><?= count($borrowings) ?></h4>
                        </div>
                        <i class="fas fa-book fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-2">Phạt chưa thanh toán</h6>
                            <h4 class="card-title"><?= number_format($totalUnpaidFines ?? 0) ?> VNĐ</h4>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-book-reader"></i> Sách đang mượn</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($borrowings)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
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
                                    <?php foreach ($borrowings as $b): ?>
                                        <tr>
                                            <td><?= $b['id'] ?></td>
                                            <td><?= htmlspecialchars($b['title']) ?></td>
                                            <td><?= $b['borrow_date'] ?></td>
                                            <td><?= $b['due_date'] ?></td>
                                            <td>
                                                <?php
                                                switch($b['status']) {
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
                                                        echo '<span class="badge bg-secondary">' . $b['status'] . '</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center mb-0">Bạn chưa mượn sách nào.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-bolt"></i> Hành động nhanh</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="index.php?controller=book" class="btn btn-primary w-100">
                                <i class="fas fa-book"></i><br>
                                Xem sách
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="index.php?controller=member&action=profile" class="btn btn-info w-100">
                                <i class="fas fa-user"></i><br>
                                Hồ sơ cá nhân
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="index.php?controller=member&action=borrowingHistory" class="btn btn-success w-100">
                                <i class="fas fa-history"></i><br>
                                Lịch sử mượn
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="index.php?controller=member&action=reservations" class="btn btn-warning w-100">
                                <i class="fas fa-calendar-check"></i><br>
                                Đặt trước
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="index.php?controller=member&action=fines" class="btn btn-danger w-100">
                                <i class="fas fa-money-bill-wave"></i><br>
                                Phạt
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>