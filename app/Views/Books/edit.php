<h2>Sửa sách</h2>

<form method="post" action="index.php?controller=book&action=update&id=<?= $book['id'] ?>">
    <label>Tên sách:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required><br><br>

    <label>Author ID:</label><br>
    <input type="number" name="author_id" value="<?= $book['author_id'] ?>"><br><br>

    <label>Publisher ID:</label><br>
    <input type="number" name="publisher_id" value="<?= $book['publisher_id'] ?>"><br><br>

    <label>Category ID:</label><br>
    <input type="number" name="category_id" value="<?= $book['category_id'] ?>"><br><br>

    <label>Tổng số:</label><br>
    <input type="number" name="total_copies" value="<?= $book['total_copies'] ?>"><br><br>

    <button type="submit">💾 Cập nhật</button>
</form>
