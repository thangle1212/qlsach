<?php include __DIR__ . '/../../../app/header.php'; ?>

<div class="card">
    <h2>Quản lý mượn – trả</h2>

    <a href="index.php?controller=borrowing&action=create">➕ Mượn sách</a>
    <br><br>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <tr>
            <th>ID</th>
            <th>Người mượn</th>
            <th>Sách</th>
            <th>Ngày mượn</th>
            <th>Ngày trả</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>

        <?php foreach ($borrowings as $b): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= htmlspecialchars($b['full_name']) ?></td>
            <td><?= htmlspecialchars($b['title']) ?></td>
            <td><?= $b['borrow_date'] ?></td>
            <td><?= $b['due_date'] ?></td>
            <td><?= $b['status'] ?></td>
            <td>
                <?php if ($b['status'] === 'borrowed'): ?>
                    <a href="index.php?controller=borrowing&action=return&id=<?= $b['id'] ?>">
                        🔄 Trả sách
                    </a>
                <?php else: ?>
                    ✔ Đã trả
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include __DIR__ . '/../../../app/footer.php'; ?>