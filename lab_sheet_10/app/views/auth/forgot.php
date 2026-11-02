<h1>Forgot password</h1>
<form method="post" action="/forgot-password" class="form">
  <?= Csrf::field() ?>
  <label>Your registered email <input type="email" name="email" required></label>
  <button class="btn">Send reset link</button>
</form>
