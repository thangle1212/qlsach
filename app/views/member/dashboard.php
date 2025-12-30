<?php require_once __DIR__ . '/layout.php'; ?>

<?php $pageTitle = 'Bảng điều khiển'; ?>

<div class="hero bg-primary text-white py-5 mb-4">
    <div class="container">
        <h1 class="display-4">Chào mừng, <?= htmlspecialchars($user['full_name']) ?>!</h1>
        <p class="lead">Chúc bạn có trải nghiệm tốt với hệ thống quản lý thư viện của chúng tôi.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-primary shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6>Hồ sơ cá nhân</h6>
                    <h5><?= htmlspecialchars($user['full_name']) ?></h5>
                    <p><?= htmlspecialchars($user['username'] ?? 'N/A') ?></p>
                </div>
                <i class="fas fa-user fa-3x"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-success shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6>Sách đang mượn</h6>
                    <h5><?= count($borrowings) ?></h5>
                    <p>Hiện đang mượn</p>
                </div>
                <i class="fas fa-book-reader fa-3x"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-danger shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6>Phạt chưa thanh toán</h6>
                    <h5><?= $totalUnpaidFines ?></h5>
                    <p>Cần xử lý</p>
                </div>
                <i class="fas fa-exclamation-triangle fa-3x"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h6><i class="fas fa-book-reader"></i> Sách đang mượn</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($borrowings)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tiêu đề</th>
                                    <th>Ngày mượn</th>
                                    <th>Hạn trả</th>
                                    <th>Trạng thái</th>
                                    <th>Phạt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($borrowings as $b): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($b['book_title']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($b['borrow_date'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($b['due_date'])) ?></td>
                                        <td>
                                            <span class="status-<?= $b['status'] ?>">
                                                <?php
                                                switch ($b['status']) {
                                                    case 'borrowed':
                                                        echo 'Đang mượn';
                                                        break;
                                                    case 'returned':
                                                        echo 'Đã trả';
                                                        break;
                                                    case 'overdue':
                                                        echo 'Quá hạn';
                                                        break;
                                                    case 'lost':
                                                        echo 'Mất sách';
                                                        break;
                                                    default:
                                                        echo $b['status'];
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td><?= $b['fine_amount'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Bạn chưa mượn sách nào</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</main>

<footer class="bg-light text-center py-3 mt-5">
    <div class="container">
        <p>&copy; 2025 Hệ thống Quản lý Thư viện. Tất cả quyền được bảo lưu.</p>
    </div>
</footer>

</body>

</html>