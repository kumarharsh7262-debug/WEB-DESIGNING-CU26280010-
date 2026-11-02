<?php
/** Task 19: Checkout - save the cart into orders using a transaction (BEGIN / COMMIT / ROLLBACK) */
require_once __DIR__ . '/includes/init.php';
require_login();

$cart = $_SESSION['cart'] ?? [];
if (!$cart && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('error', 'Your cart is empty.');
    redirect('cart.php');
}

$orderId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    if (!$cart) {
        flash('error', 'Your cart is empty.');
        redirect('cart.php');
    }

    $userId = (int)$_SESSION['user_id'];

    try {
        $conn->begin_transaction();                         // BEGIN

        // Create the order first (total is filled in after prices are read from the DB)
        $zero = 0.0;
        $stmt = $conn->prepare('INSERT INTO orders (user_id, total) VALUES (?, ?)');
        $stmt->bind_param('id', $userId, $zero);
        $stmt->execute();
        $orderId = $stmt->insert_id;

        $total   = 0.0;
        $lookup  = $conn->prepare('SELECT name, price, stock FROM products WHERE id = ? FOR UPDATE'); // lock the row
        $addItem = $conn->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
        $reduce  = $conn->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');

        foreach ($cart as $pid => $qty) {
            $pid = (int)$pid;
            $qty = (int)$qty;

            $lookup->bind_param('i', $pid);
            $lookup->execute();
            $prod = $lookup->get_result()->fetch_assoc();

            if (!$prod) {
                throw new RuntimeException('A product in your cart no longer exists.');
            }
            if ($qty < 1 || $qty > $prod['stock']) {
                throw new RuntimeException('Not enough stock for "' . $prod['name'] . '" (available: ' . $prod['stock'] . ').');
            }

            $price = (float)$prod['price'];                 // trust the DB price, never the browser
            $total += $price * $qty;

            $addItem->bind_param('iiid', $orderId, $pid, $qty, $price);
            $addItem->execute();

            $reduce->bind_param('ii', $qty, $pid);
            $reduce->execute();
        }

        $upd = $conn->prepare('UPDATE orders SET total = ? WHERE id = ?');
        $upd->bind_param('di', $total, $orderId);
        $upd->execute();

        $conn->commit();                                    // COMMIT
        $_SESSION['cart'] = [];
        flash('success', 'Order #' . $orderId . ' placed successfully. Total ₹' . number_format($total, 2));
        redirect('dashboard.php');

    } catch (Throwable $ex) {
        $conn->rollback();                                  // ROLLBACK: nothing is saved if any step fails
        $msg = $ex instanceof RuntimeException ? $ex->getMessage() : 'Checkout failed. Please try again.';
        if (!($ex instanceof RuntimeException)) {
            error_log('Checkout error: ' . $ex->getMessage());
        }
        flash('error', $msg);
        redirect('cart.php');
    }
}

// ---- order summary (GET) ----
$ids = array_keys($cart);
$in  = implode(',', array_fill(0, count($ids), '?'));
$rows = db_query($conn, "SELECT id, name, price FROM products WHERE id IN ($in)", str_repeat('i', count($ids)), $ids)
        ->get_result()->fetch_all(MYSQLI_ASSOC);
$grand = 0.0;

$pageTitle = 'Checkout';
require __DIR__ . '/includes/header.php';
?>
<div class="card narrow">
    <h1>Checkout</h1>
    <table>
        <thead><tr><th>Product</th><th>Qty</th><th>Subtotal</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r):
            $qty = $cart[$r['id']];
            $sub = $qty * $r['price'];
            $grand += $sub; ?>
            <tr>
                <td><?= e($r['name']) ?></td>
                <td><?= (int)$qty ?></td>
                <td>₹<?= number_format($sub, 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><th colspan="2" class="right">Total</th><th>₹<?= number_format($grand, 2) ?></th></tr></tfoot>
    </table>

    <form method="post">
        <?= csrf_field() ?>
        <button type="submit" class="btn success">Place order</button>
        <a href="cart.php" class="btn secondary">Back to cart</a>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
