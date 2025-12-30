<div class="container" style="max-width:600px; margin:50px auto;">
    <h2 style="text-align:center; margin-bottom:30px;">Thêm Phí Phạt</h2>

    <?php if(!empty($data['error'])): ?>
        <div style="color:red; text-align:center; margin-bottom:15px; font-weight:bold;"><?= $data['error'] ?></div>
    <?php endif; ?>

    <form method="post" action="/ADMIN/Fine/add">
        <div style="margin-bottom:15px;">
            <label>Người dùng:</label><br>
            <select name="user_id" required style="width:100%; height:35px; font-size:16px;">
                <?php while($u = $data['users']->fetch_assoc()): ?>
                    <option value="<?= $u['id'] ?>"><?= $u['username'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label>Số tiền:</label><br>
            <input type="text" name="amount" required style="width:100%; height:35px; font-size:16px;">
        </div>

        <div style="margin-bottom:15px;">
            <label>Lý do:</label><br>
            <select name="reason" required style="width:100%; height:35px; font-size:16px;">
                <option value="overdue">Overdue</option>
                <option value="lost">Lost</option>
                <option value="damaged">Damaged</option>
            </select>
        </div>

        <div style="margin-bottom:20px;">
            <label>Trạng thái:</label><br>
            <select name="status" required style="width:100%; height:35px; font-size:16px;">
                <option value="unpaid" selected>Unpaid</option>
                <option value="paid">Paid</option>
                <option value="waived">Waived</option>
            </select>
        </div>

        <div style="text-align:center;">
            <button type="submit" name="add" style="background:#27ae60; color:white; padding:10px 25px; font-size:16px; border:none; cursor:pointer; border-radius:5px;">Lưu</button>
            <a href="/ADMIN/Fine" style="background:#7f8c8d; color:white; padding:10px 25px; font-size:16px; text-decoration:none; border-radius:5px; margin-left:10px;">Hủy</a>
        </div>
    </form>
</div>
