"""Q18 – Session helpers: tokens stored in MySQL, token sent in an HttpOnly cookie."""
import os
import secrets
import sys
from http.cookies import SimpleCookie

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from db_config import get_connection

COOKIE_NAME = "SESSID"
SESSION_MINUTES = 30


def ensure_tables():
    conn = get_connection()
    cur = conn.cursor()
    cur.execute("""CREATE TABLE IF NOT EXISTS sessions (
        token CHAR(64) PRIMARY KEY, user_id INT NOT NULL, expires_at DATETIME NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB""")
    conn.commit()
    conn.close()


def create_session(user_id):
    """Store a new random token and return the Set-Cookie header line."""
    token = secrets.token_hex(32)
    conn = get_connection()
    cur = conn.cursor()
    cur.execute("INSERT INTO sessions (token, user_id, expires_at) "
                "VALUES (%s, %s, NOW() + INTERVAL %s MINUTE)", (token, user_id, SESSION_MINUTES))
    conn.commit()
    conn.close()
    return (f"Set-Cookie: {COOKIE_NAME}={token}; Path=/; HttpOnly; SameSite=Lax; "
            f"Max-Age={SESSION_MINUTES * 60}")


def current_user():
    """Return the logged-in user's dict, or None. Also extends the session (sliding expiry)."""
    cookie = SimpleCookie(os.environ.get("HTTP_COOKIE", ""))
    if COOKIE_NAME not in cookie:
        return None
    token = cookie[COOKIE_NAME].value
    conn = get_connection()
    cur = conn.cursor(dictionary=True)
    cur.execute("""SELECT u.id, u.name, u.email FROM sessions s JOIN users u ON u.id = s.user_id
                   WHERE s.token = %s AND s.expires_at > NOW()""", (token,))
    user = cur.fetchone()
    if user:
        cur.execute("UPDATE sessions SET expires_at = NOW() + INTERVAL %s MINUTE WHERE token = %s",
                    (SESSION_MINUTES, token))
        conn.commit()
    conn.close()
    return user


def destroy_session():
    """Delete the server-side session and return a cookie-expiring header line."""
    cookie = SimpleCookie(os.environ.get("HTTP_COOKIE", ""))
    if COOKIE_NAME in cookie:
        conn = get_connection()
        cur = conn.cursor()
        cur.execute("DELETE FROM sessions WHERE token = %s", (cookie[COOKIE_NAME].value,))
        conn.commit()
        conn.close()
    return f"Set-Cookie: {COOKIE_NAME}=; Path=/; Max-Age=0"
