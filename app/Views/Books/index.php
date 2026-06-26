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
        <?php if ($_SESSION['role'] === 'member' && !empty($books)): ?>
        <form method="post" action="index.php?controller=member&action=borrowMultiple" class="mt-3">
        <?php endif; ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <?php if ($_SESSION['role'] === 'member'): ?>
                    <th><input type="checkbox" id="select-all" onclick="toggleSelectAll(this)"></th>
                    <?php endif; ?>
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
                    <?php if ($_SESSION['role'] === 'member'): ?>
                    <td>
                        <?php if ($b['available_copies'] > 0): ?>
                        <input type="checkbox" name="book_ids[]" value="<?= $b['id'] ?>" class="book-checkbox">
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
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
                                <a href="index.php?controller=member&action=borrow&book_id=<?= $b['id'] ?>" class="btn btn-sm btn-success me-1">
                                    <i class="fas fa-book"></i> Mượn đơn
                                </a>
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
        <?php if ($_SESSION['role'] === 'member' && !empty($books)): ?>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-book"></i> Mượn các sách đã chọn
                </button>
            </div>
            <div>
                <small class="text-muted">Chỉ những sách còn mới có thể chọn</small>
            </div>
        </div>
        </form>
        <?php endif; ?>
    </div>

    <script>
    function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('.book-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }

    // Validate before submitting the multiple borrow form
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action*="borrowMultiple"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                const selectedBooks = document.querySelectorAll('input[name="book_ids[]"]:checked');
                if (selectedBooks.length === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một cuốn sách để mượn.');
                    return false;
                }
            });
        }
    });
    </script>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>