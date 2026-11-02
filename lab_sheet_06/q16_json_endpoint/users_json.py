#!/usr/bin/env python3
"""Q16 – Dynamic JSON endpoint backed by MySQL.

Examples (when served at /cgi-bin/users_json.py):
    /cgi-bin/users_json.py
    /cgi-bin/users_json.py?city=Delhi
    /cgi-bin/users_json.py?id=3
"""
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from cgi_utils import get_form
from db_config import get_connection


def respond(status, payload):
    print(f"Status: {status}")
    print("Content-Type: application/json; charset=utf-8")
    print("Access-Control-Allow-Origin: *")
    print()
    print(json.dumps(payload, default=str, indent=2))


form = get_form()
sql = "SELECT id, name, email, age, city, created_at FROM users WHERE 1=1"
params = []

if form.get("id", "").isdigit():
    sql += " AND id = %s"
    params.append(int(form["id"]))
if form.get("city"):
    sql += " AND city = %s"
    params.append(form["city"])
sql += " ORDER BY id"

try:
    conn = get_connection()
    cur = conn.cursor(dictionary=True)
    cur.execute(sql, params)
    rows = cur.fetchall()
    respond("200 OK", {"success": True, "count": len(rows), "data": rows})
except Error as e:
    respond("500 Internal Server Error", {"success": False, "error": "Database error"})
    print(f"users_json error: {e}", file=sys.stderr)       # goes to Apache error.log
finally:
    if "conn" in locals() and conn.is_connected():
        cur.close()
        conn.close()
