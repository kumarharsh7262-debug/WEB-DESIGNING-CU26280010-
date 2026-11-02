<?php
require '../config.php';
require_admin();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';
    if ($id && in_array($status, ['Pending', 'Shipped', 'Delivered'], true)) {
        $s = $pdo->prepare("UPDATE orders SET status=? WHERE id=?");
        $s->execute([$status, $id]);
        $message = 'Order status updated.';
    }
}
$orders = $pdo->query("SELECT o.*,u.name customer_name,u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC")->fetchAll();
$pageTitle = 'Manage orders';
require '../includes/header.php'; ?>
<h1>Customer orders</h1><?php if ($message): ?>
    <div class="notice"><?= e($message) ?></div><?php endif; ?>
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody><?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td><?= e($o['customer_name']) ?><br><small><?= e($o['email']) ?></small></td>
                    <td><?= e($o['created_at']) ?></td>
                    <td><?= money($o['total']) ?></td>
                    <td><?= e($o['status']) ?></td>
                    <td>
                        <form method="post" class="inline"><input type="hidden" name="id" value="<?= $o['id'] ?>"><select
                                name="status"><?php foreach (['Pending', 'Shipped', 'Delivered'] as $st): ?>
                                    <option <?= $st === $o['status'] ? 'selected' : '' ?>><?= $st ?></option><?php endforeach; ?>
                            </select><button class="button">Save</button></form>
                    </td>
                </tr><?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require '../includes/footer.php'; ?>