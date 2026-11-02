"""Q14 – Read a CSV file and bulk-insert its rows into MySQL.

Usage: python csv_bulk_insert.py [file.csv]     (default: new_users.csv)
"""
import csv
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from db_config import get_connection

BATCH = 500
path = sys.argv[1] if len(sys.argv) > 1 else os.path.join(os.path.dirname(__file__), "new_users.csv")

rows, skipped = [], 0
with open(path, newline="", encoding="utf-8-sig") as f:
    for line_no, rec in enumerate(csv.DictReader(f), start=2):
        name, email = (rec.get("name") or "").strip(), (rec.get("email") or "").strip()
        age = (rec.get("age") or "").strip()
        if not name or "@" not in email:
            print(f"  skipping line {line_no}: invalid name/email")
            skipped += 1
            continue
        rows.append((name, email, int(age) if age.isdigit() else None, (rec.get("city") or "").strip() or None))

sql = "INSERT IGNORE INTO users (name, email, age, city) VALUES (%s, %s, %s, %s)"
inserted = 0
try:
    conn = get_connection()
    cur = conn.cursor()
    for i in range(0, len(rows), BATCH):          # chunked so huge files don't exhaust memory/packet size
        cur.executemany(sql, rows[i:i + BATCH])
        inserted += cur.rowcount
    conn.commit()                                  # all-or-nothing
    print(f"Read {len(rows) + skipped} rows: {inserted} inserted, "
          f"{len(rows) - inserted} duplicates ignored, {skipped} invalid skipped.")
except Error as e:
    if "conn" in locals():
        conn.rollback()
    print("Bulk insert failed, rolled back:", e)
finally:
    if "conn" in locals() and conn.is_connected():
        cur.close()
        conn.close()
