<?php
// Q6 – Insert a student using a prepared statement
$pdo = require __DIR__ . '/../config/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $date  = $_POST['enrollment_date'] ?? date('Y-m-d');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<p style="color:red">Please enter a valid name and email.</p>';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO students (name, email, enrollment_date) VALUES (:name, :email, :date)'
            );
            $stmt->execute([':name' => $name, ':email' => $email, ':date' => $date]);
            $message = '<p style="color:green">Student added with ID ' . (int)$pdo->lastInsertId() . '.</p>';
        } catch (PDOException $e) {
            $message = ($e->getCode() == 23000)
                ? '<p style="color:red">That email already exists.</p>'
                : '<p style="color:red">Database error.</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Add Student</title>
<style>
  body{font-family:Arial,sans-serif;max-width:420px;margin:40px auto}
  input,button{width:100%;padding:8px;margin:6px 0;box-sizing:border-box}
  button{background:#1976d2;color:#fff;border:0;cursor:pointer}
</style>
</head>
<body>
<h2>Add Student</h2>
<?= $message ?>
<form method="post">
  <input type="text" name="name" placeholder="Full name" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="date" name="enrollment_date" value="<?= date('Y-m-d') ?>" required>
  <button type="submit">Insert</button>
</form>
<p><a href="../q07_display/display.php">View all students</a></p>
</body>
</html>
