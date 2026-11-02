<?php
require 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $s = $pdo->prepare("SELECT id,name,email,password_hash,role FROM users WHERE email=?");
    $s->execute([$email ?: '']);
    $u = $s->fetch();
    if ($u && password_verify($password, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name'], 'email' => $u['email'], 'role' => $u['role']];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
$pageTitle = 'Login';
require 'includes/header.php'; ?>
<form class="form-card" method="post" data-validate>
    <h1>Welcome back</h1><?php if (isset($_GET['registered'])): ?>
        <div class="notice">Registration successful. Please log in.</div><?php endif; ?><?php if ($error): ?>
        <div class="error"><?= e($error) ?></div><?php endif; ?><label>Email</label><input type="email" name="email"
        required><label>Password</label><input type="password" name="password" required><button class="button">Log
        in</button>
    <p>New here? <a href="register.php">Create an account</a></p>
</form>
<?php require 'includes/footer.php'; ?>