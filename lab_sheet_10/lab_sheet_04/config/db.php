<?php
/**
 * Shared PDO connection used by most solutions.
 * Edit the credentials below to match your MySQL setup.
 * Usage: $pdo = require __DIR__ . '/../config/db.php';
 */
$host    = 'localhost';
$db      = 'college_db';
$user    = 'root';      // change to 'college_app' after Q14
$pass    = '';          // XAMPP default is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // real prepared statements
];

try {
    return new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}
