<h1>Hello, <?= e(Session::get('username')) ?></h1>
<p>You have <strong><?= (int)$productCount ?></strong> product(s) in the catalogue.</p>
<p><a class="btn" href="/products">Manage products</a> <a class="btn btn-alt" href="/api-client">Try the API client</a></p>
