"""WSGI entry point – shows that Apache runs inside the virtualenv and can reach MySQL."""
import sys


def application(environ, start_response):
    try:
        import mysql.connector
        driver = f"mysql-connector-python {mysql.connector.__version__} (import OK)"
    except ImportError:
        driver = "mysql-connector-python NOT importable – venv not wired up"

    body = (
        "<h1>Apache + venv</h1>"
        f"<p><b>sys.prefix:</b> {sys.prefix}</p>"
        f"<p><b>Python:</b> {sys.version.split()[0]}</p>"
        f"<p><b>Driver:</b> {driver}</p>"
    ).encode()
    start_response("200 OK", [("Content-Type", "text/html; charset=utf-8"),
                              ("Content-Length", str(len(body)))])
    return [body]
