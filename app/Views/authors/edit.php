<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Sửa tác giả</h2>
                <a href="index.php?controller=author" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="index.php?controller=author&action=update&id=<?= $author['id'] ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên tác giả *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($author['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="biography" class="form-label">Tiểu sử</label>
                            <textarea class="form-control" id="biography" name="biography" rows="4"><?= htmlspecialchars($author['biography'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="nationality" class="form-label">Quốc tịch</label>
                            <input type="text" class="form-control" id="nationality" name="nationality" value="<?= htmlspecialchars($author['nationality'] ?? '') ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birth_year" class="form-label">Năm sinh</label>
                                    <input type="number" class="form-control" id="birth_year" name="birth_year" value="<?= htmlspecialchars($author['birth_year'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="death_year" class="form-label">Năm mất</label>
                                    <input type="number" class="form-control" id="death_year" name="death_year" value="<?= htmlspecialchars($author['death_year'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?controller=author" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Cập nhật tác giả
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>