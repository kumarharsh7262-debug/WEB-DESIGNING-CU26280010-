<?php
/** Task 18: Session-based shopping cart (add, update quantities, remove) */
require_once __DIR__ . '/includes/init.php';
require_login();

$_SESSION['cart'] = $_SESSION['cart'] ?? []; // [product_id => quantity]

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $pid = (int)($_POST['product_id'] ?? 0);
            $row = db_query($conn, 'SELECT name, stock FROM products WHERE id = ?', 'i', [$pid])->get_result()->fetch_assoc();
            if (!$row) {
                flash('error', 'Product not found.');
            } else {
                $newQty = ($_SESSION['cart'][$pid] ?? 0) + 1;
                if ($newQty > $row['stock']) {
                    flash('error', 'Only ' . (int)$row['stock'] . ' of "' . $row['name'] . '" in stock.');
                } else {
                    $_SESSION['cart'][$pid] = $newQty;
                    flash('success', '"' . $row['name'] . '" added to cart.');
                }
            }
            break;

        case 'update':
            foreach ((array)($_POST['qty'] ?? []) as $pid => $qty) {
                $pid = (int)$pid;
                $qty = (int)$qty;
                if (!isset($_SESSION['cart'][$pid])) continue;
                if ($qty <= 0) {
                    unset($_SESSION['cart'][$pid]);
                    continue;
                }
                $stock = (int)db_query($conn, 'SELECT stock FROM products WHERE id = ?', 'i', [$pid])->get_result()->fetch_row()[0];
                $_SESSION['cart'][$pid] = min($qty, $stock);
            }
            flash('success', 'Cart updated.');
            break;

        case 'remove':
            unset($_SESSION['cart'][(int)($_POST['product_id'] ?? 0)]);
            flash('success', 'Item removed.');
            break;

        case 'clear':
            $_SESSION['cart'] = [];
            flash('success', 'Cart cleared.');
            break;
    }
    redirect('cart.php');
}

// ---- load cart lines from the database ----
$items = [];
$grand = 0.0;
if ($_SESSION['cart']) {
    $ids = array_keys($_SESSION['cart']);
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $res = db_query($conn, "SELECT id, name, price, stock, image FROM products WHERE id IN ($in)", str_repeat('i', count($ids)), $ids)
           ->get_result();
    foreach ($res as $p) {
        $p['qty']      = $_SESSION['cart'][$p['id']];
        $p['subtotal'] = $p['qty'] * $p['price'];
        $grand        += $p['subtotal'];
        $items[]       = $p;
    }
    // drop ids whose product no longer exists
    $_SESSION['cart'] = array_intersect_key($_SESSION['cart'], array_column($items, 'id', 'id'));
}

$pageTitle = 'Shopping Cart';
require __DIR__ . '/includes/header.php';
?>
<h1>Shopping cart</h1>

<?php if (!$items): ?>
    <div class="card"><p class="muted">Your cart is empty. <a href="products.php">Browse products</a>.</p></div>
<?php else: ?>
<form method="post" class="card">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="update">
    <table>
        <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?= e($it['name']) ?></td>
                <td>₹<?= number_format((float)$it['price'], 2) ?></td>
                <td>
                    <input type="number" class="qty" name="qty[<?= (int)$it['id'] ?>]"
                           value="<?= (int)$it['qty'] ?>" min="0" max="<?= (int)$it['stock'] ?>">
                </td>
                <td>₹<?= number_format((float)$it['subtotal'], 2) ?></td>
                <td>
                    <button type="submit" class="btn small danger"
                            formaction="cart.php" name="action" value="remove"
                            formnovalidate
                            onclick="document.getElementById('rm').value=<?= (int)$it['id'] ?>">Remove</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><th colspan="3" class="right">Total</th><th colspan="2">₹<?= number_format($grand, 2) ?></th></tr>
        </tfoot>
    </table>
    <input type="hidden" name="product_id" id="rm" value="0">
    <p>
        <button type="submit" class="btn">Update quantities</button>
        <a href="checkout.php" class="btn success">Proceed to checkout</a>
    </p>
</form>

<form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="clear">
    <button class="btn secondary" onclick="return confirm('Empty the whole cart?');">Clear cart</button>
</form>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
