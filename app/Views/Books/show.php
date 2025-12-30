<h2><?= $book['title'] ?></h2>

<p><b>ISBN:</b> <?= $book['isbn'] ?></p>
<p><b>Tác giả:</b> <?= $book['author_name'] ?></p>
<p><b>Thể loại:</b> <?= $book['category_name'] ?></p>
<p><b>Mô tả:</b><br><?= nl2br($book['description']) ?></p>
<p><b>Giá:</b> <?= number_format($book['price']) ?></p>

<a href="index.php?controller=book">⬅️ Quay lại</a>
