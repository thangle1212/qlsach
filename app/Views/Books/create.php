<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Thêm sách mới</h2>
                <a href="index.php?controller=book" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-book"></i> Thông tin sách</h5>
                </div>

                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="index.php?controller=book&action=store">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tên sách *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ISBN</label>
                                <input type="text" name="isbn" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tác giả</label>
                                <select name="author_id" class="form-select">
                                    <option value="">-- Chọn tác giả --</option>
                                    <?php foreach ($authors as $a): ?>
                                        <option value="<?= $a['id'] ?>">
                                            <?= htmlspecialchars($a['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nhà xuất bản</label>
                                <select name="publisher_id" class="form-select">
                                    <option value="">-- Chọn nhà xuất bản --</option>
                                    <?php foreach ($publishers as $p): ?>
                                        <option value="<?= $p['id'] ?>">
                                            <?= htmlspecialchars($p['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục</label>
                                <select name="category_id" class="form-select">
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?= $c['id'] ?>">
                                            <?= htmlspecialchars($c['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Số lượng *</label>
                                <input type="number" name="total_copies" value="1" min="1" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Năm xuất bản</label>
                                <input type="number" name="publication_year" min="1900" max="<?= date('Y') ?>" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Số trang</label>
                                <input type="number" name="pages" min="1" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea name="description" rows="3" class="form-control"></textarea>
                        </div>

                        <div class="text-end">
                            <a href="index.php?controller=book" class="btn btn-secondary">
                                Hủy
                            </a>
                            <button type="submit" class="btn btn-success">
                                Thêm sách
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
