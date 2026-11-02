"""Q18 – Create a demo login: demo@example.com / demo12345 (run once from the command line)."""
import hashlib
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from db_config import get_connection

from session_util import ensure_tables


def hash_password(password: str, salt: bytes = None) -> str:
    """PBKDF2-HMAC-SHA256 (stdlib only). Format: salt_hex$hash_hex"""
    salt = salt or os.urandom(16)
    digest = hashlib.pbkdf2_hmac("sha256", password.encode(), salt, 200_000)
    return salt.hex() + "$" + digest.hex()


if __name__ == "__main__":
    conn = get_connection()
    cur = conn.cursor()
    cur.execute("INSERT INTO users (name, email, password_hash) VALUES (%s, %s, %s) "
                "ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)",
                ("Demo User", "demo@example.com", hash_password("demo12345")))
    conn.commit()
    conn.close()
    ensure_tables()
    print("Demo user ready -> demo@example.com / demo12345")
