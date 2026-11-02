#!/usr/bin/env python3
"""Q18 – Protected page: needs a valid session stored in MySQL."""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from cgi_utils import esc, page

from session_util import current_user

user = current_user()
if not user:
    print("Status: 303 See Other\nLocation: login.py\n")
    sys.exit()

print("Content-Type: text/html; charset=utf-8\n")
print(page("Profile", f"""<h2>Welcome, {esc(user['name'])}!</h2>
<p>Email: {esc(user['email'])}</p>
<p>Your session is stored in MySQL and renews on each visit (30 min idle timeout).</p>
<p><a href="logout.py">Log out</a></p>"""))
