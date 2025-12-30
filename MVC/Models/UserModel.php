<?php
class UserModel {
    private $db;

    function __construct() {
        $this->db = DB::connect();
    }

    function getAll() {
        return $this->db->query("SELECT * FROM users");
    }

    function getOne($id) {
        $res = $this->db->query("SELECT * FROM users WHERE id=$id");
        return $res->fetch_assoc();
    }

    function exists($username, $email, $exclude_id = null) {
        $sql = "SELECT * FROM users WHERE (username='$username' OR email='$email')";
        if($exclude_id) $sql .= " AND id != $exclude_id";
        $res = $this->db->query($sql);
        return $res->num_rows > 0;
    }

    // Lưu mật khẩu thực
    function insert($u, $e, $p, $role) {
        $p = trim($p);
        if(empty($p)) return false;
        return $this->db->query(
            "INSERT INTO users(username,email,password_hash,full_name,role,status)
             VALUES('$u','$e','$p','$u','$role','active')"
        );
    }

    function update($id, $u, $e, $p, $role) {
        $p = trim($p);
        $pass_sql = '';
        if(!empty($p)) {
            $pass_sql = ", password_hash='$p'";
        }
        $sql = "UPDATE users SET username='$u', email='$e', role='$role' $pass_sql WHERE id=$id";
        return $this->db->query($sql);
    }

    function delete($id) {
        return $this->db->query("DELETE FROM users WHERE id=$id");
    }
}
