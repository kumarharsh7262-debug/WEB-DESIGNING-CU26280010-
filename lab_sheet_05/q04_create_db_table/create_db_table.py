"""Q4 – Create database python_web_db and the users table."""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from db_config import get_connection

try:
    conn = get_connection(with_database=False)
    cur = conn.cursor()

    cur.execute(
        "CREATE DATABASE IF NOT EXISTS python_web_db "
        "CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    )
    cur.execute("USE python_web_db")
    cur.execute(
        """
        CREATE TABLE IF NOT EXISTS users (
            id            INT AUTO_INCREMENT PRIMARY KEY,
            name          VARCHAR(100) NOT NULL,
            email         VARCHAR(150) NOT NULL UNIQUE,
            age           INT NULL,
            city          VARCHAR(100) NULL,
            password_hash VARCHAR(255) NULL,
            created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
        """
    )
    print("Database 'python_web_db' and table 'users' are ready.")

    cur.execute("DESCRIBE users")
    for field, ftype, null, key, default, extra in cur.fetchall():
        print(f"  {field:<14} {ftype:<14} null={null:<3} key={key}")
except Error as e:
    print("Error:", e)
finally:
    if "conn" in locals() and conn.is_connected():
        cur.close()
        conn.close()
