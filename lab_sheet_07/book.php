<?php
require 'config.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT b.*, COALESCE(AVG(r.rating),0) average_rating, COUNT(r.id) review_count FROM books b LEFT JOIN reviews r ON r.book_id=b.id WHERE b.id=? GROUP BY b.id");
$stmt->execute([$id ?: 0]);
$book = $stmt->fetch();
if (!$book) {
    http_response_code(404);
    $pageTitle = 'Book not found';
    require 'includes/header.php';
    echo '<div class="empty">Book not found. <a href="index.php">Return to books</a>.</div>';
    require 'includes/footer.php';
    exit;
}
$rv = $pdo->prepare("SELECT r.*,u.name FROM reviews r JOIN users u ON u.id=r.user_id WHERE r.book_id=? ORDER BY r.created_at DESC");
$rv->execute([$book['id']]);
$reviews = $rv->fetchAll();
$pageTitle = $book['title'];
require 'includes/header.php';
?>
<div class="panel">
    <div class="form-row">
        <div class="cover" style="height:300px"><?php if ($book['image']): ?><img src="<?= e($book['image']) ?>"
                    alt="<?= e($book['title']) ?>"><?php else: ?>📖<?php endif; ?></div>
        <div>
            <p class="eyebrow"><?= e($book['category']) ?></p>
            <h1><?= e($book['title']) ?></h1>
            <p class="muted">by <?= e($book['author']) ?></p>
            <p class="price"><?= money($book['price']) ?></p>
            <p><?= nl2br(e($book['description'] ?: 'No description has been added yet.')) ?></p>
            <p class="muted">Stock available: <?= $book['stock'] ?></p>
            <p class="stars">★ <?= number_format((float) $book['average_rating'], 1) ?> / 5 (<?= $book['review_count'] ?>
                reviews)</p>
            <div class="actions">
                <form method="post" action="cart_action.php"><input type="hidden" name="book_id"
                        value="<?= $book['id'] ?>"><input type="hidden" name="action"
                        value="add"><label>Quantity</label><input type="number" name="quantity" value="1" min="1"
                        max="<?= max(1, (int) $book['stock']) ?>"><button class="button"
                        <?= $book['stock'] < 1 ? 'disabled' : '' ?>>Add to cart</button></form>
                <form method="post" action="wishlist.php"><input type="hidden" name="book_id"
                        value="<?= $book['id'] ?>"><input type="hidden" name="action" value="add"><button
                        class="button secondary">♡ Wishlist</button></form>
            </div>
        </div>
    </div>
</div>
<div class="section-head">
    <h2>Reader reviews</h2>
</div>
<?php if (!empty($_SESSION['user'])): ?>
    <form class="form-card" method="post" action="review.php" data-validate><input type="hidden" name="book_id"
            value="<?= $book['id'] ?>"><label for="rating">Rating</label><select id="rating" name="rating" required>
            <option value="">Choose stars</option><?php for ($i = 5; $i >= 1; $i--): ?>
                <option value="<?= $i ?>"><?= $i ?> star(s)</option><?php endfor; ?>
        </select><label for="comment">Comment</label><textarea id="comment" name="comment"
            maxlength="2000"></textarea><button class="button">Submit review</button></form><?php else: ?>
    <p><a href="login.php">Log in</a> to write a review.</p><?php endif; ?>
<?php foreach ($reviews as $r): ?>
    <div class="panel" style="margin:.8rem 0"><strong><?= e($r['name']) ?></strong> <span
            class="stars"><?= str_repeat('★', (int) $r['rating']) ?></span>
        <p><?= nl2br(e($r['comment'])) ?></p><small class="muted"><?= e($r['created_at']) ?></small>
    </div><?php endforeach; ?>
<?php require 'includes/footer.php'; ?>