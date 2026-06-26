<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Sửa thông tin sách</h2>
                <a href="index.php?controller=book" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit"></i> Cập nhật thông tin sách
                    </h5>
                </div>

                <div class="card-body">
                    <!-- THÔNG BÁO -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- FORM -->
                    <form method="post"
                          action="index.php?controller=book&action=update&id=<?= $book['id'] ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tên sách *</label>
                                <input type="text"
                                       class="form-control"
                                       name="title"
                                       value="<?= htmlspecialchars($book['title']) ?>"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ISBN</label>
                                <input type="text"
                                       class="form-control"
                                       name="isbn"
                                       value="<?= htmlspecialchars($book['isbn'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Tác giả</label>
                                <select class="form-select" name="author_id">
                                    <option value="">-- Chọn tác giả --</option>
                                    <?php foreach ($authors as $author): ?>
                                        <option value="<?= $author['id'] ?>"
                                            <?= ($book['author_id'] == $author['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($author['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Danh mục</label>
                                <select class="form-select" name="category_id">
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"
                                            <?= ($book['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Nhà xuất bản</label>
                                <select class="form-select" name="publisher_id">
                                    <option value="">-- Chọn NXB --</option>
                                    <?php foreach ($publishers as $publisher): ?>
                                        <option value="<?= $publisher['id'] ?>"
                                            <?= ($book['publisher_id'] == $publisher['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($publisher['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Số lượng *</label>
                                <input type="number"
                                       class="form-control"
                                       name="total_copies"
                                       min="1"
                                       value="<?= $book['total_copies'] ?>"
                                       required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Năm xuất bản</label>
                                <input type="number"
                                       class="form-control"
                                       name="publication_year"
                                       value="<?= $book['publication_year'] ?? '' ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Số trang</label>
                                <input type="number"
                                       class="form-control"
                                       name="pages"
                                       value="<?= $book['pages'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea class="form-control"
                                      name="description"
                                      rows="3"><?= htmlspecialchars($book['description'] ?? '') ?></textarea>
                        </div>

                        <div class="text-end">
                            <a href="index.php?controller=book" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
