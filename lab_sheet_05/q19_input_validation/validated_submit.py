#!/usr/bin/env python3
"""Q19 – Validate and sanitize web form input before running SQL write operations.

Layers of defence: (1) validate format/length, (2) normalize, (3) parameterized SQL,
(4) escape on output.  Works as CGI (POST name, email, age, city) and via `python validated_submit.py`.
"""
import os
import re
import sys
import unicodedata

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from cgi_utils import esc, get_form, header, page
from db_config import get_connection

EMAIL_RE = re.compile(r"^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$")
NAME_RE = re.compile(r"^[A-Za-z][A-Za-z .'-]{1,99}$")


def clean(text, max_len):
    """Normalize unicode, strip control characters and trim."""
    text = unicodedata.normalize("NFKC", text or "")
    text = "".join(ch for ch in text if ch.isprintable())
    return text.strip()[:max_len]


def validate(form):
    errors, data = [], {}

    data["name"] = clean(form.get("name"), 100)
    if not NAME_RE.match(data["name"]):
        errors.append("Name must be 2-100 letters (spaces, . ' - allowed).")

    data["email"] = clean(form.get("email"), 150).lower()
    if not EMAIL_RE.match(data["email"]):
        errors.append("Email address is not valid.")

    age = clean(form.get("age"), 3)
    if age == "":
        data["age"] = None
    elif age.isdigit() and 1 <= int(age) <= 120:
        data["age"] = int(age)
    else:
        errors.append("Age must be a whole number between 1 and 120.")

    data["city"] = clean(form.get("city"), 100) or None
    if data["city"] and not re.match(r"^[A-Za-z .'-]+$", data["city"]):
        errors.append("City contains invalid characters.")

    return data, errors


def save(data):
    conn = get_connection()
    try:
        cur = conn.cursor()
        cur.execute("INSERT INTO users (name, email, age, city) VALUES (%(name)s, %(email)s, %(age)s, %(city)s)", data)
        conn.commit()
        return cur.lastrowid
    finally:
        conn.close()


if __name__ == "__main__":
    in_cgi = "GATEWAY_INTERFACE" in os.environ
    form = get_form() if in_cgi else {"name": "Test <script>", "email": "bad-email", "age": "999", "city": "Delhi"}
    data, errors = validate(form)

    if in_cgi:
        header()
    if errors:
        print(page("Invalid input", "<h3 class='err'>Please fix:</h3><ul>" +
                   "".join(f"<li>{esc(e)}</li>" for e in errors) + "</ul>") if in_cgi
              else "Rejected:\n  - " + "\n  - ".join(errors))
    else:
        try:
            new_id = save(data)
            msg = f"Saved user #{new_id}: {data['name']} <{data['email']}>"
            print(page("Saved", f"<h3 class='ok'>{esc(msg)}</h3>") if in_cgi else msg)
        except Error as e:
            print(page("Error", f"<p class='err'>{esc(e)}</p>") if in_cgi else f"DB error: {e}")
