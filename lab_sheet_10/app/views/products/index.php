<h1>Products</h1>
<form method="get" action="/products" class="inline-form">
  <input name="q" placeholder="Search name or category" value="<?= e($q) ?>">
  <button class="btn">Search</button>
  <a class="btn btn-alt" href="/products/create">+ Add product</a>
</form>

<table>
  <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach ($products as $p): ?>
    <tr>
      <td><?php if ($p['image']): ?><img src="/uploads/<?= e($p['image']) ?>" width="48" alt=""><?php endif; ?></td>
      <td><?= e($p['name']) ?></td>
      <td><?= e($p['category']) ?></td>
      <td><?= e(number_format((float)$p['price'], 2)) ?></td>
      <td><?= (int)$p['stock'] ?></td>
      <td>
        <a href="/products/<?= (int)$p['id'] ?>/edit">Edit</a>
        <form method="post" action="/products/<?= (int)$p['id'] ?>/delete" class="inline"
              onsubmit="return confirm('Delete this product?');">
          <?= Csrf::field() ?>
          <button class="link-btn danger">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$products): ?><tr><td colspan="6">No products found.</td></tr><?php endif; ?>
  </tbody>
</table>

<div class="pager">
  <?php for ($i = 1; $i <= $pages; $i++): ?>
    <a class="<?= $i === $page ? 'cur' : '' ?>" href="/products?<?= e(http_build_query(['q' => $q, 'page' => $i])) ?>"><?= $i ?></a>
  <?php endfor; ?>
</div>
