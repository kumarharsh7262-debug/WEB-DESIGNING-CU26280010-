<?php
require '../config.php';
require_admin();
$pageTitle = 'Admin dashboard';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save_book') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $category = trim($_POST['category'] ?? 'General');
        $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
        $stock = filter_var($_POST['stock'] ?? null, FILTER_VALIDATE_INT);
        $image = trim($_POST['image'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $featured = isset($_POST['featured']) ? 1 : 0;
        if ($title && $author && $price !== false && $price >= 0 && $stock !== false && $stock >= 0) {
            if ($id) {
                $s = $pdo->prepare("UPDATE books SET title=?,author=?,category=?,price=?,stock=?,image=?,description=?,featured=? WHERE id=?");
                $s->execute([$title, $author, $category, $price, $stock, $image, $desc, $featured, $id]);
            } else {
                $s = $pdo->prepare("INSERT INTO books(title,author,category,price,stock,image,description,featured) VALUES(?,?,?,?,?,?,?,?)");
                $s->execute([$title, $author, $category, $price, $stock, $image, $desc, $featured]);
            }
            $message = 'Book saved.';
        } else {
            $message = 'Please enter valid title, author, price and stock.';
        }
    }
    if ($action === 'delete_book') {
        $s = $pdo->prepare("DELETE FROM books WHERE id=?");
        try {
            $s->execute([(int) $_POST['id']]);
            $message = 'Book deleted.';
        } catch (Throwable $ex) {
            $message = 'This book is referenced by an order and cannot be deleted.';
        }
    }
}
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM books WHERE id=?");
    $s->execute([(int) $_GET['edit']]);
    $edit = $s->fetch();
}
$books = $pdo->query("SELECT * FROM books ORDER BY id DESC")->fetchAll();
require '../includes/header.php'; ?>
<h1>Admin dashboard</h1><?php if ($message): ?>
    <div class="notice"><?= e($message) ?></div><?php endif; ?>
<div class="panel">
    <h2><?= $edit ? 'Edit book' : 'Add a book' ?></h2>
    <form method="post" data-validate><input type="hidden" name="action" value="save_book"><input type="hidden"
            name="id" value="<?= e($edit['id'] ?? 0) ?>">
        <div class="form-row">
            <div><label>Title</label><input name="title" required value="<?= e($edit['title'] ?? '') ?>"></div>
            <div><label>Author</label><input name="author" required value="<?= e($edit['author'] ?? '') ?>"></div>
            <div><label>Category</label><input name="category" required value="<?= e($edit['category'] ?? 'General') ?>">
            </div>
            <div><label>Price (₹)</label><input type="number" name="price" min="0" step=".01" required
                    value="<?= e($edit['price'] ?? '') ?>"></div>
            <div><label>Stock</label><input type="number" name="stock" min="0" required
                    value="<?= e($edit['stock'] ?? 0) ?>"></div>
            <div><label>Image URL (optional)</label><input name="image" value="<?= e($edit['image'] ?? '') ?>"></div>
        </div><label>Description</label><textarea
            name="description"><?= e($edit['description'] ?? '') ?></textarea><label><input style="width:auto"
                type="checkbox" name="featured" <?= $edit && $edit['featured'] ? 'checked' : '' ?>> Featured book</label><button
            class="button">Save book</button>
    </form>
</div>
<div class="section-head">
    <h2>Inventory</h2><a href="orders.php">Manage orders</a> · <a href="sales.php">Sales report</a>
</div>
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody><?php foreach ($books as $b): ?>
                <tr>
                    <td><?= $b['id'] ?></td>
                    <td><?= e($b['title']) ?></td>
                    <td><?= money($b['price']) ?></td>
                    <td><?= $b['stock'] ?></td>
                    <td><a href="?edit=<?= $b['id'] ?>">Edit</a>
                        <form style="display:inline" method="post" onsubmit="return confirm('Delete this book?')"><input
                                type="hidden" name="action" value="delete_book"><input type="hidden" name="id"
                                value="<?= $b['id'] ?>"><button class="button danger">Delete</button></form>
                    </td>
                </tr><?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require '../includes/footer.php'; ?>