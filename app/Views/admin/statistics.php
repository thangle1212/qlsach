<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Thống kê báo cáo</h2>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3 class="card-title"><?=$totalUsers?></h3>
                    <p class="card-text">Tổng số người dùng</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x mb-3"></i>
                    <h3 class="card-title"><?=$totalBooks?></h3>
                    <p class="card-text">Tổng số sách</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-book-reader fa-3x mb-3"></i>
                    <h3 class="card-title"><?=$activeBorrowings?></h3>
                    <p class="card-text">Sách đang mượn</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h3 class="card-title"><?=$activeUsers?></h3>
                    <p class="card-text">Người dùng hoạt động</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <h3 class="card-title"><?=$overdueBorrowings?></h3>
                    <p class="card-text">Sách quá hạn</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-history fa-3x mb-3 text-muted"></i>
                    <h3 class="card-title"><?=$totalBorrowings?></h3>
                    <p class="card-text">Tổng lượt mượn</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-table"></i> Chi tiết thống kê</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Loại thống kê</th>
                                    <th>Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Tổng số người dùng</td>
                                    <td><?=$totalUsers?></td>
                                </tr>
                                <tr>
                                    <td>Người dùng đang hoạt động</td>
                                    <td><?=$activeUsers?></td>
                                </tr>
                                <tr>
                                    <td>Người dùng không hoạt động</td>
                                    <td><?=$inactiveUsers?></td>
                                </tr>
                                <tr>
                                    <td>Tổng số sách</td>
                                    <td><?=$totalBooks?></td>
                                </tr>
                                <tr>
                                    <td>Sách còn trong thư viện</td>
                                    <td><?=$availableBooks?></td>
                                </tr>
                                <tr>
                                    <td>Sách đang được mượn</td>
                                    <td><?=$borrowedBooks?></td>
                                </tr>
                                <tr>
                                    <td>Tổng lượt mượn sách</td>
                                    <td><?=$totalBorrowings?></td>
                                </tr>
                                <tr>
                                    <td>Đang mượn sách</td>
                                    <td><?=$activeBorrowings?></td>
                                </tr>
                                <tr>
                                    <td>Sách quá hạn</td>
                                    <td><?=$overdueBorrowings?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>