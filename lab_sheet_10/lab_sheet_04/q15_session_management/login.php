<?php
// Q15 – Session login (demo credentials: admin / admin123)
session_start();

if (!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';

    // Demo check; with the database use the approach from Q12/Q21
    if ($u === 'admin' && $p === 'admin123') {
        session_regenerate_id(true);   // prevents session fixation
        $_SESSION['user'] = $u;
        $_SESSION['login_time'] = time();
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Session Login</title>
</head>

<body style="font-family:Arial,sans-serif;max-width:320px;margin:60px auto">
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
        <input name="username" placeholder="Username" required style="width:100%;padding:8px;margin:5px 0"><br>
        <input type="password" name="password" placeholder="Password" required
            style="width:100%;padding:8px;margin:5px 0"><br>
        <button style="width:100%;padding:8px">Log in</button>
    </form>
</body>

</html>