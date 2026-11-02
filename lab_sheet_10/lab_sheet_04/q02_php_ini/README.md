# Q2 – Enable error reporting in php.ini and verify with phpinfo()

## Locate php.ini
Run `phpinfo.php` (this folder) and look for **Loaded Configuration File**, or run `php --ini`.
Typical paths: `C:\xampp\php\php.ini`, `/opt/lampp/etc/php.ini`, `/etc/php/8.x/apache2/php.ini`.

## Edit these directives
```ini
display_errors = On
display_startup_errors = On
error_reporting = E_ALL
log_errors = On
```

## Restart Apache
- XAMPP: Stop → Start Apache
- Linux: `sudo systemctl restart apache2`

## Verify
1. Put `phpinfo.php` in the document root and open `http://localhost/phpinfo.php`.
2. Search the page (Ctrl+F) for `display_errors` – Local Value should be **On**.
3. Open `http://localhost/error_test.php` – a visible warning/notice should appear.

> Use `display_errors = Off` on production servers; log errors instead.
