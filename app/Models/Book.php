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
        $sql = "SELECT b.*, (b.total_copies - b.available_copies) AS borrowed, a.name as author_name, p.name as publisher_name, c.name as category_name FROM books b LEFT JOIN authors a ON b.author_id = a.id LEFT JOIN publishers p ON b.publisher_id = p.id LEFT JOIN categories c ON b.category_id = c.id ORDER BY b.id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $sql = "SELECT b.*,
            (b.total_copies - b.available_copies) AS borrowed,
            a.name as author_name,
            p.name as publisher_name,
            c.name as category_name
            FROM books b
            LEFT JOIN authors a ON b.author_id = a.id
            LEFT JOIN publishers p ON b.publisher_id = p.id
            LEFT JOIN categories c ON b.category_id = c.id
            WHERE b.id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ======================
       THÊM SÁCH
    ====================== */
    public function insert($d) {
        $sql = "
            INSERT INTO books
            (title, isbn, author_id, publisher_id, category_id, total_copies, available_copies, publication_year, pages, description)
            VALUES (?,?,?,?,?,?,?,?,?,?)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $d['title'],
            $d['isbn'] ?? null,
            $d['author_id'] ?? null,
            $d['publisher_id'] ?? null,
            $d['category_id'] ?? null,
            $d['total_copies'],
            $d['total_copies'], // mới thêm => còn đủ
            $d['publication_year'] ?? null,
            $d['pages'] ?? null,
            $d['description'] ?? null
        ]);
    }

    /* ======================
       CẬP NHẬT SÁCH
    ====================== */
    public function update($id, $d) {
        $sql = "
            UPDATE books
            SET title=?, isbn=?, author_id=?, publisher_id=?, category_id=?, total_copies=?,
                publication_year=?, pages=?, description=?
            WHERE id=?
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $d['title'],
            $d['isbn'] ?? null,
            $d['author_id'] ?? null,
            $d['publisher_id'] ?? null,
            $d['category_id'] ?? null,
            $d['total_copies'],
            $d['publication_year'] ?? null,
            $d['pages'] ?? null,
            $d['description'] ?? null,
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

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAvailableCopies($id, $available) {
        try {
            $stmt = $this->db->prepare("UPDATE books SET available_copies = ? WHERE id = ?");
            return $stmt->execute([$available, $id]);
        } catch (PDOException $e) {
            error_log("Book Update Available Copies Error: " . $e->getMessage());
            return false;
        }
    }

    public function search($keyword) {
        $sql = "SELECT b.*,
            (b.total_copies - b.available_copies) AS borrowed,
            a.name as author_name,
            p.name as publisher_name,
            c.name as category_name
            FROM books b
            LEFT JOIN authors a ON b.author_id = a.id
            LEFT JOIN publishers p ON b.publisher_id = p.id
            LEFT JOIN categories c ON b.category_id = c.id
            WHERE b.title LIKE ? OR b.isbn LIKE ? OR a.name LIKE ?
            ORDER BY b.title";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['%' . $keyword . '%', '%' . $keyword . '%', '%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($category_id) {
        $stmt = $this->db->prepare("
            SELECT b.* FROM books b
            WHERE b.category_id = ?
            ORDER BY b.title
        ");
        $stmt->execute([$category_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByAuthor($author_id) {
        $stmt = $this->db->prepare("
            SELECT b.* FROM books b
            WHERE b.author_id = ?
            ORDER BY b.title
        ");
        $stmt->execute([$author_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findWithRelations($id) {
    $sql = "
        SELECT 
            b.*,
            a.name AS author_name,
            p.name AS publisher_name,
            c.name AS category_name,
            (b.total_copies - b.available_copies) AS borrowed
        FROM books b
        LEFT JOIN authors a ON b.author_id = a.id
        LEFT JOIN publishers p ON b.publisher_id = p.id
        LEFT JOIN categories c ON b.category_id = c.id
        WHERE b.id = ?
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}
