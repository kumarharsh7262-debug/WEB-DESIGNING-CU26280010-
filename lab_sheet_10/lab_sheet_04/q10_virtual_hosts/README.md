# Q10 – Apache virtual host: `http://collegeweb.local`

1. **Create the project folder** and copy `index.php` from here into it:
   - XAMPP: `C:\xampp\htdocs\collegeweb\`
   - Linux: `/var/www/collegeweb/`

2. **Enable vhosts include** in `httpd.conf` (XAMPP) – uncomment:
   ```
   Include conf/extra/httpd-vhosts.conf
   ```

3. **Add the virtual host** – paste `httpd-vhosts.conf` into `conf/extra/httpd-vhosts.conf`.

4. **Map the domain to your machine** by editing the hosts file as Administrator/root:
   - Windows: `C:\Windows\System32\drivers\etc\hosts`
   - Linux/macOS: `/etc/hosts`
   ```
   127.0.0.1   collegeweb.local
   ```

5. **Check syntax & restart Apache**
   ```
   httpd -t          # or: apachectl configtest
   ```
   Restart from the XAMPP panel, or `sudo systemctl restart apache2`.

   *Ubuntu only:* save the config as `/etc/apache2/sites-available/collegeweb.conf`, then
   `sudo a2ensite collegeweb.conf && sudo systemctl reload apache2`.

6. **Test:** open `http://collegeweb.local` – you should see the page from `index.php`.
