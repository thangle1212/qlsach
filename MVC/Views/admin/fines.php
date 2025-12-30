<div class="container">
    <div class="header" style="display:flex; align-items:center; justify-content:space-between;">
        <h2>Quản lý phí phạt</h2>
        <a href="/ADMIN/Fine/add" class="btn add">+ Thêm</a>
    </div>

    <table class="table">
        <tr>
            <th>ID</th>
            <th>Người dùng</th>
            <th>Số tiền</th>
            <th>Lý do</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>

        <?php while($f = $data["fines"]->fetch_assoc()): ?>
        <tr>
            <td><?= $f["id"] ?></td>
            <td><?= $f["username"] ?></td>
            <td><?= number_format($f["amount"], 0, '', ',') ?> đ</td>
            <td><?= $f["reason"] ?></td>
            <td><span class="status <?= $f["status"] ?>"><?= $f["status"] ?></span></td>
            <td>
                <a class="btn edit" href="/ADMIN/Fine/edit/<?= $f["id"] ?>">Sửa</a>
                <a class="btn delete" onclick="return confirm('Xoá phí phạt này?')" href="/ADMIN/Fine/delete/<?= $f["id"] ?>">Xoá</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
