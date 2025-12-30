<?php
require_once __DIR__ . '/../Core/Database.php';

class Book {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /* ======================
       LẤY DANH SÁCH SÁCH
    ====================== */
    public function getAll() {
        $sql = "
            SELECT b.*,
            (b.total_copies - b.available_copies) AS borrowed
            FROM books b
            ORDER BY b.id DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ======================
       THÊM SÁCH
    ====================== */
    public function insert($d) {
        $sql = "
            INSERT INTO books
            (title, author_id, publisher_id, category_id, total_copies, available_copies)
            VALUES (?,?,?,?,?,?)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $d['title'],
            $d['author_id'],
            $d['publisher_id'],
            $d['category_id'],
            $d['total_copies'],
            $d['total_copies'] // mới thêm => còn đủ
        ]);
    }

    /* ======================
       CẬP NHẬT SÁCH
    ====================== */
    public function update($id, $d) {
        $sql = "
            UPDATE books
            SET title=?, author_id=?, publisher_id=?, category_id=?, total_copies=?
            WHERE id=?
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $d['title'],
            $d['author_id'],
            $d['publisher_id'],
            $d['category_id'],
            $d['total_copies'],
            $id
        ]);
    }

    /* ======================
       XOÁ SÁCH (CHECK NGHIỆP VỤ)
    ====================== */
    public function canDelete($id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM borrowings
            WHERE book_id=? AND status='borrowed'
        ");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() == 0;
    }

    public function delete($id) {
        if (!$this->canDelete($id)) return false;

        $stmt = $this->db->prepare("DELETE FROM books WHERE id=?");
        return $stmt->execute([$id]);
    }
}
