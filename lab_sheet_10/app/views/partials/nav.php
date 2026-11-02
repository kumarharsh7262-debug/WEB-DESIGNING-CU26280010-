<nav class="nav">
  <a class="brand" href="/"><?= e(config('app.name')) ?></a>
  <div class="links">
    <a href="/">Home</a>
    <a href="/api-client">API client</a>
    <?php if (Session::get('user_id')): ?>
      <a href="/dashboard">Dashboard</a>
      <a href="/products">Products</a>
      <form method="post" action="/logout" class="inline">
        <?= Csrf::field() ?>
        <button class="link-btn">Logout (<?= e(Session::get('username')) ?>)</button>
      </form>
    <?php else: ?>
      <a href="/login">Login</a>
      <a href="/register">Register</a>
    <?php endif; ?>
  </div>
</nav>
