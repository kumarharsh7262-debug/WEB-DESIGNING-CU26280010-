<?php
/**
 * Task 16: Preventing Cross-Site Scripting (XSS) with htmlspecialchars().
 * Every page in this project prints user data through e(), which wraps
 * htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').
 */
require_once __DIR__ . '/includes/init.php';

$input = $_GET['comment'] ?? '';

$pageTitle = 'XSS Demo';
require __DIR__ . '/includes/header.php';
?>
<h1>XSS prevention demo</h1>
<p>Type something containing HTML, for example:
   <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code> or
   <code>&lt;b onmouseover="alert(1)"&gt;hi&lt;/b&gt;</code></p>

<form method="get" class="card filters">
    <input type="text" name="comment" value="<?= e($input) ?>" placeholder="Enter a comment…" style="flex:1">
    <button class="btn">Submit</button>
</form>

<?php if ($input !== ''): ?>
<div class="two-col">
    <div class="card">
        <h2 class="good">✅ Escaped output (what this site does)</h2>
        <p>Displayed in the page as harmless text:</p>
        <div class="box"><?= e($input) ?></div>
    </div>
    <div class="card">
        <h2>🔍 What the browser actually receives</h2>
        <p>The HTML source after <code>htmlspecialchars()</code>:</p>
        <pre><?= e(e($input)) ?></pre>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <h2>Characters that get converted</h2>
    <table>
        <thead><tr><th>Character</th><th>Becomes</th></tr></thead>
        <tbody>
            <tr><td><code>&amp;lt;</code> as typed: &lt;</td><td><code>&amp;lt;</code></td></tr>
            <tr><td>&gt;</td><td><code>&amp;gt;</code></td></tr>
            <tr><td>&amp;</td><td><code>&amp;amp;</code></td></tr>
            <tr><td>"</td><td><code>&amp;quot;</code></td></tr>
            <tr><td>'</td><td><code>&amp;#039;</code></td></tr>
        </tbody>
    </table>
    <p class="muted">Because tag and quote characters are converted, user input can never open a tag or break out of an attribute value, so injected scripts are shown as text instead of running.
       Always escape at output time, and use <code>ENT_QUOTES</code> so single quotes in attributes are covered.</p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
