<?php
/** Task 9: Edit form pre-filled with existing data, saved with an UPDATE query */
require_once __DIR__ . '/includes/init.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$product = db_query($conn, 'SELECT * FROM products WHERE id = ?', 'i', [$id])->get_result()->fetch_assoc();
if (!$product) {
    flash('error', 'Product not found.');
    redirect('products.php');
}

$errors = [];
$name     = $product['name'];
$category = $product['category'];
$price    = $product['price'];
$stock    = $product['stock'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $stock    = trim($_POST['stock'] ?? '');

    if ($name === '' || mb_strlen($name) > 150)       $errors[] = 'Name is required (max 150 characters).';
    if ($category === '' || mb_strlen($category) > 80) $errors[] = 'Category is required (max 80 characters).';
    if (!is_numeric($price) || $price < 0)            $errors[] = 'Price must be a number, 0 or greater.';
    if (!ctype_digit($stock))                         $errors[] = 'Stock must be a whole number, 0 or greater.';

    $newImage = handle_image_upload($_FILES['image'] ?? [], $uploadError);
    if ($uploadError) {
        $errors[] = $uploadError;
    }

    if (!$errors) {
        $imageName = $newImage ?? $product['image']; // keep the old image unless a new one was uploaded
        $priceF = (float)$price;
        $stockI = (int)$stock;

        $stmt = $conn->prepare('UPDATE products SET name = ?, category = ?, price = ?, stock = ?, image = ? WHERE id = ?');
        $stmt->bind_param('ssdisi', $name, $category, $priceF, $stockI, $imageName, $id);
        $stmt->execute();

        if ($newImage && $product['image']) {
            delete_image_file($product['image']); // remove the replaced file
        }
        flash('success', 'Product updated.');
        redirect('products.php');
    } elseif (!empty($newImage)) {
        delete_image_file($newImage);
    }
}

$pageTitle = 'Edit Product';
require __DIR__ . '/includes/header.php';
?>
<div class="card narrow">
    <h1>Edit product #<?= (int)$id ?></h1>
    <?php foreach ($errors as $err): ?>
        <div class="alert error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)$id ?>">
        <label>Name <input type="text" name="name" value="<?= e($name) ?>" required></label>
        <label>Category <input type="text" name="category" value="<?= e($category) ?>" required></label>
        <label>Price (₹) <input type="number" step="0.01" min="0" name="price" value="<?= e($price) ?>" required></label>
        <label>Stock <input type="number" min="0" name="stock" value="<?= e($stock) ?>" required></label>

        <?php if ($product['image']): ?>
            <p>Current photo:<br><img class="preview" src="uploads/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>"></p>
        <?php endif; ?>
        <label>Replace photo <small>(optional)</small>
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
        </label>

        <button type="submit" class="btn">Update product</button>
        <a href="products.php" class="btn secondary">Cancel</a>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
