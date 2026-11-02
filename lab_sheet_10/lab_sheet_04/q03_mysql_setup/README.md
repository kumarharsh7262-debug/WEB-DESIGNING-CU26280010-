# Q3 – Install MySQL and create `college_db`

## Install
- XAMPP already includes MySQL/MariaDB. Start **MySQL** in the Control Panel.
- Ubuntu: `sudo apt install mysql-server && sudo systemctl enable --now mysql`
  then `sudo mysql_secure_installation`

## Connect via command line
```bash
mysql -u root -p
```
Then run the contents of `create_db.sql`, or directly:
```bash
mysql -u root -p < create_db.sql
```

## Connect via phpMyAdmin
1. Open `http://localhost/phpmyadmin`.
2. Click **New** → Database name `college_db` → Collation `utf8mb4_unicode_ci` → **Create**.

## Verify
```sql
SHOW DATABASES;
```
`college_db` must appear in the list.
