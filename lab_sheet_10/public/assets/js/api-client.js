// Q8: consume the token-protected REST API with fetch().
const $ = (id) => document.getElementById(id);
const msg = (t) => { $('msg').textContent = t || ''; };
const getToken = () => sessionStorage.getItem('api_token');

async function api(path, options = {}) {
  const res = await fetch(path, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(getToken() ? { Authorization: 'Bearer ' + getToken() } : {}),
    },
  });
  const body = res.status === 204 ? null : await res.json().catch(() => null);
  if (!res.ok) throw new Error((body && (body.error || JSON.stringify(body.fields))) || res.statusText);
  return body;
}

function showState() {
  const loggedIn = !!getToken();
  $('login-box').hidden = loggedIn;
  $('app').hidden = !loggedIn;
  if (loggedIn) loadProducts();
}

async function loadProducts() {
  try {
    const { data } = await api('/api/products');
    const tbody = $('table').querySelector('tbody');
    tbody.replaceChildren();
    data.forEach((p) => {
      const tr = document.createElement('tr');
      [p.id, p.name, p.category, Number(p.price).toFixed(2)].forEach((v) => {
        const td = document.createElement('td');
        td.textContent = v;                       // textContent => no XSS
        tr.appendChild(td);
      });
      const td = document.createElement('td');
      const del = document.createElement('button');
      del.textContent = 'Delete';
      del.className = 'link-btn danger';
      del.onclick = async () => {
        if (!confirm('Delete product #' + p.id + '?')) return;
        try { await api('/api/products/' + p.id, { method: 'DELETE' }); loadProducts(); }
        catch (e) { msg(e.message); }
      };
      td.appendChild(del);
      tr.appendChild(td);
      tbody.appendChild(tr);
    });
    msg('');
  } catch (e) {
    if (/Unauthorized/i.test(e.message)) { sessionStorage.removeItem('api_token'); showState(); }
    msg(e.message);
  }
}

$('login-btn').onclick = async () => {
  try {
    const out = await api('/api/login', {
      method: 'POST',
      body: JSON.stringify({ email: $('email').value, password: $('password').value }),
    });
    sessionStorage.setItem('api_token', out.token);
    msg('');
    showState();
  } catch (e) { msg(e.message); }
};

$('logout-btn').onclick = () => { sessionStorage.removeItem('api_token'); showState(); };

$('add-form').onsubmit = async (ev) => {
  ev.preventDefault();
  const f = new FormData(ev.target);
  try {
    await api('/api/products', {
      method: 'POST',
      body: JSON.stringify({ name: f.get('name'), category: f.get('category'), price: f.get('price'), stock: 0 }),
    });
    ev.target.reset();
    loadProducts();
  } catch (e) { msg(e.message); }
};

showState();
