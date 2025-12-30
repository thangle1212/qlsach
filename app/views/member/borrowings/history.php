<?php $pageTitle = 'Lịch sử mượn sách'; ?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-history"></i> Lịch sử mượn sách</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($history)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tiêu đề sách</th>
                                    <th>Ngày mượn</th>
                                    <th>Ngày trả</th>
                                    <th>Trạng thái</th>
                                    <th>Phạt</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $h): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($h['book_title']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($h['borrow_date'])) ?></td>
                                        <td><?= $h['return_date'] ? date('d/m/Y', strtotime($h['return_date'])) : '-' ?></td>
                                        <td>
                                            <span class="status-<?= $h['status'] ?>">
                                                <?php
                                                switch($h['status']) {
                                                    case 'borrowed': echo 'Đang mượn'; break;
                                                    case 'returned': echo 'Đã trả'; break;
                                                    case 'overdue': echo 'Quá hạn'; break;
                                                    case 'lost': echo 'Mất sách'; break;
                                                    default: echo $h['status'];
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td><?= $h['fine_amount'] ?></td>
                                        <td><?= htmlspecialchars($h['notes']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <h5>Chưa có lịch sử mượn sách</h5>
                        <p class="text-muted">Hãy mượn sách để lịch sử được lưu</p>
                        <a href="/qlisach/member/books" class="btn btn-primary">Tìm sách để mượn</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
