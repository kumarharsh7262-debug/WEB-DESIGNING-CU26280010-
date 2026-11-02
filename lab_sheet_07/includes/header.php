<?php if (!isset($pageTitle))
  $pageTitle = 'BookNest Online Bookstore'; ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | BookNest</title>
  <link rel="stylesheet" href="assets/style.css">
  <script defer src="assets/validation.js"></script>
</head>

<body>
  <header class="site-header">
    <a class="brand" href="index.php">📚 BookNest</a>
    <nav>
      <a href="index.php">Home</a><a href="index.php#books">Books</a><a href="wishlist.php">Wishlist</a>
      <?php if (!empty($_SESSION['user'])): ?>
        <a href="orders.php">My Orders</a>
        <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?><a href="admin/index.php">Admin</a><?php endif; ?>
        <span class="nav-user">Hi, <?= e($_SESSION['user']['name']) ?></span><a href="logout.php">Logout</a>
      <?php else: ?><a href="login.php">Login</a><a href="register.php">Register</a><?php endif; ?>
      <a class="cart-link" href="cart.php">Cart (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
    </nav>
  </header>
  <main class="container">