#!/usr/bin/env python3
"""Q18 – Log out: delete the session row and expire the cookie."""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from session_util import destroy_session

print(destroy_session())
print("Status: 303 See Other")
print("Location: login.py")
print()
