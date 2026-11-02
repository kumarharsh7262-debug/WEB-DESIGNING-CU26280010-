#!/usr/bin/env python3
# Q10 – If you can read this page rendered (not as source code), .htaccess works.
import os

print("Content-Type: text/html; charset=utf-8")
print()
print("<h1 style='font-family:Arial'>.htaccess handler is working ✔</h1>")
print(f"<p>Script: <code>{os.environ.get('SCRIPT_NAME', '')}</code></p>")
print(f"<p>Server: <code>{os.environ.get('SERVER_SOFTWARE', '')}</code></p>")
