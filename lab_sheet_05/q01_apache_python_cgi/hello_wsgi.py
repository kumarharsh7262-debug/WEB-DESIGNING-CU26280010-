# Q1 (WSGI option) – a minimal WSGI application served by mod_wsgi
import platform


def application(environ, start_response):
    body = (
        "<h1>Hello, World from Python WSGI!</h1>"
        f"<p>Python {platform.python_version()} &middot; path: {environ.get('PATH_INFO', '/')}</p>"
    ).encode("utf-8")
    start_response("200 OK", [
        ("Content-Type", "text/html; charset=utf-8"),
        ("Content-Length", str(len(body))),
    ])
    return [body]
