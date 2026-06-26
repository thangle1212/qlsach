<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Tạo phiếu mượn sách</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="index.php?controller=borrowing&action=store">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Người mượn</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>">
                                        <?= htmlspecialchars($u['full_name']) ?> (<?= $u['username'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="book_ids" class="form-label">Sách (Chọn nhiều sách nếu cần)</label>
                            <select class="form-select" id="book_ids" name="book_ids[]" multiple required>
                                <?php foreach ($books as $b): ?>
                                    <?php if ($b['available_copies'] > 0): ?>
                                        <option value="<?= $b['id'] ?>">
                                            <?= htmlspecialchars($b['title']) ?> (còn <?= $b['available_copies'] ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Giữ phím Ctrl để chọn nhiều sách</small>
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Ngày trả</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?controller=borrowing" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-book"></i> Tạo phiếu mượn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default due date to 14 days from today
    const today = new Date();
    const dueDate = new Date(today);
    dueDate.setDate(dueDate.getDate() + 14);

    const formattedDate = dueDate.toISOString().split('T')[0];
    document.getElementById('due_date').value = formattedDate;
});
</script>

<?php include __DIR__ . '/../../footer.php'; ?>