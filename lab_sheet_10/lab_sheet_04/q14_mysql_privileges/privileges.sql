-- Q14 – Dedicated, least-privilege user for the web app
-- Run as root: mysql -u root -p < privileges.sql

CREATE USER IF NOT EXISTS 'college_app'@'localhost' IDENTIFIED BY 'StrongPass@123';

-- Only the three permissions requested, only on college_db
GRANT SELECT, INSERT, UPDATE ON college_db.* TO 'college_app'@'localhost';
FLUSH PRIVILEGES;

-- Verify
SHOW GRANTS FOR 'college_app'@'localhost';

-- Test (log in as the new user: mysql -u college_app -p)
--   USE college_db;
--   SELECT * FROM students;                     -- works
--   INSERT INTO students (name,email,enrollment_date)
--          VALUES ('Test','t@x.com',CURDATE());  -- works
--   UPDATE students SET name='T2' WHERE id=1;   -- works
--   DELETE FROM students WHERE id=1;            -- ERROR 1142: DELETE command denied
--   DROP TABLE students;                        -- ERROR 1142: DROP command denied

-- To remove the user later:
-- DROP USER 'college_app'@'localhost';
