# Q20 – Python virtual environment + Apache deployment

## 1. Create and populate the venv
```bash
sudo mkdir -p /var/www/pyapp && cd /var/www/pyapp
sudo python3 -m venv venv
sudo ./venv/bin/pip install -r /path/to/lab5_solutions/requirements.txt
./venv/bin/python -c "import mysql.connector, sys; print(sys.prefix)"
```
(`setup_venv.sh` does this automatically.)

## 2A. CGI integration
Change the shebang of each script to the venv interpreter:
```
#!/var/www/pyapp/venv/bin/python
```
(Windows: `#!C:/xampp/pyapp/venv/Scripts/python.exe`)

## 2B. mod_wsgi integration (recommended)
Install mod_wsgi **built for the same Python version** as the venv:
`sudo apt install libapache2-mod-wsgi-py3 && sudo a2enmod wsgi`

Copy `app.wsgi` to `/var/www/pyapp/` and `pyapp.conf` to `/etc/apache2/sites-available/`, then:
```bash
sudo a2ensite pyapp.conf
sudo apachectl configtest
sudo systemctl reload apache2
```
Open `http://localhost/pyapp/` – the page prints the interpreter prefix, which must point inside `venv/`.

## Notes
- `python-home` in `WSGIDaemonProcess` is what ties Apache to the venv.
- After `pip install`-ing new packages, `touch /var/www/pyapp/app.wsgi` reloads the daemon process.
- Make sure `www-data` can read the venv: `sudo chmod -R o+rX /var/www/pyapp/venv`.
