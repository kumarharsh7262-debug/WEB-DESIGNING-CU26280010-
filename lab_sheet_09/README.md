# MCA108P – Lab Sheet 9: PHP & MySQL Web Application

One project folder that covers all 20 tasks of the lab sheet.

## Setup (XAMPP / WAMP / any PHP 8+ with MySQL)

1. Copy the `webapp_lab9` folder into `htdocs` (XAMPP) or `www` (WAMP).
2. Start Apache and MySQL.
3. Import the database: open phpMyAdmin → **Import** → choose `database.sql`
   (or run `mysql -u root < database.sql`).
4. Check the credentials in `config/db.php` (default: user `root`, empty password).
5. Open <http://localhost/webapp_lab9/> → **Register**, then log in.

Requirements: PHP 8.0+ with `mysqli`, `fileinfo` and `mysqlnd` enabled (all on by default in XAMPP).
The `uploads/` folder must be writable by the web server.

## Task → file map

| # | Task | File(s) |
|---|------|---------|
| 1 | Database `webapp_db` (users, products, orders + order_items) with PK/FK | `database.sql` |
| 2 | mysqli connection, error handling, `require_once` on every page | `config/db.php`, `includes/init.php` |
| 3 | Registration, server-side validation, `password_hash()` | `register.php` |
| 4 | Login, `password_verify()`, `session_start()` | `login.php`, `includes/functions.php` |
| 5 | Protected dashboard (redirects guests) | `dashboard.php`, `require_login()` |
| 6 | Secure logout (clear vars, delete cookie, destroy session) | `logout.php` |
| 7 | Insert product with prepared statement | `product_add.php` |
| 8 | Product table (SELECT) | `products.php` |
| 9 | Edit form pre-filled + UPDATE | `product_edit.php` |
| 10 | Delete with JS `confirm()` + secure PHP handler | `products.php`, `product_delete.php` |
| 11 | Pagination (LIMIT/OFFSET, Prev/Next, numbers) | `products.php` |
| 12 | Search/filter (LIKE, BETWEEN) | `products.php` |
| 13 | Sort by clicking column headers | `products.php` |
| 14 | Image upload (type, size, unique name) | `handle_image_upload()` in `includes/functions.php`, used by `product_add.php` / `product_edit.php` |
| 15 | SQL injection: vulnerable vs prepared | `sqli_demo.php` |
| 16 | XSS prevention with `htmlspecialchars()` | `e()` helper used everywhere, `xss_demo.php` |
| 17 | "Remember Me" cookie + welcome message | `login.php`, `includes/functions.php`, `dashboard.php` |
| 18 | Session shopping cart (add / update / remove) | `cart.php` |
| 19 | Checkout with transaction (BEGIN/COMMIT/ROLLBACK) | `checkout.php` |
| 20 | `fetch()` live search suggestions | `assets/js/live-search.js`, `suggest.php` |

## Security notes

- Every query that touches user input uses prepared statements (the only deliberately unsafe query is in `sqli_demo.php`, for demonstration).
- All state-changing requests are POST and protected by a CSRF token.
- Passwords are stored only as `password_hash()` hashes; "Remember Me" stores a random token whose SHA-256 hash is kept in the database.
- Uploads are checked by real MIME type, size (2 MB) and given random names; `uploads/.htaccess` blocks script execution.
- `sqli_demo.php` runs a vulnerable query against the products table on purpose. Delete that file before deploying anywhere public.
