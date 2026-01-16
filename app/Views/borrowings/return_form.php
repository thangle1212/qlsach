<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Trả sách - Phiếu mượn #<?= $loanSlipId ?></h2>
                <a href="index.php?controller=borrowing" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Thông tin phiếu mượn</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>ID:</th>
                            <td>#<?= $loanSlipId ?></td>
                        </tr>
                        <tr>
                            <th>Ngày mượn:</th>
                            <td><?= $loanSlip['borrow_date'] ?? 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>Ngày hẹn trả:</th>
                            <td><?= $loanSlip['due_date'] ?? 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>Trạng thái:</th>
                            <td>
                                <span class="badge bg-warning"><?= $loanSlip['status'] ?? 'N/A' ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Chọn sách cần trả</h5>
                </div>
                <div class="card-body">
                    <form action="index.php?controller=borrowing&action=processReturn" method="post">
                        <input type="hidden" name="loan_slip_id" value="<?= $loanSlipId ?>">
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Chọn</th>
                                        <th>Tiêu đề sách</th>
                                        <th>Số lượng mượn</th>
                                        <th>Đã trả</th>
                                        <th>Có thể trả</th>
                                        <th>Số lượng trả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($returnableItems as $item): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="return_items[<?= $item['id'] ?>]" value="<?= $item['returnable_quantity'] ?>" 
                                                   onchange="toggleQuantityInput(this, <?= $item['id'] ?>)" 
                                                   id="chk_<?= $item['id'] ?>">
                                        </td>
                                        <td><?= htmlspecialchars($item['title']) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td><?= $item['returned_quantity'] ?></td>
                                        <td><?= $item['returnable_quantity'] ?></td>
                                        <td>
                                            <input type="number" 
                                                   name="return_items[<?= $item['id'] ?>]" 
                                                   id="qty_<?= $item['id'] ?>" 
                                                   min="1" 
                                                   max="<?= $item['returnable_quantity'] ?>" 
                                                   value="<?= $item['returnable_quantity'] ?>" 
                                                   class="form-control" 
                                                   disabled>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú (nếu có)</label>
                            <textarea name="note" id="note" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Xác nhận trả sách
                        </button>
                        <a href="index.php?controller=borrowing" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleQuantityInput(checkbox, itemId) {
    const qtyInput = document.getElementById('qty_' + itemId);
    if (checkbox.checked) {
        qtyInput.disabled = false;
        qtyInput.value = qtyInput.max; // Set to max by default when checked
    } else {
        qtyInput.disabled = true;
        qtyInput.value = 0;
    }
}
</script>

<?php include __DIR__ . '/../../footer.php'; ?>