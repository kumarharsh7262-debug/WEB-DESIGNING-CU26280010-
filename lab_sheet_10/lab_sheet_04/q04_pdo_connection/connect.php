<?php
// Q4 – Secure PDO connection and status check
$host = 'localhost';
$db = 'college_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,                  // use native prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "<h2 style='color:green'>Connection successful ✔</h2>";
    echo '<p>Database: <b>' . htmlspecialchars($db) . '</b></p>';
    echo '<p>MySQL server version: <b>' . htmlspecialchars($version) . '</b></p>';
    echo '<p>Connection status: <b>' . htmlspecialchars($pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)) . '</b></p>';
} catch (PDOException $e) {
    // Log details privately; show a generic message to the user
    error_log('DB connection error: ' . $e->getMessage());
    echo "<h2 style='color:red'>Connection failed ✘</h2>";
    echo '<p>Check credentials and that MySQL is running.</p>';
}
