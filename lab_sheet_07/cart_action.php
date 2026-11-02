<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}
$id = filter_var($_POST['book_id'] ?? null, FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? 'add';
$qty = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT);
if (!$id || !in_array($action, ['add', 'update', 'remove'], true)) {
    $_SESSION['flash'] = 'Invalid cart action.';
    header('Location: cart.php');
    exit;
}
if ($action === 'remove') {
    unset($_SESSION['cart'][$id]);
    header('Location: cart.php');
    exit;
}
$s = $pdo->prepare("SELECT id,stock FROM books WHERE id=?");
$s->execute([$id]);
$book = $s->fetch();
if (!$book || $book['stock'] < 1) {
    $_SESSION['flash'] = 'This book is currently out of stock.';
    header('Location: cart.php');
    exit;
}
$qty = max(1, (int) $qty);
if ($action === 'add')
    $qty += (int) ($_SESSION['cart'][$id] ?? 0);
if ($qty > (int) $book['stock']) {
    $qty = (int) $book['stock'];
    $_SESSION['flash'] = 'Quantity adjusted to available stock.';
}
$_SESSION['cart'][$id] = $qty;
header('Location: cart.php');
exit;
