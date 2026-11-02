#!/usr/bin/env python3
"""Q21 – Analytical summary report (counts, averages) for an admin dashboard.

Run from the command line for a text report, or as CGI (/cgi-bin/reports.py) for an HTML dashboard.
"""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from cgi_utils import esc, header, page
from db_config import get_connection


def collect():
    conn = get_connection()
    cur = conn.cursor(dictionary=True)

    cur.execute("""SELECT COUNT(*) AS total_users,
                          COUNT(age) AS users_with_age,
                          ROUND(AVG(age), 1) AS avg_age,
                          MIN(age) AS min_age,
                          MAX(age) AS max_age,
                          COUNT(DISTINCT city) AS cities
                   FROM users""")
    overview = cur.fetchone()

    cur.execute("""SELECT COALESCE(city, 'Unknown') AS city, COUNT(*) AS users, ROUND(AVG(age), 1) AS avg_age
                   FROM users GROUP BY city ORDER BY users DESC, city LIMIT 10""")
    by_city = cur.fetchall()

    cur.execute("""SELECT CASE WHEN age IS NULL THEN 'Unknown'
                               WHEN age < 22 THEN 'Under 22'
                               WHEN age BETWEEN 22 AND 24 THEN '22-24'
                               ELSE '25+' END AS age_group,
                          COUNT(*) AS users
                   FROM users GROUP BY age_group ORDER BY users DESC""")
    by_age = cur.fetchall()

    cur.execute("""SELECT DATE(created_at) AS day, COUNT(*) AS signups
                   FROM users GROUP BY DATE(created_at) ORDER BY day DESC LIMIT 7""")
    daily = cur.fetchall()

    conn.close()
    return overview, by_city, by_age, daily


def table(rows, headers):
    if not rows:
        return "<p>No data.</p>"
    head = "".join(f"<th>{esc(h)}</th>" for h in headers)
    body = "".join("<tr>" + "".join(f"<td>{esc(v)}</td>" for v in r.values()) + "</tr>" for r in rows)
    return f"<table><tr>{head}</tr>{body}</table>"


def html_report():
    o, by_city, by_age, daily = collect()
    cards = "".join(
        f"<div style='display:inline-block;background:#fff;padding:14px 22px;margin:6px;"
        f"box-shadow:0 1px 4px #0003'><div style='font-size:26px;color:#1976d2'>{esc(v)}</div>"
        f"<div style='color:#555'>{esc(k.replace('_', ' '))}</div></div>" for k, v in o.items())
    return (f"<h1>Admin Summary</h1>{cards}"
            f"<h3>Top cities</h3>{table(by_city, ['City', 'Users', 'Avg age'])}"
            f"<h3>Age groups</h3>{table(by_age, ['Group', 'Users'])}"
            f"<h3>Signups (last 7 active days)</h3>{table(daily, ['Day', 'Signups'])}")


def text_report():
    o, by_city, by_age, daily = collect()
    lines = ["=== ADMIN SUMMARY ===", *[f"{k.replace('_', ' '):<16}: {v}" for k, v in o.items()],
             "", "--- Top cities ---", *[f"{r['city']:<14} users={r['users']:<3} avg_age={r['avg_age']}" for r in by_city],
             "", "--- Age groups ---", *[f"{r['age_group']:<10} {r['users']}" for r in by_age],
             "", "--- Signups ---", *[f"{r['day']}  {r['signups']}" for r in daily]]
    return "\n".join(lines)


if __name__ == "__main__":
    in_cgi = "GATEWAY_INTERFACE" in os.environ
    try:
        if in_cgi:
            header()
            print(page("Admin Summary", html_report()))
        else:
            print(text_report())
    except Error as e:
        print(page("Error", f"<p class='err'>{esc(e)}</p>") if in_cgi else f"Database error: {e}")
