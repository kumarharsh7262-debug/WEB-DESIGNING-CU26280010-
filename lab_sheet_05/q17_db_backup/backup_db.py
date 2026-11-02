"""Q17 – Back up schema + data of a MySQL database into a .sql file (pure Python, no mysqldump needed).

Usage: python backup_db.py [output.sql]
Restore: mysql -u root -p < backup.sql
"""
import os
import sys
from datetime import date, datetime

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from db_config import DB_CONFIG, get_connection


def sql_literal(conn, value):
    """Convert a Python value into a safe SQL literal."""
    if value is None:
        return "NULL"
    if isinstance(value, (int, float)):
        return str(value)
    if isinstance(value, (bytes, bytearray)):
        return "0x" + value.hex()
    if isinstance(value, (datetime, date)):
        return f"'{value}'"
    s = str(value).replace("\\", "\\\\").replace("'", "''").replace("\n", "\\n").replace("\r", "\\r")
    return f"'{s}'"


def backup(path):
    db = DB_CONFIG["database"]
    conn = get_connection()
    cur = conn.cursor()
    with open(path, "w", encoding="utf-8") as f:
        f.write(f"-- Backup of `{db}` created {datetime.now():%Y-%m-%d %H:%M:%S}\n")
        f.write("SET FOREIGN_KEY_CHECKS=0;\n")
        f.write(f"CREATE DATABASE IF NOT EXISTS `{db}`;\nUSE `{db}`;\n\n")

        cur.execute("SHOW TABLES")
        tables = [t[0] for t in cur.fetchall()]
        for table in tables:
            cur.execute(f"SHOW CREATE TABLE `{table}`")
            create_stmt = cur.fetchone()[1]
            f.write(f"-- Table: {table}\nDROP TABLE IF EXISTS `{table}`;\n{create_stmt};\n\n")

            cur.execute(f"SELECT * FROM `{table}`")
            cols = ", ".join(f"`{c}`" for c in cur.column_names)
            count = 0
            for row in cur:
                vals = ", ".join(sql_literal(conn, v) for v in row)
                f.write(f"INSERT INTO `{table}` ({cols}) VALUES ({vals});\n")
                count += 1
            f.write(f"-- {count} row(s)\n\n")
        f.write("SET FOREIGN_KEY_CHECKS=1;\n")
    conn.close()
    return tables


if __name__ == "__main__":
    out = sys.argv[1] if len(sys.argv) > 1 else f"backup_{DB_CONFIG['database']}_{datetime.now():%Y%m%d_%H%M%S}.sql"
    try:
        tables = backup(out)
        print(f"Backed up {len(tables)} table(s) -> {os.path.abspath(out)} ({os.path.getsize(out)} bytes)")
    except Error as e:
        print("Backup failed:", e)
