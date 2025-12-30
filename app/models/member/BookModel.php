<?php
class BookModel
{
    private $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    public function getAvailableBooks()
    {
        return $this->db
            ->query("SELECT * FROM books WHERE available_copies > 0 AND is_reference = 0")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBooks()
    {
        return $this->db
            ->query("SELECT * FROM books ORDER BY title")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
