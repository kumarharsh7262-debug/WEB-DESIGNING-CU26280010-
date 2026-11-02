-- Q20: limited-privilege MySQL user for the application (run as root AFTER schema.sql).
--   mysql -u root -p < database/mysql_user.sql
CREATE USER IF NOT EXISTS 'myproject_app'@'localhost' IDENTIFIED BY 'ChangeMe_Str0ng!Pass';

-- Only the four data operations, only on this one database.
-- (No DROP, ALTER, CREATE, GRANT, FILE, SUPER ... and no access to other databases.)
GRANT SELECT, INSERT, UPDATE, DELETE ON myproject_db.* TO 'myproject_app'@'localhost';
FLUSH PRIVILEGES;

-- Verify:
SHOW GRANTS FOR 'myproject_app'@'localhost';

-- Then export the same credentials to PHP (e.g. in the Apache vhost: SetEnv DB_USER / DB_PASS)
-- and change the password above to a long random one.
