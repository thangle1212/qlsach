<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Bảng điều khiển Admin</h2>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x mb-3"></i>
                    <h3 class="card-title"><?=$totalBooks?></h3>
                    <p class="card-text">Tổng số sách</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3 class="card-title"><?=$totalUsers?></h3>
                    <p class="card-text">Tổng số người dùng</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-book-reader fa-3x mb-3"></i>
                    <h3 class="card-title"><?=$activeBorrowings?></h3>
                    <p class="card-text">Sách đang mượn</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h3 class="card-title"><?=$overdueBorrowings?></h3>
                    <p class="card-text">Sách quá hạn</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-tachometer-alt"></i> Tổng quan</h5>
                </div>
                <div class="card-body">
                    <p>Chào mừng bạn đến với bảng điều khiển quản trị hệ thống thư viện.</p>
                    <p>Từ đây, bạn có thể quản lý người dùng, sách, mượn trả và xem các báo cáo thống kê.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>