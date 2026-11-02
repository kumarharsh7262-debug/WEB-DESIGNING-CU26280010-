# Q1 – Apache + Python (CGI or mod_wsgi)

## Option A: CGI (simplest)

**Ubuntu/Debian**
```bash
sudo apt install apache2 python3
sudo a2enmod cgi            # (use `cgid` for threaded MPMs: sudo a2enmod cgid)
sudo mkdir -p /var/www/html/cgi-bin
sudo cp hello.py /var/www/html/cgi-bin/
sudo chmod 755 /var/www/html/cgi-bin/hello.py
```
Add to the site config (e.g. `/etc/apache2/sites-available/000-default.conf`) inside `<VirtualHost>`:
```apache
<Directory "/var/www/html/cgi-bin">
    Options +ExecCGI
    AddHandler cgi-script .py
    Require all granted
</Directory>
```
```bash
sudo systemctl restart apache2
```
Open **http://localhost/cgi-bin/hello.py**.

**XAMPP (Windows):** put `hello.py` in `C:\xampp\cgi-bin\`, and change line 1 to your interpreter, e.g.
`#!C:/Python312/python.exe`. XAMPP already maps `/cgi-bin/` and `.py` can be added with
`AddHandler cgi-script .py` in `httpd.conf`.

## Option B: mod_wsgi
```bash
sudo apt install libapache2-mod-wsgi-py3
sudo a2enmod wsgi
sudo cp hello_wsgi.py /var/www/wsgi/
```
```apache
WSGIScriptAlias /hello /var/www/wsgi/hello_wsgi.py
<Directory /var/www/wsgi>
    Require all granted
</Directory>
```
Restart Apache and open **http://localhost/hello**.

## Verify
- `curl -i http://localhost/cgi-bin/hello.py` → `HTTP/1.1 200 OK` and the Hello World HTML.
- **500 error?** Check `/var/log/apache2/error.log` – usual causes: wrong shebang, script not executable,
  missing blank line after the header, or Windows line endings (`dos2unix hello.py`).
