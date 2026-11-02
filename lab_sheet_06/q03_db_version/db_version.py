"""Q3 – Connect to MySQL and print the server version."""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from db_config import get_connection

try:
    # No database needed – python_web_db may not exist yet
    conn = get_connection(with_database=False)
    cursor = conn.cursor()
    cursor.execute("SELECT VERSION()")
    (version,) = cursor.fetchone()
    print("Connected to MySQL server")
    print("Server version:", version)
    print("Connection ID :", conn.connection_id)
except Error as e:
    print("Connection failed:", e)
finally:
    if "conn" in locals() and conn.is_connected():
        cursor.close()
        conn.close()
        print("Connection closed.")
