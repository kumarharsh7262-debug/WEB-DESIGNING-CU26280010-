<?php
require 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    if (!$name || !$email || strlen($password) < 8) {
        $error = 'Enter a name, valid email, and password of at least 8 characters.';
    } else {
        try {
            $s = $pdo->prepare("INSERT INTO users(name,email,password_hash) VALUES(?,?,?)");
            $s->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            $error = 'That email may already be registered.';
        }
    }
}
$pageTitle = 'Register';
require 'includes/header.php'; ?>
<form class="form-card" method="post" data-validate>
    <h1>Create account</h1><?php if ($error): ?>
        <div class="error"><?= e($error) ?></div><?php endif; ?><label>Name</label><input name="name" required maxlength="100"
        value="<?= e($_POST['name'] ?? '') ?>"><label>Email</label><input type="email" name="email" required
        value="<?= e($_POST['email'] ?? '') ?>"><label>Password (minimum 8 characters)</label><input type="password"
        name="password" required minlength="8"><button class="button">Register</button>
    <p>Already registered? <a href="login.php">Log in</a></p>
</form>
<?php require 'includes/footer.php'; ?>