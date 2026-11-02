<?php
// Expects $pageTitle (optional). init.php must already be loaded by the page.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Web App') ?> | webapp_db</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <a class="brand" href="index.php">WebApp Lab 9</a>
    <div class="links">
        <?php if (is_logged_in()): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="product_add.php">Add Product</a>
            <a href="cart.php">Cart (<?= cart_count() ?>)</a>
            <a href="sqli_demo.php">SQLi Demo</a>
            <a href="xss_demo.php">XSS Demo</a>
            <form method="post" action="logout.php" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="link-btn">Logout (<?= e($_SESSION['user_name']) ?>)</button>
            </form>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <a href="sqli_demo.php">SQLi Demo</a>
            <a href="xss_demo.php">XSS Demo</a>
        <?php endif; ?>
    </div>
</nav>
<main class="container">
<?= flash_render() ?>
