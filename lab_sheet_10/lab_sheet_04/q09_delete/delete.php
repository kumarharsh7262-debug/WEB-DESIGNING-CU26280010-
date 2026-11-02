<?php
// Q9 – Delete a student using a GET parameter: delete.php?id=3
// (Lab requirement uses GET. In production, prefer POST + CSRF token for destructive actions.)
$pdo = require __DIR__ . '/../config/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Invalid or missing id. Usage: delete.php?id=3');
}

$stmt = $pdo->prepare('DELETE FROM students WHERE id = :id');
$stmt->execute([':id' => $id]);

$msg = $stmt->rowCount()
    ? "Student #$id deleted."
    : "No student found with ID $id.";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delete Student</title>
</head>

<body style="font-family:Arial,sans-serif;margin:40px">
    <h3><?= htmlspecialchars($msg) ?></h3>
    <a href="../q07_display/display.php">Back to student list</a>
</body>

</html>