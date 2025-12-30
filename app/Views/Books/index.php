<?php include __DIR__ . '/../../header.php'; ?>

<div class="card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý sách</h2>
        <div class="d-flex">
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'librarian'): ?>
                <a href="index.php?controller=book&action=create" class="btn btn-success me-2">
                    <i class="fas fa-plus"></i> Thêm sách
                </a>
            <?php endif; ?>
            <form method="GET" action="index.php" class="d-flex">
                <input type="hidden" name="controller" value="book">
                <input type="hidden" name="action" value="index">
                <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm sách..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit" class="btn btn-outline-primary">Tìm kiếm</button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên sách</th>
                    <th>Tác giả</th>
                    <th>Nhà xuất bản</th>
                    <th>Tổng</th>
                    <th>Còn</th>
                    <th>Đang mượn</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $b): ?>
                <tr>
                    <td><?= $b['id'] ?></td>
                    <td><?= htmlspecialchars($b['title']) ?></td>
                    <td><?= htmlspecialchars($b['author_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($b['publisher_name'] ?? 'N/A') ?></td>
                    <td><?= $b['total_copies'] ?></td>
                    <td><?= $b['available_copies'] ?></td>
                    <td><?= $b['borrowed'] ?></td>
                    <td>
                        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'librarian'): ?>
                            <a href="index.php?controller=book&action=show&id=<?= $b['id'] ?>" class="btn btn-sm btn-info me-1">
                                <i class="fas fa-info-circle"></i> Chi tiết
                            </a>
                            <a href="index.php?controller=book&action=edit&id=<?= $b['id'] ?>" class="btn btn-sm btn-warning me-1">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <a href="index.php?controller=book&action=delete&id=<?= $b['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Xóa sách này?')">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        <?php elseif ($_SESSION['role'] === 'member'): ?>
                            <a href="index.php?controller=book&action=show&id=<?= $b['id'] ?>" class="btn btn-sm btn-info me-1">
                                <i class="fas fa-info-circle"></i> Chi tiết
                            </a>
                            <?php if ($b['available_copies'] > 0): ?>
                                <form method="post" action="index.php?controller=member&action=borrow" style="display: inline;">
                                    <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-book"></i> Mượn sách
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="post" action="index.php?controller=member&action=reserve" style="display: inline;">
                                    <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <i class="fas fa-calendar-plus"></i> Đặt trước
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>