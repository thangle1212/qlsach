<?php
define('BASE_URL', '/qlisach');

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=qlisach;charset=utf8mb4",
        "root",
        ""
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
