<?php
require 'config.php';
require_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$s = $pdo->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$s->execute([$id ?: 0, $_SESSION['user']['id']]);
$order = $s->fetch();
if (!$order) {
    http_response_code(404);
    exit('Invoice not found.');
}
$i = $pdo->prepare("SELECT * FROM order_items WHERE order_id=?");
$i->execute([$order['id']]);
$items = $i->fetchAll();
$pageTitle = 'Invoice #' . $order['id'];
require 'includes/header.php'; ?>
<div class="panel">
    <h1>Order invoice #<?= $order['id'] ?></h1>
    <p><strong>Order date:</strong> <?= e($order['created_at']) ?></p>
    <p><strong>Status:</strong> <?= e($order['status']) ?></p>
    <h2>Ship to</h2>
    <p><?= e($order['shipping_name']) ?><br><?= nl2br(e($order['shipping_address'])) ?><br><?= e($order['shipping_city']) ?>,
        <?= e($order['shipping_postal_code']) ?></p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Qty</th>
                    <th>Unit price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody><?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['title_snapshot']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= money($item['unit_price']) ?></td>
                        <td><?= money($item['unit_price'] * $item['quantity']) ?></td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <h2>Total: <?= money($order['total']) ?></h2><button class="button secondary" onclick="window.print()">Print
        invoice</button>
</div>
<?php require 'includes/footer.php'; ?>