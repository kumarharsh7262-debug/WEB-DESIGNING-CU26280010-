<?php
// Q12 – Authenticate username/password against MySQL records
$pdo = require __DIR__ . '/../config/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = :u LIMIT 1');
  $stmt->execute([':u' => $username]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password_hash'])) {
    $message = '<p style="color:green">Welcome, ' . htmlspecialchars($user['username']) . '! Login successful.</p>';
  } else {
    // Same message for unknown user and wrong password (avoids user enumeration)
    $message = '<p style="color:red">Invalid username or password.</p>';
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      max-width: 340px;
      margin: 60px auto
    }

    input,
    button {
      width: 100%;
      padding: 8px;
      margin: 6px 0;
      box-sizing: border-box
    }

    button {
      background: #388e3c;
      color: #fff;
      border: 0;
      cursor: pointer
    }
  </style>
</head>

<body>
  <h2>Login</h2>
  <?= $message ?>
  <form method="post">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Log in</button>
  </form>
  <p><small>First time? Run <code>setup_user.php</code> once.</small></p>
</body>

</html>