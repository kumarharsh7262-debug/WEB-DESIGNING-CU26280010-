<?php
/**
 * Task 15: SQL injection - vulnerable query vs prepared statement.
 *
 * EDUCATIONAL ONLY. The vulnerable query below is deliberately unsafe and is
 * run against the harmless `products` table just to show the difference.
 * Never build real queries by concatenating user input.
 *
 * Try these inputs in the box:
 *     Mouse                 (normal search - both versions behave the same)
 *     ' OR '1'='1           (injection - vulnerable version returns EVERY row)
 */
require_once __DIR__ . '/includes/init.php';

$input = $_GET['name'] ?? '';
$vulnSql = $vulnRows = $vulnErr = $safeRows = null;

if ($input !== '') {
    // ---------------- VULNERABLE: string concatenation ----------------
    $vulnSql = "SELECT id, name, category, price FROM products WHERE name = '" . $input . "'";
    try {
        $vulnRows = $conn->query($vulnSql)->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception $ex) {
        $vulnErr = 'SQL error: ' . $ex->getMessage(); // shows how attackers probe the query
    }

    // ---------------- SAFE: prepared statement ----------------
    $stmt = $conn->prepare('SELECT id, name, category, price FROM products WHERE name = ?');
    $stmt->bind_param('s', $input);   // input is sent as DATA, never parsed as SQL
    $stmt->execute();
    $safeRows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function render_rows(?array $rows): void
{
    if (!$rows) { echo '<p class="muted">0 rows returned.</p>'; return; }
    echo '<table><thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th></tr></thead><tbody>';
    foreach ($rows as $r) {
        echo '<tr><td>' . (int)$r['id'] . '</td><td>' . e($r['name']) . '</td><td>' . e($r['category'])
           . '</td><td>₹' . number_format((float)$r['price'], 2) . '</td></tr>';
    }
    echo '</tbody></table><p><strong>' . count($rows) . ' row(s) returned.</strong></p>';
}

$pageTitle = 'SQL Injection Demo';
require __DIR__ . '/includes/header.php';
?>
<h1>SQL injection demo</h1>
<p>Search for an exact product name. Try <code>Wireless Mouse</code>, then try the attack string
   <code>' OR '1'='1</code>.</p>

<form method="get" class="card filters">
    <input type="text" name="name" value="<?= e($input) ?>" placeholder="Product name…" style="flex:1">
    <button class="btn">Run both queries</button>
</form>

<?php if ($input !== ''): ?>
<div class="two-col">
    <div class="card">
        <h2 class="bad">❌ Vulnerable (concatenation)</h2>
        <pre><?= e($vulnSql) ?></pre>
        <?php if ($vulnErr): ?>
            <div class="alert error"><?= e($vulnErr) ?></div>
        <?php else: render_rows($vulnRows); endif; ?>
    </div>
    <div class="card">
        <h2 class="good">✅ Safe (prepared statement)</h2>
        <pre>SELECT id, name, category, price
FROM products WHERE name = ?
-- bound value: <?= e($input) ?></pre>
        <?php render_rows($safeRows); ?>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <h2>Why it works</h2>
    <ul>
        <li>With concatenation, the quote in the input closes the string and <code>OR '1'='1'</code> becomes part of the SQL, so the <code>WHERE</code> is always true.</li>
        <li>A prepared statement sends the SQL and the data separately, so the whole input is treated as one literal value and matches nothing.</li>
        <li>Every query in the rest of this project uses prepared statements.</li>
    </ul>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
