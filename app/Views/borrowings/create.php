<?php include __DIR__ . '/../../../app/header.php'; ?>

<div class="card">
    <h2>Mượn sách (Admin)</h2>

    <form method="post" action="index.php?controller=borrowing&action=store">

        <label>Người mượn:</label><br>
        <select name="user_id" required>
            <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>">
                    <?= htmlspecialchars($u['full_name']) ?> (<?= $u['username'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Sách:</label><br>
        <select name="book_id" required>
            <?php foreach ($books as $b): ?>
                <?php if ($b['available_copies'] > 0): ?>
                    <option value="<?= $b['id'] ?>">
                        <?= htmlspecialchars($b['title']) ?> (còn <?= $b['available_copies'] ?>)
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Ngày trả:</label><br>
        <input type="date" name="due_date" required><br><br>

        <button type="submit">📚 Mượn</button>
    </form>
</div>

<?php include __DIR__ . '/../../../app/footer.php'; ?>