<?php
/**
 * Task 7  : Insert product using prepared statements with bound parameters
 * Task 14 : Image upload with type/size validation and unique file naming
 */
require_once __DIR__ . '/includes/init.php';
require_login();

$errors = [];
$name = $category = $price = $stock = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $stock    = trim($_POST['stock'] ?? '');

    if ($name === '' || mb_strlen($name) > 150)      $errors[] = 'Name is required (max 150 characters).';
    if ($category === '' || mb_strlen($category) > 80) $errors[] = 'Category is required (max 80 characters).';
    if (!is_numeric($price) || $price < 0)           $errors[] = 'Price must be a number, 0 or greater.';
    if (!ctype_digit($stock))                        $errors[] = 'Stock must be a whole number, 0 or greater.';

    $imageName = handle_image_upload($_FILES['image'] ?? [], $uploadError);
    if ($uploadError) {
        $errors[] = $uploadError;
    }

    if (!$errors) {
        $priceF = (float)$price;
        $stockI = (int)$stock;
        // Prepared statement: ? placeholders + bind_param (s = string, d = double, i = integer)
        $stmt = $conn->prepare('INSERT INTO products (name, category, price, stock, image) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('ssdis', $name, $category, $priceF, $stockI, $imageName);
        $stmt->execute();

        flash('success', 'Product added (ID ' . $stmt->insert_id . ').');
        redirect('products.php');
    } elseif (!empty($imageName)) {
        delete_image_file($imageName); // don't keep orphan uploads when other validation failed
    }
}

$pageTitle = 'Add Product';
require __DIR__ . '/includes/header.php';
?>
<div class="card narrow">
    <h1>Add product</h1>
    <?php foreach ($errors as $err): ?>
        <div class="alert error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label>Name <input type="text" name="name" value="<?= e($name) ?>" required></label>
        <label>Category <input type="text" name="category" value="<?= e($category) ?>" required></label>
        <label>Price (₹) <input type="number" step="0.01" min="0" name="price" value="<?= e($price) ?>" required></label>
        <label>Stock <input type="number" min="0" name="stock" value="<?= e($stock) ?>" required></label>
        <label>Photo <small>(JPG, PNG, GIF or WEBP, max 2 MB)</small>
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
        </label>
        <button type="submit" class="btn">Save product</button>
        <a href="products.php" class="btn secondary">Cancel</a>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
