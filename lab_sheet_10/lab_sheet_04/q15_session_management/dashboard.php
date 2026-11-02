<?php
// Protected page 1
session_start();
if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>

<body style="font-family:Arial,sans-serif;margin:40px">
    <h2>Dashboard</h2>
    <p>Hello, <b><?= htmlspecialchars($_SESSION['user']) ?></b>!</p>
    <p>Logged in at: <?= date('H:i:s', $_SESSION['login_time']) ?></p>
    <p>Session ID: <code><?= htmlspecialchars(session_id()) ?></code></p>
    <p><a href="profile.php">Go to Profile</a> | <a href="logout.php">Logout</a></p>
</body>

</html>