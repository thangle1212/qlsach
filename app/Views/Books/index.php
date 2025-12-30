<?php include __DIR__ . '/../../../app/header.php'; ?>

<div class="card">
    <h2>Quản lý sách</h2>

    <a href="index.php?controller=book&action=create">➕ Thêm sách</a>
    <br><br>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <tr>
            <th>ID</th>
            <th>Tên sách</th>
            <th>Tổng</th>
            <th>Còn</th>
            <th>Đang mượn</th>
            <th>Hành động</th>
        </tr>

        <?php foreach ($books as $b): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= htmlspecialchars($b['title']) ?></td>
            <td><?= $b['total_copies'] ?></td>
            <td><?= $b['available_copies'] ?></td>
            <td><?= $b['borrowed'] ?></td>
            <td>
                <a href="index.php?controller=book&action=edit&id=<?= $b['id'] ?>">✏️ Sửa</a> |
                <a href="index.php?controller=book&action=delete&id=<?= $b['id'] ?>"
                   onclick="return confirm('Xóa sách này?')">🗑 Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include __DIR__ . '/../../../app/footer.php'; ?>