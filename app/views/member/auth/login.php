<?php require_once __DIR__ . '/../layout.php'; ?>

<div class="container">
    <div class="card" style="max-width: 400px; margin: 50px auto;">
        <h2 style="text-align:center; margin-bottom:20px;">Đăng nhập</h2>
        <form method="post" action="/qlisach/member/auth/handleLogin">
            <div style="margin-bottom: 15px;">
                <label>Username</label>
                <input type="text" name="username" class="form-control" style="width:100%; padding:8px; border-radius:4px; border:1px solid #ccc;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Password</label>
                <input type="password" name="password" class="form-control" style="width:100%; padding:8px; border-radius:4px; border:1px solid #ccc;">
            </div>
            <button type="submit" class="btn" style="width:100%;">Đăng nhập</button>
        </form>
    </div>
</div>
