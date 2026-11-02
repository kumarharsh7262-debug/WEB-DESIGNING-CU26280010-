# MyProject – MCA108P Lab Sheet 10

An MVC-style PHP application (no framework) covering secure web development, a REST API,
and server configuration.

## Quick start

```bash
composer install                                   # installs PHPMailer (Q14)
mysql -u root -p < database/schema.sql             # create DB + tables
mysql -u root -p < database/mysql_user.sql         # Q20: limited-privilege DB user
# configure Apache vhost + SSL + hosts file -> see docs/SETUP_GUIDE.md
```

Open **https://myproject.local** (accept the self-signed certificate warning).
First register an account, then log in.

Secrets are read from environment variables (see `config/config.php`):
`DB_HOST DB_NAME DB_USER DB_PASS SMTP_HOST SMTP_PORT SMTP_USER SMTP_PASS SMTP_FROM APP_URL`.

## Folder layout

```
myproject/
├── app/
│   ├── controllers/   Auth, Product, Api, Upload, Home, Error
│   ├── core/          Router, Controller, Model, Database (singleton), Session, Csrf,
│   │                  Validator, Mailer, Uploader, helpers, bootstrap
│   ├── models/        User, Product
│   └── views/         partials (header/nav/footer), auth, products, home, apiclient, errors
├── config/            config.php, routes.php
├── database/          schema.sql, mysql_user.sql
├── public/            index.php (front controller), .htaccess, assets/, 500.html   <- web root
├── storage/           uploads/ (outside web root!), logs/
├── apache/            myproject.local.conf (virtual host + SSL)
├── scripts/           git_workflow.sh, make_self_signed_cert.sh
└── docs/              SETUP_GUIDE.md, CHANGELOG.md
```

## Where each question is answered

| # | Topic | Location |
|---|-------|----------|
| 1 | Git init, `.gitignore`, logical commits | `.gitignore`, `scripts/git_workflow.sh`, `docs/SETUP_GUIDE.md` §1 |
| 2 | Branch, merge conflict, push to GitHub | `scripts/git_workflow.sh`, `docs/SETUP_GUIDE.md` §1 |
| 3 | MVC folder layout | whole project (`app/models`, `app/views`, `app/controllers`, `public`, `config`) |
| 4 | Front controller + router | `public/index.php`, `app/core/Router.php`, `config/routes.php` |
| 5 | OOP `Database` (PDO singleton) + model CRUD | `app/core/Database.php`, `app/core/Model.php`, `app/models/*` |
| 6 | Header/footer/nav templates, pass data to views | `app/views/partials/*`, `Controller::render()` |
| 7 | RESTful JSON API (GET/POST/PUT/DELETE) | `app/controllers/ApiController.php` |
| 8 | Token auth + `fetch()` front end | `ApiController::authenticate()`, `public/assets/js/api-client.js`, `/api-client` |
| 9 | CSRF tokens in all forms | `app/core/Csrf.php`, `Controller::verifyCsrf()` |
| 10 | Secure session flags + ID regeneration | `app/core/Session.php`, `AuthController::login()` |
| 11 | `filter_var()` validation/sanitising | `app/core/Validator.php`, `Product::validate()` |
| 12 | Login attempt limiter / lock-out | `User::recordFailure()`, `AuthController::login()` |
| 13 | Forgot-password with time-limited token | `AuthController::forgot()/reset()`, `User::createResetToken()` |
| 14 | PHPMailer (SMTP) e-mails | `app/core/Mailer.php`, `composer.json` |
| 15 | Secure upload outside web root | `app/core/Uploader.php`, `UploadController` |
| 16 | 404/500 pages + error logging | `app/views/errors/*`, `bootstrap.php`, `public/500.html` |
| 17 | `.htaccess` (clean URLs, HTTPS, no listing) | `public/.htaccess` |
| 18 | SSL/TLS self-signed on Apache | `scripts/make_self_signed_cert.sh`, `apache/myproject.local.conf` |
| 19 | Virtual host + hosts file | `apache/myproject.local.conf`, `docs/SETUP_GUIDE.md` §4 |
| 20 | Limited-privilege MySQL user | `database/mysql_user.sql` |

## REST API cheat-sheet

```bash
# 1. get a token
curl -k -X POST https://myproject.local/api/login -H "Content-Type: application/json" \
     -d '{"email":"me@example.com","password":"secret123"}'

TOKEN=...paste token...
# 2. CRUD
curl -k https://myproject.local/api/products                         -H "Authorization: Bearer $TOKEN"
curl -k https://myproject.local/api/products/1                       -H "Authorization: Bearer $TOKEN"
curl -k -X POST   https://myproject.local/api/products -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" -d '{"name":"Pen","category":"Stationery","price":9.5,"stock":100}'
curl -k -X PUT    https://myproject.local/api/products/1 -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" -d '{"price":12}'
curl -k -X DELETE https://myproject.local/api/products/1 -H "Authorization: Bearer $TOKEN"
```
(`-k` only because the certificate is self-signed.)
