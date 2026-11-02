<h1>MVC PHP Project</h1>
<p>Front controller &rarr; router &rarr; controller &rarr; model &rarr; view.</p>
<?php if (!Session::get('user_id')): ?>
  <p><a class="btn" href="/register">Create account</a> <a class="btn btn-alt" href="/login">Login</a></p>
<?php else: ?>
  <p><a class="btn" href="/dashboard">Go to dashboard</a></p>
<?php endif; ?>
