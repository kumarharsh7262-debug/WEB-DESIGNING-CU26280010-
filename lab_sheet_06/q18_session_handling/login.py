#!/usr/bin/env python3
"""Q18 – Login page: verifies credentials against MySQL and starts a session."""
import hashlib
import hmac
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from cgi_utils import esc, get_form, page
from db_config import get_connection

from session_util import create_session, current_user


def verify_password(password, stored):
    try:
        salt_hex, hash_hex = stored.split("$")
    except (ValueError, AttributeError):
        return False
    digest = hashlib.pbkdf2_hmac("sha256", password.encode(), bytes.fromhex(salt_hex), 200_000)
    return hmac.compare_digest(digest.hex(), hash_hex)


form = get_form()
message, extra = "", []

if os.environ.get("REQUEST_METHOD") == "POST":
    conn = get_connection()
    cur = conn.cursor(dictionary=True)
    cur.execute("SELECT id, password_hash FROM users WHERE email = %s", (form.get("email", ""),))
    row = cur.fetchone()
    conn.close()
    if row and verify_password(form.get("password", ""), row["password_hash"]):
        extra.append(create_session(row["id"]))
        extra.append("Status: 303 See Other")
        extra.append("Location: profile.py")
        print("\n".join(extra))
        print()
        sys.exit()
    message = "<p class='err'>Invalid email or password.</p>"
elif current_user():
    print("Status: 303 See Other\nLocation: profile.py\n")
    sys.exit()

print("Content-Type: text/html; charset=utf-8\n")
print(page("Login", f"""<h2>Login</h2>{message}
<form method="post">
  <input type="email" name="email" placeholder="Email" required><br><br>
  <input type="password" name="password" placeholder="Password" required><br><br>
  <button>Log in</button>
</form><p><small>Demo: demo@example.com / demo12345</small></p>"""))
