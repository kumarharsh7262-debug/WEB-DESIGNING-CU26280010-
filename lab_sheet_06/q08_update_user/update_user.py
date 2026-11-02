"""Q8 – Update a user's profile by ID.

Usage:
    python update_user.py <id> [--name NAME] [--email EMAIL] [--age AGE] [--city CITY]
Example:
    python update_user.py 1 --city Chennai --age 26
"""
import argparse
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error

from db_config import get_connection

parser = argparse.ArgumentParser(description="Update a user profile by ID")
parser.add_argument("user_id", type=int)
parser.add_argument("--name")
parser.add_argument("--email")
parser.add_argument("--age", type=int)
parser.add_argument("--city")
args = parser.parse_args()

# Build the SET clause only from the fields supplied (column names are fixed, values are bound)
fields = {k: v for k, v in (("name", args.name), ("email", args.email),
                            ("age", args.age), ("city", args.city)) if v is not None}
if not fields:
    sys.exit("Nothing to update. Pass at least one of --name --email --age --city.")

set_clause = ", ".join(f"{col} = %s" for col in fields)
sql = f"UPDATE users SET {set_clause} WHERE id = %s"

try:
    conn = get_connection()
    cur = conn.cursor()
    cur.execute(sql, (*fields.values(), args.user_id))
    conn.commit()
    if cur.rowcount:
        print(f"User {args.user_id} updated: {fields}")
    else:
        print(f"No change: user {args.user_id} not found or values identical.")
except Error as e:
    conn.rollback()
    print("Update failed:", e)
finally:
    if "conn" in locals() and conn.is_connected():
        cur.close()
        conn.close()
