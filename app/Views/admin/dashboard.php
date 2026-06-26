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
                    <h3 class="card-title"><?= $totalBooks ?></h3>
                    <p class="card-text">Tổng số sách</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3 class="card-title"><?= $totalUsers ?></h3>
                    <p class="card-text">Tổng số người dùng</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-book-reader fa-3x mb-3"></i>
                    <h3 class="card-title"><?= $activeBorrowings ?></h3>
                    <p class="card-text">Sách đang mượn</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h3 class="card-title"><?= $overdueBorrowings ?></h3>
                    <p class="card-text">Sách quá hạn</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-tachometer-alt"></i> Tổng quan thống kê</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Sách được mượn nhiều nhất đến ít nhất</h6>
                            <canvas id="topBorrowedBooksChart" width="400" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h6>Khách hàng mượn nhiều nhất đến ít nhất</h6>
                            <canvas id="topActiveUsersChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Top Borrowed Books Chart
        const topBorrowedBooksCtx = document.getElementById('topBorrowedBooksChart').getContext('2d');
        const topBorrowedBooksData = <?php echo json_encode($topBorrowedBooks); ?>;
        new Chart(topBorrowedBooksCtx, {
            type: 'bar',
            data: {
                labels: topBorrowedBooksData.map(item => item.title),
                datasets: [{
                    label: 'Số lần mượn',
                    data: topBorrowedBooksData.map(item => item.borrow_count),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Top Active Users Chart
        const topActiveUsersCtx = document.getElementById('topActiveUsersChart').getContext('2d');
        const topActiveUsersData = <?php echo json_encode($topActiveUsers); ?>;
        new Chart(topActiveUsersCtx, {
            type: 'bar',
            data: {
                labels: topActiveUsersData.map(item => item.full_name),
                datasets: [{
                    label: 'Số lần mượn',
                    data: topActiveUsersData.map(item => item.borrow_count),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<?php include __DIR__ . '/../../footer.php'; ?>