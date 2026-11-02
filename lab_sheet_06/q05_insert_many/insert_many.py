"""Q5 – Insert several users at once with executemany()."""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from db_config import get_connection

users = [
    ("Aarav Sharma", "aarav@example.com", 22, "Delhi"),
    ("Diya Patel", "diya@example.com", 24, "Mumbai"),
    ("Rohan Gupta", "rohan@example.com", 21, "Pune"),
    ("Sneha Reddy", "sneha@example.com", 23, "Hyderabad"),
    ("Vikram Singh", "vikram@example.com", 25, "Jaipur"),
]

sql = "INSERT IGNORE INTO users (name, email, age, city) VALUES (%s, %s, %s, %s)"

try:
    conn = get_connection()
    cur = conn.cursor()
    cur.executemany(sql, users)      # one call, many rows
    conn.commit()
    print(f"{cur.rowcount} row(s) inserted (duplicates by email are skipped).")
except Error as e:
    if "conn" in locals():
        conn.rollback()
    print("Error:", e)
finally:
    if "conn" in locals() and conn.is_connected():
        cur.close()
        conn.close()
