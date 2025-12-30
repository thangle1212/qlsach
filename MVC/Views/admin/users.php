<div class="container">
    <div class="header" style="display:flex; align-items:center; justify-content:space-between;">
        <h2>Quản lý người dùng</h2>
        <a href="/ADMIN/User/add" class="btn add">+ Thêm</a>
    </div>

    <table class="table">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Password</th> <!-- mật khẩu thực -->
            <th>Trạng thái</th>
            <th>Quyền</th>
            <th>Hành động</th>
        </tr>

        <?php while($u = $data["users"]->fetch_assoc()): ?>
        <tr>
            <td><?= $u["id"] ?></td>
            <td><?= $u["username"] ?></td>
            <td><?= $u["email"] ?></td>
            <td><?= $u["password_hash"] ?></td> <!-- mật khẩu thực -->
            <td><span class="status <?= $u["status"] ?>"><?= $u["status"] ?></span></td>
            <td><?= $u["role"] ?></td>
            <td>
                <a class="btn edit" href="/ADMIN/User/edit/<?= $u["id"] ?>">Sửa</a>
                <a class="btn delete" onclick="return confirm('Xoá người dùng này?')" href="/ADMIN/User/delete/<?= $u["id"] ?>">Xoá</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
