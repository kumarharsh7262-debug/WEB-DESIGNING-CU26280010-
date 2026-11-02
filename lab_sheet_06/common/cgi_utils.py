"""Small CGI helpers.

The stdlib `cgi` module was removed in Python 3.13, so this module uses `cgi.FieldStorage`
when it exists and falls back to a pure-stdlib parser otherwise.
"""
import html
import os
import sys
from urllib.parse import parse_qs

try:  # Python <= 3.12
    import cgi  # noqa: F401
    HAVE_CGI = True
except ImportError:  # Python >= 3.13
    HAVE_CGI = False


def get_form():
    """Return form data (GET query string + POST body) as {name: value}."""
    data = {}
    if HAVE_CGI:
        import cgi
        form = cgi.FieldStorage()
        for key in form.keys():
            data[key] = form.getfirst(key, "")
        return data

    query = os.environ.get("QUERY_STRING", "")
    for k, v in parse_qs(query, keep_blank_values=True).items():
        data[k] = v[0]
    if os.environ.get("REQUEST_METHOD", "GET").upper() == "POST":
        length = int(os.environ.get("CONTENT_LENGTH") or 0)
        body = sys.stdin.read(length) if length else ""
        for k, v in parse_qs(body, keep_blank_values=True).items():
            data[k] = v[0]
    return data


def header(content_type="text/html", extra=None):
    """Print the CGI header block (must be first output)."""
    print(f"Content-Type: {content_type}; charset=utf-8")
    for line in (extra or []):
        print(line)
    print()


def esc(value):
    """HTML-escape any value."""
    return html.escape(str(value))


def page(title, body):
    """Wrap body HTML in a simple styled page."""
    return f"""<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><title>{esc(title)}</title>
<style>
 body{{font-family:Arial,sans-serif;margin:30px;background:#f4f6f8}}
 table{{border-collapse:collapse;background:#fff}}
 th{{background:#1976d2;color:#fff}}
 th,td{{padding:8px 14px;border:1px solid #ddd;text-align:left}}
 .ok{{color:#2e7d32}} .err{{color:#c62828}}
</style></head><body>
{body}
</body></html>"""
