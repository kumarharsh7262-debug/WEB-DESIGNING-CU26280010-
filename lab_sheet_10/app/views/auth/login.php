<h1>Login</h1>
<?php if (!empty($error)): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<form method="post" action="/login" class="form">
  <?= Csrf::field() ?>
  <label>Email <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <button class="btn">Login</button>
  <a href="/forgot-password">Forgot password?</a>
</form>
