"""Q9 – Delete a user and handle database exceptions with try/except.

Usage: python delete_user.py <id>
"""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
import mysql.connector
from mysql.connector import Error, IntegrityError

from db_config import get_connection


def delete_user(user_id: int) -> None:
    conn = None
    try:
        conn = get_connection()
        cur = conn.cursor()
        cur.execute("DELETE FROM users WHERE id = %s", (user_id,))
        conn.commit()
        if cur.rowcount:
            print(f"User {user_id} deleted.")
        else:
            print(f"No user with ID {user_id}.")
    except IntegrityError as e:          # e.g. a foreign key still references this row
        print("Cannot delete – row is referenced elsewhere:", e)
        if conn:
            conn.rollback()
    except mysql.connector.errors.ProgrammingError as e:   # bad SQL / missing table
        print("SQL or schema problem:", e)
    except Error as e:                    # any other MySQL error (connection lost, etc.)
        print("Database error:", e)
        if conn and conn.is_connected():
            conn.rollback()
    finally:
        if conn and conn.is_connected():
            conn.close()


if __name__ == "__main__":
    if len(sys.argv) != 2 or not sys.argv[1].isdigit():
        sys.exit("Usage: python delete_user.py <id>")
    delete_user(int(sys.argv[1]))
