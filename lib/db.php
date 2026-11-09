<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS practicum4 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    $pdo->exec("USE practicum4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        amount DECIMAL(10, 2) NOT NULL,
        category VARCHAR(100) NOT NULL,
        date DATE NOT NULL,
        type VARCHAR(50) DEFAULT 'standard',
        frequency VARCHAR(100) DEFAULT NULL
    )");

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Помилка підключення до бази даних: " . $e->getMessage());
}