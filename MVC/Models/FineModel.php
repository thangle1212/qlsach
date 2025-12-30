<?php
class FineModel {
    private $db;

    function __construct() {
        $this->db = DB::connect();
    }

    function getAll() {
        return $this->db->query("
            SELECT f.id, u.username, f.amount, f.reason, f.status
            FROM fines f JOIN users u ON f.user_id=u.id
        ");
    }

    function get($id) {
        $id = $this->db->real_escape_string($id);
        $res = $this->db->query("
            SELECT f.*, u.username 
            FROM fines f JOIN users u ON f.user_id=u.id
            WHERE f.id=$id
        ");
        return $res->fetch_assoc();
    }

    function getUsers() {
        return $this->db->query("SELECT id, username FROM users");
    }

    function insert($user_id, $amount, $reason, $status) {
        $user_id = $this->db->real_escape_string($user_id);
        $amount = $this->db->real_escape_string(str_replace(',', '', $amount)); // remove ',' 
        $reason = $this->db->real_escape_string($reason);
        $status = $this->db->real_escape_string($status);

        if(!is_numeric($amount) || $amount <= 0) return false;

        return $this->db->query("
            INSERT INTO fines(user_id, borrowing_id, amount, reason, status)
            VALUES('$user_id', 1, '$amount', '$reason', '$status')
        ");
    }

    function update($id, $user_id, $amount, $reason, $status) {
        $id = $this->db->real_escape_string($id);
        $user_id = $this->db->real_escape_string($user_id);
        $amount = $this->db->real_escape_string(str_replace(',', '', $amount));
        $reason = $this->db->real_escape_string($reason);
        $status = $this->db->real_escape_string($status);

        if(!is_numeric($amount) || $amount <= 0) return false;

        return $this->db->query("
            UPDATE fines 
            SET user_id='$user_id', amount='$amount', reason='$reason', status='$status'
            WHERE id=$id
        ");
    }

    function delete($id) {
        $id = $this->db->real_escape_string($id);
        return $this->db->query("DELETE FROM fines WHERE id=$id");
    }
}
