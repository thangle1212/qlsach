<div class="container" style="max-width:500px; margin:50px auto;">
    <h2 style="text-align:center; margin-bottom:20px;">Thêm người dùng mới</h2>

    <?php if(!empty($data['error'])): ?>
        <div style="color:red; text-align:center; margin-bottom:10px; font-weight:bold;"><?= $data['error'] ?></div>
    <?php endif; ?>

    <form method="post" action="/ADMIN/User/add">
        <div style="margin-bottom:10px;">
            <label>Username:</label><br>
            <input type="text" name="username" required style="width:100%; height:35px; font-size:14px;">
        </div>

        <div style="margin-bottom:10px;">
            <label>Email:</label><br>
            <input type="email" name="email" required style="width:100%; height:35px; font-size:14px;">
        </div>

        <div style="margin-bottom:10px;">
            <label>Password:</label><br>
            <input type="text" name="password" required style="width:100%; height:35px; font-size:14px;" placeholder="Nhập mật khẩu">
        </div>

        <div style="margin-bottom:15px;">
            <label>Quyền:</label><br>
            <select name="role" required style="width:100%; height:35px; font-size:14px;">
                <option value="member">Member</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div style="text-align:center;">
            <button class="btn add" type="submit" name="add" style="background:#27ae60; color:white; padding:8px 25px; font-size:14px; border:none; border-radius:5px; cursor:pointer;">Thêm</button>
            <a href="/ADMIN/User" class="btn" style="background:#7f8c8d; color:white; padding:8px 25px; font-size:14px; text-decoration:none; border-radius:5px; margin-left:10px;">Hủy</a>
        </div>
    </form>
</div>
