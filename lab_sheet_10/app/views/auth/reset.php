<h1>Choose a new password</h1>
<?php if (!empty($error)): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<form method="post" action="/reset-password" class="form">
  <?= Csrf::field() ?>
  <input type="hidden" name="token" value="<?= e($token) ?>">
  <label>New password <input type="password" name="password" minlength="8" required></label>
  <label>Confirm password <input type="password" name="confirm" required></label>
  <button class="btn">Update password</button>
</form>
