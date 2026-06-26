<?php require_once __DIR__ . '/../layout.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Sách đang mượn</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-list"></i> Danh sách sách đang mượn</h5>
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
                                        <th>Hành động</th>
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
                                            <td>
                                                <?php if ($b['status'] == 'borrowed'): ?>
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-primary disabled">Trả sách</a>
                                                <?php else: ?>
                                                    <span class="text-success">Đã trả</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5>Bạn chưa mượn sách nào</h5>
                            <p class="text-muted">Hãy tìm và mượn sách từ danh mục sách</p>
                            <a href="../index.php?controller=book" class="btn btn-primary">Tìm sách để mượn</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>