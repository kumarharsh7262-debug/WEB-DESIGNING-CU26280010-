<?php
// Q8 – Update a student's email by student ID
$pdo = require __DIR__ . '/../config/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $email = trim($_POST['email'] ?? '');

    if (!$id || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<p style="color:red">Enter a valid ID and email.</p>';
    } else {
        try {
            $stmt = $pdo->prepare('UPDATE students SET email = :email WHERE id = :id');
            $stmt->execute([':email' => $email, ':id' => $id]);
            $message = $stmt->rowCount()
                ? '<p style="color:green">Email updated.</p>'
                : '<p style="color:orange">No change (ID not found or email identical).</p>';
        } catch (PDOException $e) {
            $message = '<p style="color:red">Update failed (email may already be in use).</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 420px;
            margin: 40px auto
        }

        input,
        button {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            box-sizing: border-box
        }

        button {
            background: #f57c00;
            color: #fff;
            border: 0;
            cursor: pointer
        }
    </style>
</head>

<body>
    <h2>Update Student Email</h2>
    <?= $message ?>
    <form method="post">
        <input type="number" name="id" placeholder="Student ID" min="1" required>
        <input type="email" name="email" placeholder="New email" required>
        <button type="submit">Update</button>
    </form>
    <p><a href="../q07_display/display.php">View all students</a></p>
</body>

</html>