<?php
/** Task 10: Secure DELETE handler (POST only + CSRF token + prepared statement).
 *  The JavaScript confirm() prompt lives on the delete form in products.php. */
require_once __DIR__ . '/includes/init.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('products.php'); // never delete via a GET link
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);

$product = db_query($conn, 'SELECT image FROM products WHERE id = ?', 'i', [$id])->get_result()->fetch_assoc();
if (!$product) {
    flash('error', 'Product not found.');
    redirect('products.php');
}

try {
    db_query($conn, 'DELETE FROM products WHERE id = ?', 'i', [$id]);
    delete_image_file($product['image']);
    flash('success', 'Product deleted.');
} catch (mysqli_sql_exception $e) {
    // e.g. foreign-key restriction: product already appears in an order
    flash('error', 'This product cannot be deleted because it is part of existing orders.');
}
redirect('products.php');
