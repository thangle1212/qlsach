<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Quản lý nhà xuất bản</h2>
                <a href="index.php?controller=publisher&action=create" class="btn btn-success">
                    <i class="fas fa-plus"></i> Thêm NXB
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Địa chỉ</th>
                                    <th>Điện thoại</th>
                                    <th>Email</th>
                                    <th>Website</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($publishers as $publisher): ?>
                                <tr>
                                    <td><?= $publisher['id'] ?></td>
                                    <td><?= htmlspecialchars($publisher['name']) ?></td>
                                    <td><?= htmlspecialchars($publisher['address'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($publisher['phone'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($publisher['email'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($publisher['website'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="index.php?controller=publisher&action=edit&id=<?= $publisher['id'] ?>" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <a href="index.php?controller=publisher&action=delete&id=<?= $publisher['id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa nhà xuất bản này?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>