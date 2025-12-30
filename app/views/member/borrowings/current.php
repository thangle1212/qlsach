<?php require_once __DIR__ . '/../layout.php'; ?>

<?php $pageTitle = 'Sách đang mượn'; ?>

<div class="hero bg-success text-white py-5 mb-4">
    <div class="container">
        <h1 class="display-4"><i class="fas fa-book-reader"></i> Sách đang mượn</h1>
        <p class="lead">Theo dõi các cuốn sách bạn đang mượn và hạn trả.</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="fas fa-list"></i> Danh sách sách đang mượn</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($borrowings)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tiêu đề sách</th>
                                    <th>Ngày mượn</th>
                                    <th>Ngày trả</th>
                                    <th>Trạng thái</th>
                                    <th>Phạt</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($borrowings as $borrowing): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($borrowing['book_title']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($borrowing['borrow_date'])) ?></td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($borrowing['due_date'])) ?>
                                            <?php if ($borrowing['status'] == 'borrowed' && strtotime($borrowing['due_date']) < time()): ?>
                                                <span class="badge bg-danger">Quá hạn</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-<?= $borrowing['status'] ?>">
                                                <?php
                                                switch ($borrowing['status']) {
                                                    case 'borrowed':
                                                        echo '<span class="badge bg-primary">Đang mượn</span>';
                                                        break;
                                                    case 'returned':
                                                        echo '<span class="badge bg-success">Đã trả</span>';
                                                        break;
                                                    case 'overdue':
                                                        echo '<span class="badge bg-danger">Quá hạn</span>';
                                                        break;
                                                    case 'lost':
                                                        echo '<span class="badge bg-secondary">Mất sách</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge bg-light text-dark">Không rõ</span>';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td><?= $borrowing['fine_amount'] ?></td>
                                        <td>
                                            <?php if ($borrowing['status'] == 'borrowed'): ?>
                                                <a href="/qlisach/member/borrowings/return/<?= $borrowing['id'] ?>" class="btn btn-sm btn-primary">Trả sách</a>
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
                        <a href="/qlisach/member/books" class="btn btn-primary">Tìm sách để mượn</a>
                    </div>
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