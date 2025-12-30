<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Cài đặt hệ thống</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="index.php?controller=settings&action=update">
                        <div class="mb-4">
                            <label for="max_borrow_days" class="form-label fw-bold">Số ngày mượn tối đa</label>
                            <input type="number" class="form-control" id="max_borrow_days" name="max_borrow_days"
                                   value="<?= htmlspecialchars($settings['max_borrow_days']) ?>" min="1" required>
                            <div class="form-text">Số ngày người dùng được mượn sách. Ảnh hưởng trực tiếp tới due_date trong bảng borrowings.</div>
                        </div>

                        <div class="mb-4">
                            <label for="fine_per_day" class="form-label fw-bold">Phí phạt quá hạn (VNĐ mỗi ngày)</label>
                            <input type="number" class="form-control" id="fine_per_day" name="fine_per_day"
                                   value="<?= htmlspecialchars($settings['fine_per_day']) ?>" min="0" required>
                            <div class="form-text">Tiền phạt mỗi ngày quá hạn. Liên quan đến bảng fines và tính toán fine_amount.</div>
                        </div>

                        <div class="mb-4">
                            <label for="max_books_per_user" class="form-label fw-bold">Giới hạn số sách mượn tối đa mỗi người</label>
                            <input type="number" class="form-control" id="max_books_per_user" name="max_books_per_user"
                                   value="<?= htmlspecialchars($settings['max_books_per_user']) ?>" min="1" required>
                            <div class="form-text">Số sách tối đa mỗi người dùng có thể mượn. Kết hợp với cột max_borrow_limit trong bảng users.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Cập nhật cài đặt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>