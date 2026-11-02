#!/usr/bin/env python3
# Q1 – CGI "Hello World". Must print the header block first, then a blank line.
import platform

print("Content-Type: text/html; charset=utf-8")
print()
print(f"""<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Hello CGI</title></head>
<body style="font-family:Arial,sans-serif;text-align:center;margin-top:15vh">
<h1>Hello, World from Python CGI!</h1>
<p>Python {platform.python_version()} on {platform.system()}</p>
</body></html>""")
