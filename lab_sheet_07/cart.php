<?php
require 'config.php';
$pageTitle = 'Shopping cart';
$cart = $_SESSION['cart'];
$books = [];
$total = 0;
if ($cart) {
    $ids = array_map('intval', array_keys($cart));
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $s = $pdo->prepare("SELECT * FROM books WHERE id IN ($marks)");
    $s->execute($ids);
    $books = $s->fetchAll();
}
require 'includes/header.php';
if (!empty($_SESSION['flash'])) {
    echo '<div class="notice">' . e($_SESSION['flash']) . '</div>';
    unset($_SESSION['flash']);
}
?>
<h1>Your shopping cart</h1><?php if (!$books): ?>
    <div class="empty">Your cart is empty. <a href="index.php">Browse books</a>.</div><?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $b):
                    $qty = min((int) ($cart[$b['id']] ?? 1), (int) $b['stock']);
                    $subtotal = $qty * (float) $b['price'];
                    $total += $subtotal; ?>
                    <tr>
                        <td><?= e($b['title']) ?></td>
                        <td><?= money($b['price']) ?></td>
                        <td>
                            <form class="inline" method="post" action="cart_action.php"><input type="hidden" name="book_id"
                                    value="<?= $b['id'] ?>"><input type="hidden" name="action" value="update"><input type="number"
                                    name="quantity" min="1" max="<?= max(1, $b['stock']) ?>" value="<?= $qty ?>"><button
                                    class="button secondary">Update</button></form>
                        </td>
                        <td><?= money($subtotal) ?></td>
                        <td>
                            <form method="post" action="cart_action.php"><input type="hidden" name="book_id"
                                    value="<?= $b['id'] ?>"><input type="hidden" name="action" value="remove"><button
                                    class="button danger">Remove</button></form>
                        </td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="section-head">
        <h2>Total: <?= money($total) ?></h2><a class="button" href="checkout.php">Proceed to checkout</a>
    </div><?php endif; ?>
<?php require 'includes/footer.php'; ?>