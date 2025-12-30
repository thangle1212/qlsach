<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Quản lý mượn – trả</h2>
                <a href="index.php?controller=borrowing&action=create" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tạo phiếu mượn
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
                                    <th>Người mượn</th>
                                    <th>Sách</th>
                                    <th>Ngày mượn</th>
                                    <th>Ngày trả</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($borrowings as $b): ?>
                                <tr>
                                    <td><?= $b['id'] ?></td>
                                    <td>
                                        <a href="index.php?controller=borrowing&action=viewMember&user_id=<?= $b['user_id'] ?>">
                                            <?= htmlspecialchars($b['full_name']) ?>
                                        </a>
                                    </td>
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
                                    <td>
                                        <?php if ($b['status'] === 'borrowed'): ?>
                                            <a href="index.php?controller=borrowing&action=return&id=<?= $b['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-undo"></i> Trả sách
                                            </a>
                                            <a href="index.php?controller=borrowing&action=renew&id=<?= $b['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-sync-alt"></i> Gia hạn
                                            </a>
                                        <?php else: ?>
                                            <span class="text-success">Đã trả</span>
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