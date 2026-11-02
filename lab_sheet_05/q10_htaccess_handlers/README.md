# Q10 – `.htaccess` + handler directives for Python

1. Make sure CGI is enabled: `sudo a2enmod cgi` (or `cgid`), and `sudo a2enmod rewrite` if needed.
2. Allow `.htaccess` to override options for your web directory (main config / vhost):
   ```apache
   <Directory "/var/www/html/pyapp">
       AllowOverride Options FileInfo
       Require all granted
   </Directory>
   ```
3. Copy this folder's `.htaccess` and `index.py` into `/var/www/html/pyapp/`, then:
   ```bash
   chmod 755 /var/www/html/pyapp/index.py
   sudo systemctl restart apache2
   ```
4. Open `http://localhost/pyapp/` – the page rendered by `index.py` should appear.

## What each directive does
| Directive | Purpose |
|---|---|
| `Options +ExecCGI` | permit CGI execution in this directory |
| `AddHandler cgi-script .py` | treat `.py` files as CGI programs |
| `DirectoryIndex index.py index.html` | default page for the folder |
| `<FilesMatch …> Require all denied` | block direct download of helper/config files |

## Troubleshooting
- **Browser shows Python source / downloads the file:** handler not applied → check `AllowOverride` includes `Options FileInfo`.
- **500 Internal Server Error:** read `error.log`; look for "Options ExecCGI is off" or "End of script output before headers".
- **"Options not allowed here":** `AllowOverride` is `None`.
