"""Q11 – Parameterized (injection-safe) filtered query.

Usage: python search_users.py [--city CITY] [--min-age N] [--name-contains TEXT]
Try an injection attempt:  python search_users.py --city "Delhi' OR '1'='1"   -> returns nothing.
"""
import argparse
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from db_config import get_connection

p = argparse.ArgumentParser()
p.add_argument("--city")
p.add_argument("--min-age", type=int)
p.add_argument("--name-contains")
args = p.parse_args()

sql = "SELECT id, name, email, age, city FROM users WHERE 1=1"
params = []
if args.city:
    sql += " AND city = %s"
    params.append(args.city)
if args.min_age is not None:
    sql += " AND age >= %s"
    params.append(args.min_age)
if args.name_contains:
    sql += " AND name LIKE %s"
    params.append(f"%{args.name_contains}%")
sql += " ORDER BY name"

try:
    conn = get_connection()
    cur = conn.cursor(dictionary=True)
    cur.execute(sql, params)      # values are sent separately from the SQL text
    rows = cur.fetchall()
    print(f"{len(rows)} result(s)")
    for r in rows:
        print(f"  #{r['id']:<3} {r['name']:<16} {r['email']:<24} age={r['age']} city={r['city']}")
except Error as e:
    print("Database error:", e)
finally:
    if "conn" in locals() and conn.is_connected():
        cur.close()
        conn.close()

# BAD (never do this):  cur.execute("... WHERE city = '" + user_input + "'")
