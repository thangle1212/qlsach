<?php
class DB {
    public static function connect() {
        return mysqli_connect("localhost", "root", "", "qlisach");
    }
}
