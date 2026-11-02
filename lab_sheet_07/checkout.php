<?php
require 'config.php';
require_login();
$error = '';
$cart = $_SESSION['cart'];
$items = [];
$total = 0;
if (!$cart) {
    header('Location: cart.php');
    exit;
}
$ids = array_map('intval', array_keys($cart));
$marks = implode(',', array_fill(0, count($ids), '?'));
$s = $pdo->prepare("SELECT * FROM books WHERE id IN ($marks)");
$s->execute($ids);
$items = $s->fetchAll();
foreach ($items as $b) {
    $qty = (int) ($cart[$b['id']] ?? 0);
    if ($qty < 1 || $qty > $b['stock'])
        $error = 'Cart contains a quantity that is no longer available. Please update your cart.';
    $total += (float) $b['price'] * $qty;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $name = trim($_POST['shipping_name'] ?? '');
    $address = trim($_POST['shipping_address'] ?? '');
    $city = trim($_POST['shipping_city'] ?? '');
    $postal = trim($_POST['shipping_postal_code'] ?? '');
    if (!$name || !$address || !$city || !$postal) {
        $error = 'Complete all shipping fields.';
    } else {
        try {
            $pdo->beginTransaction();
            // Re-check and lock stock inside the transaction.
            $locked = $pdo->prepare("SELECT id,title,price,stock FROM books WHERE id=? FOR UPDATE");
            foreach ($cart as $bid => $qty) {
                $locked->execute([(int) $bid]);
                $b = $locked->fetch();
                if (!$b || (int) $qty < 1 || (int) $qty > (int) $b['stock'])
                    throw new RuntimeException('Stock changed. Please return to your cart and try again.');
            }
            $order = $pdo->prepare("INSERT INTO orders(user_id,shipping_name,shipping_address,shipping_city,shipping_postal_code,total) VALUES(?,?,?,?,?,?)");
            $order->execute([$_SESSION['user']['id'], $name, $address, $city, $postal, $total]);
            $orderId = (int) $pdo->lastInsertId();
            $get = $pdo->prepare("SELECT title,price,stock FROM books WHERE id=? FOR UPDATE");
            $oi = $pdo->prepare("INSERT INTO order_items(order_id,book_id,title_snapshot,quantity,unit_price) VALUES(?,?,?,?,?)");
            $upd = $pdo->prepare("UPDATE books SET stock=stock-? WHERE id=?");
            foreach ($cart as $bid => $qty) {
                $get->execute([(int) $bid]);
                $b = $get->fetch();
                $oi->execute([$orderId, (int) $bid, $b['title'], (int) $qty, $b['price']]);
                $upd->execute([(int) $qty, (int) $bid]);
            }
            $pdo->commit();
            $_SESSION['cart'] = [];
            header('Location: invoice.php?id=' . $orderId);
            exit;
        } catch (Throwable $ex) {
            if ($pdo->inTransaction())
                $pdo->rollBack();
            $error = $ex instanceof RuntimeException ? $ex->getMessage() : 'Unable to place order. Please try again.';
        }
    }
}
$pageTitle = 'Checkout';
require 'includes/header.php'; ?>
<form class="form-card" method="post" data-validate>
    <h1>Shipping details</h1><?php if ($error): ?>
        <div class="error"><?= e($error) ?></div><?php endif; ?>
    <p>Total: <strong><?= money($total) ?></strong></p><label>Full name</label><input name="shipping_name" required
        value="<?= e($_POST['shipping_name'] ?? $_SESSION['user']['name']) ?>"><label>Address</label><textarea
        name="shipping_address" required><?= e($_POST['shipping_address'] ?? '') ?></textarea>
    <div class="form-row">
        <div><label>City</label><input name="shipping_city" required value="<?= e($_POST['shipping_city'] ?? '') ?>"></div>
        <div><label>Postal code</label><input name="shipping_postal_code" required
                value="<?= e($_POST['shipping_postal_code'] ?? '') ?>"></div>
    </div><button class="button" <?= $error ? 'disabled' : '' ?>>Place order</button>
</form>
<?php require 'includes/footer.php'; ?>