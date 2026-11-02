<?php
/** Task 5: Protected dashboard (session check -> redirect)
 *  Task 17: personalised welcome message */
require_once __DIR__ . '/includes/init.php';
require_login();

$uid = (int)$_SESSION['user_id'];

$totalProducts = $conn->query('SELECT COUNT(*) FROM products')->fetch_row()[0];

$stmt = db_query($conn, 'SELECT COUNT(*), COALESCE(SUM(total),0) FROM orders WHERE user_id = ?', 'i', [$uid]);
[$myOrders, $mySpent] = $stmt->get_result()->fetch_row();

$stmt = db_query($conn, 'SELECT id, total, status, created_at FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 5', 'i', [$uid]);
$recent = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$remembered = !empty($_COOKIE['remember']);

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<h1>Welcome back, <?= e($_SESSION['user_name']) ?>! 👋</h1>
<?php if ($remembered): ?>
    <p class="muted">You are signed in with "Remember Me" on this device.</p>
<?php endif; ?>

<div class="stats">
    <div class="stat"><span><?= (int)$totalProducts ?></span>Products</div>
    <div class="stat"><span><?= (int)$myOrders ?></span>My orders</div>
    <div class="stat"><span>₹<?= number_format((float)$mySpent, 2) ?></span>Total spent</div>
    <div class="stat"><span><?= cart_count() ?></span>Items in cart</div>
</div>

<div class="card">
    <h2>Recent orders</h2>
    <?php if (!$recent): ?>
        <p class="muted">No orders yet. <a href="products.php">Browse products</a>.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Order #</th><th>Total</th><th>Status</th><th>Placed on</th></tr></thead>
            <tbody>
            <?php foreach ($recent as $o): ?>
                <tr>
                    <td><?= (int)$o['id'] ?></td>
                    <td>₹<?= number_format((float)$o['total'], 2) ?></td>
                    <td><?= e($o['status']) ?></td>
                    <td><?= e($o['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
