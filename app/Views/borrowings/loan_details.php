<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Chi tiết phiếu mượn #<?= $loanSlip['id'] ?></h2>
                <a href="index.php?controller=borrowing" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Thông tin phiếu mượn</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>ID:</th>
                            <td>#<?= $loanSlip['id'] ?></td>
                        </tr>
                        <tr>
                            <th>Người mượn:</th>
                            <td>
                                <a href="index.php?controller=borrowing&action=viewMember&user_id=<?= $loanSlip['user_id'] ?>">
                                    <?= htmlspecialchars($loanSlip['user_name']) ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Người xử lý:</th>
                            <td><?= htmlspecialchars($loanSlip['librarian_name'] ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>Ngày mượn:</th>
                            <td><?= $loanSlip['borrow_date'] ?></td>
                        </tr>
                        <tr>
                            <th>Ngày hẹn trả:</th>
                            <td><?= $loanSlip['due_date'] ?></td>
                        </tr>
                        <tr>
                            <th>Trạng thái:</th>
                            <td>
                                <?php
                                switch($loanSlip['status']) {
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
                                        echo '<span class="badge bg-secondary">' . $loanSlip['status'] . '</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Hành động</h5>
                </div>
                <div class="card-body">
                    <?php if ($loanSlip['status'] === 'active' && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'librarian')): ?>
                        <a href="index.php?controller=borrowing&action=viewReturnForm&id=<?= $loanSlip['id'] ?>" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-undo"></i> Trả sách
                        </a>
                        <a href="index.php?controller=borrowing&action=renew&id=<?= $loanSlip['id'] ?>" class="btn btn-primary w-100">
                            <i class="fas fa-sync"></i> Gia hạn
                        </a>
                    <?php elseif ($loanSlip['status'] === 'active' && $_SESSION['role'] === 'member'): ?>
                        <p class="text-info">Vui lòng liên hệ thủ thư để trả sách hoặc gia hạn</p>
                    <?php else: ?>
                        <p class="text-success">Phiếu mượn đã hoàn tất</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Danh sách sách mượn</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tiêu đề</th>
                                    <th>ISBN</th>
                                    <th>Số lượng</th>
                                    <th>Đã trả</th>
                                    <th>Còn lại</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($loanDetails as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['title']) ?></td>
                                    <td><?= htmlspecialchars($item['isbn']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= $item['returned_quantity'] ?></td>
                                    <td><?= $item['quantity'] - $item['returned_quantity'] ?></td>
                                    <td>
                                        <?php
                                        switch($item['status']) {
                                            case 'borrowed':
                                                echo '<span class="badge bg-warning">Đang mượn</span>';
                                                break;
                                            case 'returned':
                                                echo '<span class="badge bg-success">Đã trả</span>';
                                                break;
                                            case 'lost':
                                                echo '<span class="badge bg-danger">Mất</span>';
                                                break;
                                            default:
                                                echo '<span class="badge bg-secondary">' . $item['status'] . '</span>';
                                        }
                                        ?>
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

    <!-- Return History -->
    <?php if (!empty($returnHistory)): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Lịch sử trả sách</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ngày trả</th>
                                    <th>Người xử lý</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($returnHistory as $return): ?>
                                <tr>
                                    <td>#<?= $return['id'] ?></td>
                                    <td><?= $return['return_date'] ?></td>
                                    <td><?= htmlspecialchars($return['librarian_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($return['note'] ?? '') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>