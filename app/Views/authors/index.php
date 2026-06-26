<?php include __DIR__ . '/../../header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Quản lý tác giả</h2>
                <a href="index.php?controller=author&action=create" class="btn btn-success">
                    <i class="fas fa-plus"></i> Thêm tác giả
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
                                    <th>Quốc tịch</th>
                                    <th>Năm sinh</th>
                                    <th>Năm mất</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($authors as $author): ?>
                                <tr>
                                    <td><?= $author['id'] ?></td>
                                    <td><?= htmlspecialchars($author['name']) ?></td>
                                    <td><?= htmlspecialchars($author['nationality'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($author['birth_year'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($author['death_year'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="index.php?controller=author&action=edit&id=<?= $author['id'] ?>" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <a href="index.php?controller=author&action=delete&id=<?= $author['id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa tác giả này?')">
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