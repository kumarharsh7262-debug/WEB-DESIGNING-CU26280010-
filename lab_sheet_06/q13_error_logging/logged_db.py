"""Q13 – Log DB errors and access timestamps into a dedicated log file.

Log file: ./logs/db_app.log (created automatically; for Apache use a path writable by www-data).
"""
import logging
import os
import sys
from logging.handlers import RotatingFileHandler

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from db_config import get_connection

LOG_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "logs")
os.makedirs(LOG_DIR, exist_ok=True)

logger = logging.getLogger("db_app")
logger.setLevel(logging.INFO)
handler = RotatingFileHandler(os.path.join(LOG_DIR, "db_app.log"), maxBytes=1_000_000, backupCount=3)
handler.setFormatter(logging.Formatter("%(asctime)s | %(levelname)-7s | %(message)s"))
logger.addHandler(handler)


def run(sql, params=None):
    """Execute a query, logging access time, duration and any error."""
    conn = None
    try:
        conn = get_connection()
        logger.info("ACCESS  connected to database (conn id %s)", conn.connection_id)
        cur = conn.cursor()
        cur.execute(sql, params or ())
        rows = cur.fetchall()
        logger.info("QUERY   ok rows=%d sql=%s", len(rows), sql)
        return rows
    except Error as e:
        logger.error("DBERROR errno=%s msg=%s sql=%s", getattr(e, "errno", "?"), e, sql)
        return None
    finally:
        if conn and conn.is_connected():
            conn.close()
            logger.info("ACCESS  connection closed")


if __name__ == "__main__":
    print("Valid query  ->", run("SELECT COUNT(*) FROM users"))
    print("Broken query ->", run("SELECT * FROM table_that_does_not_exist"))
    print(f"\nSee the log: {os.path.join(LOG_DIR, 'db_app.log')}")
