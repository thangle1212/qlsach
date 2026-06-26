<?php
require_once __DIR__ . '/../Core/Database.php';

class Reservation {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->db->prepare("
            SELECT r.*, u.full_name, b.title 
            FROM reservations r
            JOIN users u ON r.user_id = u.id
            JOIN books b ON r.book_id = b.id
            ORDER BY r.reservation_date DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT r.*, u.full_name, b.title 
            FROM reservations r
            JOIN users u ON r.user_id = u.id
            JOIN books b ON r.book_id = b.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($user_id) {
        $stmt = $this->db->prepare("
            SELECT r.*, b.title, b.isbn
            FROM reservations r
            JOIN books b ON r.book_id = b.id
            WHERE r.user_id = ? AND r.status IN ('pending', 'available')
            ORDER BY r.reservation_date DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByBookId($book_id) {
        $stmt = $this->db->prepare("
            SELECT r.*, u.full_name, u.email
            FROM reservations r
            JOIN users u ON r.user_id = u.id
            WHERE r.book_id = ? AND r.status = 'pending'
            ORDER BY r.priority DESC, r.reservation_date ASC
        ");
        $stmt->execute([$book_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO reservations (user_id, book_id, reservation_date, expiry_date, status) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $data['user_id'],
                $data['book_id'],
                $data['reservation_date'],
                $data['expiry_date'],
                $data['status'] ?? 'pending'
            ]);
        } catch (PDOException $e) {
            error_log("Reservation Create Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE reservations SET status = ?, priority = ? WHERE id = ?"
            );
            return $stmt->execute([
                $data['status'],
                $data['priority'] ?? 1,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Reservation Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function cancel($id) {
        try {
            $sql = "UPDATE reservations SET status = 'cancelled' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            // Check if any row was actually updated
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Reservation Cancel Error: " . $e->getMessage());
            return false;
        }
    }

    public function checkActiveReservation($user_id, $book_id) {
        $stmt = $this->db->prepare(
            "SELECT id FROM reservations 
             WHERE user_id = ? AND book_id = ? AND status IN ('pending', 'available')"
        );
        $stmt->execute([$user_id, $book_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markAvailable($id) {
        try {
            $stmt = $this->db->prepare("UPDATE reservations SET status = 'available' WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Reservation Mark Available Error: " . $e->getMessage());
            return false;
        }
    }

    public function markExpired() {
        try {
            $stmt = $this->db->prepare(
                "UPDATE reservations SET status = 'expired' 
                 WHERE status = 'pending' AND expiry_date < CURDATE()"
            );
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Reservation Mark Expired Error: " . $e->getMessage());
            return false;
        }
    }
}
