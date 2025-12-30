<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Chi tiết sách</h2>
                <a href="index.php?controller=book" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-book"></i> Thông tin sách</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <img src="https://placehold.co/200x250?text=Sách" alt="Ảnh bìa" class="img-fluid rounded" style="max-height: 250px;">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Tên sách:</td>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tác giả:</td>
                                    <td><?= htmlspecialchars($book['author_name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Nhà xuất bản:</td>
                                    <td><?= htmlspecialchars($book['publisher_name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Danh mục:</td>
                                    <td><?= htmlspecialchars($book['category_name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">ISBN:</td>
                                    <td><?= htmlspecialchars($book['isbn'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Năm xuất bản:</td>
                                    <td><?= $book['publication_year'] ?? 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Số trang:</td>
                                    <td><?= $book['pages'] ?? 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tổng số lượng:</td>
                                    <td><?= $book['total_copies'] ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Còn lại:</td>
                                    <td><?= $book['available_copies'] ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Đang mượn:</td>
                                    <td><?= $book['borrowed'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if (!empty($book['description'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="fw-bold">Mô tả:</h6>
                            <p class="text-muted"><?= htmlspecialchars($book['description']) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'librarian'): ?>
                            <a href="index.php?controller=book&action=edit&id=<?= $book['id'] ?>" class="btn btn-warning me-md-2">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                        <?php endif; ?>
                        <?php if ($_SESSION['role'] === 'member' && $book['available_copies'] > 0): ?>
                            <form method="post" action="index.php?controller=member&action=borrow" style="display: inline;">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <button type="submit" class="btn btn-success me-md-2">
                                    <i class="fas fa-book"></i> Mượn sách
                                </button>
                            </form>
                        <?php elseif ($_SESSION['role'] === 'member' && $book['available_copies'] <= 0): ?>
                            <form method="post" action="index.php?controller=member&action=reserve" style="display: inline;">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <button type="submit" class="btn btn-warning me-md-2">
                                    <i class="fas fa-calendar-plus"></i> Đặt trước
                                </button>
                            </form>
                        <?php endif; ?>
                        <a href="index.php?controller=book" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>