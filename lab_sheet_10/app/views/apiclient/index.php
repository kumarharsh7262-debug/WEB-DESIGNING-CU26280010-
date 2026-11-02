<h1>API client (fetch)</h1>
<p>Logs in through <code>POST /api/login</code>, keeps the bearer token in <code>sessionStorage</code>, then calls the REST API.</p>

<section id="login-box" class="form">
  <label>Email <input id="email" type="email"></label>
  <label>Password <input id="password" type="password"></label>
  <button id="login-btn" class="btn">Get API token</button>
</section>

<section id="app" hidden>
  <p>Token active. <button id="logout-btn" class="link-btn">Forget token</button></p>
  <form id="add-form" class="inline-form">
    <input name="name" placeholder="Name" required>
    <input name="category" placeholder="Category" required>
    <input name="price" type="number" step="0.01" min="0" placeholder="Price" required>
    <button class="btn">Add</button>
  </form>
  <table id="table"><thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th></th></tr></thead><tbody></tbody></table>
</section>
<p id="msg" class="err"></p>
<script src="/assets/js/api-client.js"></script>
