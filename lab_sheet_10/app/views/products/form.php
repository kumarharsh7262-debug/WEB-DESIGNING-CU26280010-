<h1><?= e($title) ?></h1>
<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="form">
  <?= Csrf::field() ?>
  <?php
  $field = function (string $name, string $label, string $type = 'text', string $extra = '') use ($product, $errors) {
      $val = $product[$name] ?? '';
      echo '<label>' . e($label) . ' <input type="' . e($type) . '" name="' . e($name) . '" value="' . e($val) . '" ' . $extra . '></label>';
      if (isset($errors[$name])) echo '<small class="err">' . e($errors[$name]) . '</small>';
  };
  $field('name', 'Name', 'text', 'required maxlength="100"');
  $field('category', 'Category', 'text', 'required maxlength="50"');
  $field('price', 'Price', 'number', 'step="0.01" min="0" required');
  $field('stock', 'Stock', 'number', 'min="0" step="1"');
  $field('supplier_url', 'Supplier URL (optional)', 'url');
  ?>
  <label>Description <textarea name="description" maxlength="1000"><?= e($product['description'] ?? '') ?></textarea></label>
  <?php if (isset($errors['description'])): ?><small class="err"><?= e($errors['description']) ?></small><?php endif; ?>
  <label>Photo (JPG/PNG/GIF, max 2 MB) <input type="file" name="image" accept="image/jpeg,image/png,image/gif"></label>
  <?php if (isset($errors['image'])): ?><small class="err"><?= e($errors['image']) ?></small><?php endif; ?>
  <button class="btn">Save</button> <a href="/products">Cancel</a>
</form>
