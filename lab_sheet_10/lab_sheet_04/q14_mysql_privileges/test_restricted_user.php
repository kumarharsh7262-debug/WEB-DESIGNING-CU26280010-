<?php
// Q14 – Proves the restricted user can SELECT but cannot DELETE
$pdo = new PDO('mysql:host=localhost;dbname=college_db;charset=utf8mb4', 'college_app', 'StrongPass@123', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

echo '<h3>Connected as college_app</h3>';
echo 'SELECT works – rows: ' . $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn() . '<br>';

try {
    $pdo->exec('DELETE FROM students WHERE id = -1');
    echo 'DELETE succeeded (unexpected!)';
} catch (PDOException $e) {
    echo '<span style="color:green">DELETE blocked as expected:</span> ' . htmlspecialchars($e->getMessage());
}
