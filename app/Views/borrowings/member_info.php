<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Thông tin thành viên</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Thông tin cá nhân</h5>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>ID:</strong></div>
                        <div class="col-sm-8"><?=$user['id']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Tên đăng nhập:</strong></div>
                        <div class="col-sm-8"><?=$user['username']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Họ tên:</strong></div>
                        <div class="col-sm-8"><?=$user['full_name']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Email:</strong></div>
                        <div class="col-sm-8"><?=$user['email']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Số điện thoại:</strong></div>
                        <div class="col-sm-8"><?=$user['phone'] ?? 'Chưa cập nhật'?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Địa chỉ:</strong></div>
                        <div class="col-sm-8"><?=$user['address'] ?? 'Chưa cập nhật'?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Vai trò:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'librarian' ? 'primary' : 'secondary') ?>">
                                <?= $user['role'] === 'admin' ? 'Quản trị viên' : ($user['role'] === 'librarian' ? 'Thủ thư' : 'Thành viên') ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Trạng thái:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'inactive' ? 'secondary' : 'warning') ?>">
                                <?= $user['status'] === 'active' ? 'Hoạt động' : ($user['status'] === 'inactive' ? 'Không hoạt động' : 'Chờ kích hoạt') ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Số sách đang mượn:</strong></div>
                        <div class="col-sm-8"><?=$user['current_borrow_count']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Giới hạn mượn:</strong></div>
                        <div class="col-sm-8"><?=$user['max_borrow_limit']?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Ngày tạo:</strong></div>
                        <div class="col-sm-8"><?=$user['created_at']?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Danh sách mượn sách</h5>
                    <?php if (!empty($userActiveLoans)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Mã phiếu</th>
                                        <th>Tên sách</th>
                                        <th>Ngày mượn</th>
                                        <th>Ngày trả</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userActiveLoans as $loan): ?>
                                        <?php
                                        // Get loan details for this loan slip
                                        $borrowService = new \BorrowService();
                                        $loanDetails = $borrowService->getLoanDetails($loan['id']);
                                        ?>
                                        <?php if (!empty($loanDetails)): ?>
                                            <?php foreach ($loanDetails as $detail): ?>
                                        <tr>
                                            <td>#<?=$loan['id']?></td>
                                            <td><?=htmlspecialchars($detail['title'])?></td>
                                            <td><?=$loan['borrow_date']?></td>
                                            <td><?=$loan['due_date']?></td>
                                            <td>
                                                <?php
                                                switch($loan['status']) {
                                                    case 'active':
                                                        echo '<span class="badge bg-warning">Đang mượn</span>';
                                                        break;
                                                    case 'completed':
                                                        echo '<span class="badge bg-success">Đã hoàn tất</span>';
                                                        break;
                                                    case 'overdue':
                                                        echo '<span class="badge bg-danger">Quá hạn</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge bg-secondary">' . $loan['status'] . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="index.php?controller=borrowing&action=viewLoanDetails&id=<?=$loan['id']?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Xem
                                                </a>
                                                <?php if ($loan['status'] === 'active'): ?>
                                                    <a href="index.php?controller=borrowing&action=viewReturnForm&id=<?=$loan['id']?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-undo"></i> Trả sách
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Không có sách nào trong phiếu mượn này</td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Thành viên chưa mượn sách nào.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Fines section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Danh sách phạt</h5>
                    <?php if (!empty($userFines)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Mã phiếu mượn</th>
                                        <th>Lý do phạt</th>
                                        <th>Số tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userFines as $fine): ?>
                                    <tr>
                                        <td><?=$fine['id']?></td>
                                        <td>#<?=$fine['loan_id']?></td>
                                        <td>
                                            <?php
                                                $reasons = [
                                                    'overdue' => 'Quá hạn',
                                                    'lost' => 'Sách mất',
                                                    'damaged' => 'Sách hư hỏng'
                                                ];
                                                echo htmlspecialchars($reasons[$fine['reason']] ?? $fine['reason']);
                                            ?>
                                        </td>
                                        <td class="fw-bold"><?=number_format($fine['amount'], 0)?> VNĐ</td>
                                        <td>
                                            <?php if ($fine['status'] == 'paid'): ?>
                                                <span class="badge bg-success">Đã trả</span>
                                            <?php elseif ($fine['status'] == 'unpaid'): ?>
                                                <span class="badge bg-danger">Chưa trả</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Miễn</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?=$fine['created_at']?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Thành viên không có khoản phạt nào.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>