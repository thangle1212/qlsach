<?php require_once __DIR__ . '/../layout.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Danh sách sách</h2>
        <form method="GET" action="index.php" class="d-flex">
            <input type="hidden" name="controller" value="member">
            <input type="hidden" name="action" value="books">
            <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm sách..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn btn-outline-primary">Tìm kiếm</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Tên sách</th>
                    <th>Tác giả</th>
                    <th>Nhà xuất bản</th>
                    <th>Tổng số</th>
                    <th>Còn lại</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $b): ?>
                <tr>
                    <td><?= $b['id'] ?></td>
                    <td><?= htmlspecialchars($b['title']) ?></td>
                    <td>
                        <?php
                        // Author name is now included in the book data from the model
                        echo htmlspecialchars($b['author_name'] ?? 'N/A');
                        ?>
                    </td>
                    <td>
                        <?php
                        // Publisher name is now included in the book data from the model
                        echo htmlspecialchars($b['publisher_name'] ?? 'N/A');
                        ?>
                    </td>
                    <td><?= $b['total_copies'] ?></td>
                    <td><?= $b['available_copies'] ?></td>
                    <td>
                        <?php if ($b['available_copies'] > 0): ?>
                            <form method="post" action="index.php?controller=member&action=borrow" style="display: inline;">
                                <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">📚 Mượn sách</button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="index.php?controller=member&action=reserve" style="display: inline;">
                                <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                                <button type="submit" class="btn btn-warning btn-sm">🔔 Đặt trước</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>