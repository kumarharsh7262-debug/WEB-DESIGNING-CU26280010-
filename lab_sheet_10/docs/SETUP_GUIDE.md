# Setup guide (Git, Apache, SSL, hosts file, MySQL)

## 1. Git and GitHub (Q1, Q2)

Automatic: `bash scripts/git_workflow.sh` runs everything below.
Manual commands:

```bash
git init -b main
git config user.name "Your Name"
git config user.email "you@example.com"

# logical commits
git add .gitignore README.md composer.json docs
git commit -m "chore: add .gitignore, README, composer.json"
git add config app/core
git commit -m "feat(core): config, router, base classes, Database singleton"
git add app/models database
git commit -m "feat(models): User/Product models and schema"
git add app/controllers app/views config/routes.php
git commit -m "feat(mvc): controllers, views, routes"
git add public
git commit -m "feat(public): front controller, .htaccess, assets"

# feature branch
git checkout -b feature/changelog-update
#   ...edit line 3 of docs/CHANGELOG.md...
git commit -am "docs: feature branch changelog"

# change the SAME line on main -> conflict
git checkout main
#   ...edit line 3 of docs/CHANGELOG.md differently...
git commit -am "docs: main changelog"
git merge feature/changelog-update        # CONFLICT in docs/CHANGELOG.md

# open the file, you will see:
#   <<<<<<< HEAD
#   Status: main branch ...
#   =======
#   Status: feature branch ...
#   >>>>>>> feature/changelog-update
# keep the wanted text, delete the three marker lines, then:
git add docs/CHANGELOG.md
git commit -m "merge: resolve changelog conflict"

# push to GitHub (create an empty repository on github.com first)
git remote add origin https://github.com/<username>/myproject.git
git push -u origin main
git push origin feature/changelog-update
```

## 2. Apache modules (Q17, Q18)

Debian/Ubuntu:
```bash
sudo apt install apache2 php libapache2-mod-php php-mysql php-mbstring php-xml mysql-server composer
sudo a2enmod rewrite ssl headers
```
XAMPP/WAMP: enable `mod_rewrite`, `mod_ssl`, `mod_headers` in `httpd.conf`
(remove the `#` before the `LoadModule` lines).

## 3. Self-signed SSL certificate (Q18)

```bash
bash scripts/make_self_signed_cert.sh          # writes /etc/ssl/myproject/myproject.local.{crt,key}
```
Same thing manually:
```bash
sudo openssl req -x509 -nodes -newkey rsa:2048 -days 365 \
  -keyout /etc/ssl/myproject/myproject.local.key -out /etc/ssl/myproject/myproject.local.crt \
  -subj "/CN=myproject.local" -addext "subjectAltName=DNS:myproject.local"
```
XAMPP: put the files in `C:\xampp\apache\conf\ssl.crt\` / `ssl.key\` and change the two
`SSLCertificate*` paths in `apache/myproject.local.conf`.

## 4. Virtual host + hosts file (Q19)

1. Copy the project to `/var/www/myproject` (XAMPP: `C:\xampp\htdocs\myproject`) and fix the paths inside `apache/myproject.local.conf`.
2. Enable the site:
   ```bash
   sudo cp apache/myproject.local.conf /etc/apache2/sites-available/
   sudo a2ensite myproject.local
   sudo apache2ctl configtest && sudo systemctl restart apache2
   ```
3. Map the name to your machine in the **hosts file** (add the line `127.0.0.1  myproject.local`):
   * Linux / macOS: `sudo nano /etc/hosts`
   * Windows: open Notepad **as Administrator** → `C:\Windows\System32\drivers\etc\hosts`
4. Browse to `https://myproject.local` (HTTP redirects to HTTPS; accept the warning once).

Writable folders: `storage/uploads` and `storage/logs` must be writable by the web server
(`sudo chown -R www-data:www-data storage`).

## 5. MySQL limited user (Q20)

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/mysql_user.sql
```
The app user has only `SELECT, INSERT, UPDATE, DELETE` on `myproject_db.*`.
Test: `mysql -u myproject_app -p -e "DROP TABLE myproject_db.users;"` → *ERROR 1142 command denied*.

## 6. E-mail (Q14)

`composer install`, then set `SMTP_HOST`, `SMTP_USER`, `SMTP_PASS` (Gmail needs an *App Password*).
Without SMTP credentials registration still works; the failure is only written to `storage/logs/php_error.log`.

## 7. Error pages and logging (Q16)

* `display_errors` is **off**; errors go to `storage/logs/php_error.log` (set in `app/core/bootstrap.php`, mirrored in the vhost).
* Unknown URL → custom 404 page. Uncaught exception / fatal error → `app/views/errors/500.php`.
* Apache fallback if PHP is down: `public/500.html` via `ErrorDocument 500`.
