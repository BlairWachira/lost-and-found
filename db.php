<?php

$host   = "localhost";
$dbname = "lost_found";
$dbuser = "root";
$dbpass = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed.");
}

$pdo = $conn;

$pdo->exec("
    CREATE TABLE IF NOT EXISTS items (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        user_id     INT NOT NULL,
        item_name   VARCHAR(150) NOT NULL,
        description TEXT,
        image_path  VARCHAR(255) NOT NULL,
        contact     VARCHAR(150) NOT NULL,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) CHARACTER SET utf8mb4
");

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

