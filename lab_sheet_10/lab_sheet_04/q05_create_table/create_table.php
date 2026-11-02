<?php
// Q5 – Create the `students` table through PHP
$pdo = require __DIR__ . '/../config/db.php';

$sql = "CREATE TABLE IF NOT EXISTS students (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            name            VARCHAR(100) NOT NULL,
            email           VARCHAR(150) NOT NULL UNIQUE,
            enrollment_date DATE NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

try {
    $pdo->exec($sql);
    echo "<h3>Table <code>students</code> created (or already exists).</h3>";

    echo '<h4>Structure:</h4><table border="1" cellpadding="6" cellspacing="0">';
    echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>';
    foreach ($pdo->query('DESCRIBE students') as $col) {
        echo '<tr><td>' . htmlspecialchars($col['Field']) . '</td><td>' . htmlspecialchars($col['Type'])
           . '</td><td>' . htmlspecialchars($col['Null']) . '</td><td>' . htmlspecialchars($col['Key']) . '</td></tr>';
    }
    echo '</table>';
} catch (PDOException $e) {
    echo 'Error: ' . htmlspecialchars($e->getMessage());
}
