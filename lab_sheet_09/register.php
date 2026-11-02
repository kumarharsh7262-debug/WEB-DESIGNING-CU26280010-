<?php
/** Task 3: Registration with server-side validation + password_hash() */
require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // ---- server-side validation ----
    if ($name === '' || mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $errors[] = 'Name must be between 2 and 100 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors[] = 'Password must contain at least one letter and one number.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // ---- unique email check ----
    if (!$errors) {
        $stmt = db_query($conn, 'SELECT id FROM users WHERE email = ?', 's', [$email]);
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = 'That email is already registered.';
        }
    }

    // ---- store with a hashed password ----
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT); // bcrypt/argon2 with salt
        db_query($conn, 'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)', 'sss', [$name, $email, $hash]);
        flash('success', 'Registration successful! You can log in now.');
        redirect('login.php');
    }
}

$pageTitle = 'Register';
require __DIR__ . '/includes/header.php';
?>
<div class="card narrow">
    <h1>Create an account</h1>
    <?php foreach ($errors as $err): ?>
        <div class="alert error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post" novalidate>
        <?= csrf_field() ?>
        <label>Name
            <input type="text" name="name" value="<?= e($name) ?>" required>
        </label>
        <label>Email
            <input type="email" name="email" value="<?= e($email) ?>" required>
        </label>
        <label>Password <small>(min 8 chars, letters + numbers)</small>
            <input type="password" name="password" required>
        </label>
        <label>Confirm password
            <input type="password" name="confirm_password" required>
        </label>
        <button type="submit" class="btn">Register</button>
    </form>
    <p>Already registered? <a href="login.php">Log in</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
