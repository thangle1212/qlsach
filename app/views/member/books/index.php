<?php require_once __DIR__ . '/../layout.php'; ?>

<h2>Danh sách sách</h2>

<?php foreach ($books as $b): ?>
    <p>
        <?= $b['title'] ?> (<?= $b['available_copies'] ?>)
        <a href="<?php echo BASE_URL; ?>/member/borrowings/borrow?book_id=<?= $b['id'] ?>">Mượn</a>
    </p>
<?php endforeach; ?>

</body>

</html>