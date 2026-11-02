<?php
require '../config.php';
require_admin();
$summary = $pdo->query("SELECT COUNT(*) order_count,COALESCE(SUM(total),0) revenue FROM orders")->fetch();
$byMonth = $pdo->query("SELECT DATE_FORMAT(created_at,'%Y-%m') month,COUNT(*) orders_count,SUM(total) revenue FROM orders GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY month DESC")->fetchAll();
$pageTitle = 'Sales report';
require '../includes/header.php'; ?>
<h1>Sales summary</h1>
<div class="grid">
    <div class="panel">
        <p class="muted">Total orders</p>
        <h2><?= $summary['order_count'] ?></h2>
    </div>
    <div class="panel">
        <p class="muted">Total revenue from recorded orders</p>
        <h2><?= money($summary['revenue']) ?></h2>
    </div>
</div>
<div class="section-head">
    <h2>Monthly report</h2><a href="index.php">Back to dashboard</a>
</div>
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Orders</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody><?php foreach ($byMonth as $m): ?>
                <tr>
                    <td><?= e($m['month']) ?></td>
                    <td><?= $m['orders_count'] ?></td>
                    <td><?= money($m['revenue']) ?></td>
                </tr><?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require '../includes/footer.php'; ?>