<?php require_once __DIR__ . '/../layout.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Lịch sử mượn sách</h2>

    <?php if (!empty($borrowings)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Tên sách</th>
                        <th>Ngày mượn</th>
                        <th>Ngày trả</th>
                        <th>Ngày hết hạn</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowings as $b): ?>
                    <tr>
                        <td><?= $b['id'] ?></td>
                        <td><?= htmlspecialchars($b['title']) ?></td>
                        <td><?= $b['borrow_date'] ?></td>
                        <td><?= $b['return_date'] ?: 'Chưa trả' ?></td>
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
        <div class="card">
            <div class="card-body text-center">
                <h5>Chưa có lịch sử mượn sách</h5>
                <p class="text-muted">Hãy mượn sách để lịch sử được lưu</p>
                <a href="<?= BASE_URL ?>/member/books" class="btn btn-primary">Tìm sách để mượn</a>
            </div>
        </div>
    <?php endif; ?>
</div>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
