# Q1 – Configure and verify Apache locally

## Install
- **Windows/macOS:** install XAMPP (https://www.apachefriends.org) – includes Apache, PHP, MySQL, phpMyAdmin.
- **Ubuntu/Debian:** `sudo apt update && sudo apt install apache2 php libapache2-mod-php php-mysql mysql-server`

## Start
- XAMPP: open the Control Panel and click **Start** next to Apache.
- Linux: `sudo systemctl enable --now apache2`

## Verify
1. Open `http://localhost` in a browser – the default page (XAMPP dashboard / "Apache2 Default Page") should load.
2. Copy `index.html` from this folder into the document root:
   - XAMPP: `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/`
   - Linux: `/var/www/html/`
3. Reload `http://localhost/index.html` – it should show the test page.
4. Command-line check: `curl -I http://localhost` → expect `HTTP/1.1 200 OK`.
5. Config syntax check: `apachectl configtest` (or `httpd -t`) → `Syntax OK`.

## Troubleshooting
- Port 80 busy (Skype/IIS): change `Listen 80` to `Listen 8080` in `httpd.conf` and use `http://localhost:8080`.
