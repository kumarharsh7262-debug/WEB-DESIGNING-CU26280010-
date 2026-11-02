#!/usr/bin/env python3
"""Q6 – Fetch all users and format them as an HTML response.

Works as a CGI script (http://localhost/cgi-bin/users_html.py) and from the command line.
"""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from cgi_utils import esc, header, page
from db_config import get_connection


def build_html():
    conn = get_connection()
    cur = conn.cursor()
    cur.execute("SELECT id, name, email, age, city FROM users ORDER BY id")
    rows = cur.fetchall()
    cur.close()
    conn.close()

    html = ["<h2>Users (%d)</h2>" % len(rows),
            "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Age</th><th>City</th></tr>"]
    for r in rows:
        html.append("<tr>" + "".join(f"<td>{esc('' if c is None else c)}</td>" for c in r) + "</tr>")
    html.append("</table>")
    return "\n".join(html)


if __name__ == "__main__":
    header()
    try:
        print(page("Users", build_html()))
    except Error as e:
        print(page("Error", f"<p class='err'>Database error: {esc(e)}</p>"))
