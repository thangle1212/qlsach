<h2>Thêm sách</h2>

<form method="post" action="index.php?controller=book&action=store">
    <label>Tên sách:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Author ID:</label><br>
    <input type="number" name="author_id"><br><br>

    <label>Publisher ID:</label><br>
    <input type="number" name="publisher_id"><br><br>

    <label>Category ID:</label><br>
    <input type="number" name="category_id"><br><br>

    <label>Tổng số:</label><br>
    <input type="number" name="total_copies" value="1"><br><br>

    <button type="submit">💾 Lưu</button>
</form>
