<h1>Register</h1>
<form method="post" action="/register" class="form">
  <?= Csrf::field() ?>
  <label>Username <input name="username" value="<?= e($old['username'] ?? '') ?>" required></label>
  <?php if (isset($errors['username'])): ?><small class="err"><?= e($errors['username']) ?></small><?php endif; ?>
  <label>Email <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required></label>
  <?php if (isset($errors['email'])): ?><small class="err"><?= e($errors['email']) ?></small><?php endif; ?>
  <label>Password <input type="password" name="password" minlength="8" required></label>
  <?php if (isset($errors['password'])): ?><small class="err"><?= e($errors['password']) ?></small><?php endif; ?>
  <label>Confirm password <input type="password" name="confirm" required></label>
  <?php if (isset($errors['confirm'])): ?><small class="err"><?= e($errors['confirm']) ?></small><?php endif; ?>
  <button class="btn">Register</button>
</form>
