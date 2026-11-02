<?php
require 'config.php';
$pageTitle = 'Discover your next great read';
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$categories = $pdo->query("SELECT DISTINCT category FROM books ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$where = ["stock > 0"];
$params = [];
if ($q !== '') {
    $where[] = "(title LIKE ? OR author LIKE ? OR category LIKE ?)";
    $like = "%$q%";
    array_push($params, $like, $like, $like);
}
if ($category !== '') {
    $where[] = "category = ?";
    $params[] = $category;
}
$perPage = 8;
$page = max(1, (int) ($_GET['page'] ?? 1));
$count = $pdo->prepare("SELECT COUNT(*) FROM books WHERE " . implode(' AND ', $where));
$count->execute($params);
$total = (int) $count->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page = min($page, $pages);
$offset = ($page - 1) * $perPage;
$stmt = $pdo->prepare("SELECT * FROM books WHERE " . implode(' AND ', $where) . " ORDER BY featured DESC, id DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$books = $stmt->fetchAll();
$featured = $pdo->query("SELECT * FROM books WHERE featured=1 AND stock>0 ORDER BY id DESC LIMIT 4")->fetchAll();
require 'includes/header.php';
?>
<section class="hero">
    <div>
        <div class="eyebrow">Your next chapter starts here</div>
        <h1>Stories worth staying up for.</h1>
        <p>Explore hand-picked fiction, practical guides, and books that spark new ideas.</p><a class="button"
            href="#books">Explore books ↓</a>
    </div>
    <div class="hero-art">📚</div>
</section>
<div class="banner"><strong>Reader's special:</strong> Find your next favourite book today. Browse our featured
    collection and discover something new.</div>
<?php if ($featured): ?>
    <div class="section-head">
        <h2>Featured books</h2>
    </div>
    <div class="grid">
        <?php foreach ($featured as $book): ?>
            <article class="book-card">
                <div class="cover"><?php if ($book['image']): ?><img src="<?= e($book['image']) ?>"
                            alt="<?= e($book['title']) ?>"><?php else: ?>📖<?php endif; ?></div>
                <h3><?= e($book['title']) ?></h3>
                <p class="muted">by <?= e($book['author']) ?></p>
                <div class="price"><?= money($book['price']) ?></div><a class="button secondary"
                    href="book.php?id=<?= $book['id'] ?>">View details</a>
            </article><?php endforeach; ?>
    </div><?php endif; ?>
<section id="books">
    <div class="section-head">
        <h2>Browse books</h2><span class="muted"><?= $total ?> book(s) found</span>
    </div>
    <form class="searchbar" method="get"><input name="q" value="<?= e($q) ?>"
            placeholder="Search title, author, or category"><select name="category">
            <option value="">All categories</option><?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>" <?= $cat === $category ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?>
        </select><button class="button">Search</button></form>
    <?php if (!$books): ?>
        <div class="empty">No books matched your search.</div><?php else: ?>
        <div class="grid"><?php foreach ($books as $book): ?>
                <article class="book-card">
                    <div class="cover"><?php if ($book['image']): ?><img src="<?= e($book['image']) ?>"
                                alt="<?= e($book['title']) ?>"><?php else: ?>📘<?php endif; ?></div>
                    <h3><?= e($book['title']) ?></h3>
                    <p class="muted"><?= e($book['author']) ?> · <?= e($book['category']) ?></p>
                    <p class="price"><?= money($book['price']) ?></p>
                    <p class="muted"><?= $book['stock'] ?> in stock</p>
                    <div class="actions"><a class="button" href="book.php?id=<?= $book['id'] ?>">Details</a>
                        <form method="post" action="cart_action.php"><input type="hidden" name="book_id"
                                value="<?= $book['id'] ?>"><input type="hidden" name="action" value="add"><input type="hidden"
                                name="quantity" value="1"><button class="button secondary"
                                <?= $book['stock'] < 1 ? 'disabled' : '' ?>>Add to cart</button></form>
                    </div>
                </article><?php endforeach; ?>
        </div>
        <div class="pagination"><?php for ($i = 1; $i <= $pages; $i++): ?><a
                    href="?<?= http_build_query(['q' => $q, 'category' => $category, 'page' => $i]) ?>"><?= $i ?></a><?php endfor; ?></div>
    <?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>