<?php
// Protected page 2 – shows login state persists across pages
session_start();
if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$_SESSION['visits'] = ($_SESSION['visits'] ?? 0) + 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
</head>

<body style="font-family:Arial,sans-serif;margin:40px">
    <h2>Profile</h2>
    <p>User: <b><?= htmlspecialchars($_SESSION['user']) ?></b></p>
    <p>You have opened this page <b><?= (int) $_SESSION['visits'] ?></b> time(s) this session.</p>
    <p><a href="dashboard.php">Back to Dashboard</a> | <a href="logout.php">Logout</a></p>
</body>

</html>