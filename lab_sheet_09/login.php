<?php
/** Task 4: Login with password_verify() + session_start()
 *  Task 17: "Remember Me" cookie */
require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    $stmt = db_query($conn, 'SELECT id, name, password_hash FROM users WHERE email = ?', 's', [$email]);
    $user = $stmt->get_result()->fetch_assoc();

    // Same message for unknown email and wrong password (prevents user enumeration)
    if ($user && password_verify($password, $user['password_hash'])) {
        // Upgrade the hash automatically if PHP's default algorithm/cost changed
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            db_query($conn, 'UPDATE users SET password_hash = ? WHERE id = ?', 'si', [$newHash, $user['id']]);
        }

        login_user($user);

        if ($remember) {
            set_remember_cookie($conn, (int)$user['id']);
        }
        redirect('dashboard.php');
    }
    $error = 'Invalid email or password.';
}

$pageTitle = 'Login';
require __DIR__ . '/includes/header.php';
?>
<div class="card narrow">
    <h1>Log in</h1>
    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <?= csrf_field() ?>
        <label>Email
            <input type="email" name="email" value="<?= e($email) ?>" required>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <label class="check">
            <input type="checkbox" name="remember" value="1"> Remember me for <?= REMEMBER_DAYS ?> days
        </label>
        <button type="submit" class="btn">Log in</button>
    </form>
    <p>No account? <a href="register.php">Register</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
