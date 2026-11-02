#!/usr/bin/env python3
"""Q7 – CGI script: read form data (cgi.FieldStorage) and insert it into MySQL."""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from cgi_utils import esc, get_form, header, page
from db_config import get_connection

header()
form = get_form()

name = form.get("name", "").strip()
email = form.get("email", "").strip()
age = form.get("age", "").strip()
city = form.get("city", "").strip()

if not name or not email:
    print(page("Error", "<p class='err'>Name and email are required.</p>"))
    sys.exit()

try:
    conn = get_connection()
    cur = conn.cursor()
    cur.execute(
        "INSERT INTO users (name, email, age, city) VALUES (%s, %s, %s, %s)",
        (name, email, int(age) if age.isdigit() else None, city or None),
    )
    conn.commit()
    print(page("Saved", f"<h3 class='ok'>User '{esc(name)}' saved with ID {cur.lastrowid}.</h3>"
                        "<a href='/cgi-bin/users_html.py'>View all users</a>"))
except Error as e:
    print(page("Error", f"<p class='err'>Could not save: {esc(e)}</p>"))
finally:
    if "conn" in locals() and conn.is_connected():
        cur.close()
        conn.close()
