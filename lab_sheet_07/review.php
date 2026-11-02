<?php
require 'config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
$bookId = filter_var($_POST['book_id'] ?? null, FILTER_VALIDATE_INT);
$rating = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT);
$comment = trim($_POST['comment'] ?? '');
if (!$bookId || !$rating || $rating < 1 || $rating > 5) {
    $_SESSION['flash'] = 'Please select a rating from 1 to 5.';
    header('Location: book.php?id=' . (int) $bookId);
    exit;
}
$exists = $pdo->prepare("SELECT id FROM books WHERE id=?");
$exists->execute([$bookId]);
if (!$exists->fetch()) {
    http_response_code(404);
    exit('Book not found.');
}
$s = $pdo->prepare("INSERT INTO reviews(user_id,book_id,rating,comment) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE rating=VALUES(rating),comment=VALUES(comment),created_at=CURRENT_TIMESTAMP");
$s->execute([$_SESSION['user']['id'], $bookId, $rating, $comment]);
header('Location: book.php?id=' . $bookId);
exit;
