<?php
// Run ONCE in the browser to create a demo user: admin / admin123
// (Passwords are stored hashed – see Q21 for the full hashing workflow.)
$pdo = require __DIR__ . '/../config/db.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

$stmt = $pdo->prepare('INSERT IGNORE INTO users (username, password_hash) VALUES (?, ?)');
$stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);

echo 'Demo user ready: <b>admin</b> / <b>admin123</b>. <a href="login.php">Go to login</a>';
