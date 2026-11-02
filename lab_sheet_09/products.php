<?php
/**
 * Task 8  : Display products in a styled HTML table (SELECT)
 * Task 11 : Pagination (LIMIT / OFFSET, Previous/Next + numbered links)
 * Task 12 : Search & filter (LIKE, BETWEEN)
 * Task 13 : Column sorting (click headers)
 * Task 16 : All output escaped with e() = htmlspecialchars()
 * Task 20 : Live search suggestions (assets/js/live-search.js + suggest.php)
 */
require_once __DIR__ . '/includes/init.php';
require_login();

const PER_PAGE = 5;

// ---------- read & sanitise request parameters ----------
$q        = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$min      = trim($_GET['min'] ?? '');
$max      = trim($_GET['max'] ?? '');

// Task 13: only allow known column names for ORDER BY (identifiers can't be bound)
$sortable = ['id' => 'ID', 'name' => 'Name', 'category' => 'Category', 'price' => 'Price', 'stock' => 'Stock'];
$sort = array_key_exists($_GET['sort'] ?? '', $sortable) ? $_GET['sort'] : 'id';
$dir  = (strtolower($_GET['dir'] ?? '') === 'desc') ? 'desc' : 'asc';

$page = max(1, (int)($_GET['page'] ?? 1));

// ---------- Task 12: build the WHERE clause ----------
$where  = [];
$types  = '';
$params = [];

if ($q !== '') {
    $where[]  = 'name LIKE ?';
    $types   .= 's';
    $params[] = '%' . addcslashes($q, '%_\\') . '%';
}
if ($category !== '') {
    $where[]  = 'category = ?';
    $types   .= 's';
    $params[] = $category;
}
if (is_numeric($min) || is_numeric($max)) {
    $lo = is_numeric($min) ? (float)$min : 0;
    $hi = is_numeric($max) ? (float)$max : 99999999.99;
    if ($lo > $hi) { [$lo, $hi] = [$hi, $lo]; }
    $where[]  = 'price BETWEEN ? AND ?';
    $types   .= 'dd';
    $params[] = $lo;
    $params[] = $hi;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ---------- Task 11: count rows -> pages ----------
$total      = (int)db_query($conn, "SELECT COUNT(*) FROM products $whereSql", $types, $params)->get_result()->fetch_row()[0];
$totalPages = max(1, (int)ceil($total / PER_PAGE));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * PER_PAGE;

// ---------- fetch one page of rows ----------
$sql = "SELECT id, name, category, price, stock, image
        FROM products $whereSql
        ORDER BY $sort $dir
        LIMIT ? OFFSET ?";
$rows = db_query($conn, $sql, $types . 'ii', array_merge($params, [PER_PAGE, $offset]))
        ->get_result()->fetch_all(MYSQLI_ASSOC);

$categories = $conn->query('SELECT DISTINCT category FROM products ORDER BY category')->fetch_all(MYSQLI_NUM);

// ---------- helpers for links ----------
function qs(array $override = []): string
{
    $p = array_merge($_GET, $override);
    $p = array_filter($p, fn($v) => $v !== '' && $v !== null);
    return '?' . http_build_query($p);
}

function sort_link(string $col, string $label, string $curSort, string $curDir): string
{
    $newDir = ($curSort === $col && $curDir === 'asc') ? 'desc' : 'asc';
    $arrow  = $curSort === $col ? ($curDir === 'asc' ? ' ▲' : ' ▼') : '';
    return '<a href="' . e(qs(['sort' => $col, 'dir' => $newDir, 'page' => 1])) . '">' . e($label) . $arrow . '</a>';
}

$pageTitle = 'Products';
require __DIR__ . '/includes/header.php';
?>
<div class="toolbar">
    <h1>Products <small class="muted">(<?= $total ?> found)</small></h1>
    <a class="btn" href="product_add.php">+ Add product</a>
</div>

<form method="get" class="card filters">
    <div class="live-wrap">
        <input type="text" name="q" id="search-box" placeholder="Search by name…" value="<?= e($q) ?>" autocomplete="off">
        <ul id="suggestions" class="suggestions" hidden></ul>
    </div>
    <select name="category">
        <option value="">All categories</option>
        <?php foreach ($categories as [$c]): ?>
            <option value="<?= e($c) ?>" <?= $c === $category ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" step="0.01" min="0" name="min" placeholder="Min ₹" value="<?= e($min) ?>">
    <input type="number" step="0.01" min="0" name="max" placeholder="Max ₹" value="<?= e($max) ?>">
    <input type="hidden" name="sort" value="<?= e($sort) ?>">
    <input type="hidden" name="dir" value="<?= e($dir) ?>">
    <button type="submit" class="btn">Search</button>
    <a href="products.php" class="btn secondary">Reset</a>
</form>

<div class="card">
<table>
    <thead>
        <tr>
            <th>Image</th>
            <?php foreach ($sortable as $col => $label): ?>
                <th><?= sort_link($col, $label, $sort, $dir) ?></th>
            <?php endforeach; ?>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!$rows): ?>
        <tr><td colspan="7" class="muted center">No products match your search.</td></tr>
    <?php endif; ?>
    <?php foreach ($rows as $p): ?>
        <tr>
            <td>
                <?php if ($p['image']): ?>
                    <img class="thumb" src="uploads/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
                <?php else: ?>
                    <span class="muted">—</span>
                <?php endif; ?>
            </td>
            <td><?= (int)$p['id'] ?></td>
            <td><?= e($p['name']) ?></td>
            <td><?= e($p['category']) ?></td>
            <td>₹<?= number_format((float)$p['price'], 2) ?></td>
            <td><?= (int)$p['stock'] ?></td>
            <td class="actions">
                <form method="post" action="cart.php" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                    <button class="btn small" <?= $p['stock'] < 1 ? 'disabled' : '' ?>>Add to cart</button>
                </form>
                <a class="btn small secondary" href="product_edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
                <!-- Task 10: JavaScript confirmation before deleting -->
                <form method="post" action="product_delete.php" class="inline"
                      onsubmit="return confirm('Delete &quot;<?= e(addslashes($p['name'])) ?>&quot;? This cannot be undone.');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                    <button class="btn small danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php if ($totalPages > 1): ?>
<nav class="pagination" aria-label="Pagination">
    <?php if ($page > 1): ?>
        <a href="<?= e(qs(['page' => $page - 1])) ?>">&laquo; Previous</a>
    <?php else: ?>
        <span class="disabled">&laquo; Previous</span>
    <?php endif; ?>

    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
        <?php if ($i === $page): ?>
            <span class="current"><?= $i ?></span>
        <?php else: ?>
            <a href="<?= e(qs(['page' => $i])) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="<?= e(qs(['page' => $page + 1])) ?>">Next &raquo;</a>
    <?php else: ?>
        <span class="disabled">Next &raquo;</span>
    <?php endif; ?>
</nav>
<p class="muted center">Page <?= $page ?> of <?= $totalPages ?></p>
<?php endif; ?>
</div>

<script src="assets/js/live-search.js"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
