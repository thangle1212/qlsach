<?php include __DIR__ . '/../../../app/header.php'; ?>

<div class="card">
    <h2>Cài đặt hệ thống</h2>
    
    <form method="post" action="index.php?controller=settings&action=update">
        <div class="form-group">
            <label for="max_borrow_days"><strong>Số ngày mượn tối đa:</strong></label>
            <input type="number" id="max_borrow_days" name="max_borrow_days" 
                   value="<?= htmlspecialchars($settings['max_borrow_days']) ?>" min="1" required>
            <p>Số ngày người dùng được mượn sách. Ảnh hưởng trực tiếp tới due_date trong bảng borrowings.</p>
        </div>
        
        <div class="form-group">
            <label for="fine_per_day"><strong>Phí phạt quá hạn (VNĐ mỗi ngày):</strong></label>
            <input type="number" id="fine_per_day" name="fine_per_day" 
                   value="<?= htmlspecialchars($settings['fine_per_day']) ?>" min="0" required>
            <p>Tiền phạt mỗi ngày quá hạn. Liên quan đến bảng fines và tính toán fine_amount.</p>
        </div>
        
        <div class="form-group">
            <label for="max_books_per_user"><strong>Giới hạn số sách mượn tối đa mỗi người:</strong></label>
            <input type="number" id="max_books_per_user" name="max_books_per_user" 
                   value="<?= htmlspecialchars($settings['max_books_per_user']) ?>" min="1" required>
            <p>Số sách tối đa mỗi người dùng có thể mượn. Kết hợp với cột max_borrow_limit trong bảng users.</p>
        </div>
        
        <button type="submit">Cập nhật cài đặt</button>
    </form>
</div>

<?php include __DIR__ . '/../../../app/footer.php'; ?>