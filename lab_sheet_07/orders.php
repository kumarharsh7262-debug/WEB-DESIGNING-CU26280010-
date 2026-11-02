<?php
require 'config.php';
require_login();
$s = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$s->execute([$_SESSION['user']['id']]);
$orders = $s->fetchAll();
$pageTitle = 'Order history';
require 'includes/header.php'; ?>
<h1>My order history</h1><?php if (!$orders): ?>
    <div class="empty">You have not placed any orders yet.</div><?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody><?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?= $o['id'] ?></td>
                        <td><?= e($o['created_at']) ?></td>
                        <td><?= money($o['total']) ?></td>
                        <td><?= e($o['status']) ?></td>
                        <td><a href="invoice.php?id=<?= $o['id'] ?>">View invoice</a></td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div><?php endif; ?>
<?php require 'includes/footer.php'; ?>