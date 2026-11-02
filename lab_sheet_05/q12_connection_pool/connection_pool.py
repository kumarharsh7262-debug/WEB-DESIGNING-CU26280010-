"""Q12 – Connection pooling for concurrent access.

Creating a TCP connection + authenticating for every request is slow. A pool keeps connections
open and hands them out, which matters when Apache serves many requests at once.
This demo runs 50 queries on 20 threads sharing a pool of 5 connections.
"""
import os
import sys
import time
from concurrent.futures import ThreadPoolExecutor

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from mysql.connector import Error, pooling

from db_config import DB_CONFIG

pool = pooling.MySQLConnectionPool(
    pool_name="web_pool",
    pool_size=5,            # max connections kept (mysql-connector limit is 32)
    pool_reset_session=True,
    **DB_CONFIG,
)


def run_query(n):
    conn = None
    try:
        conn = pool.get_connection()           # raises PoolError if pool exhausted
        cur = conn.cursor()
        cur.execute("SELECT COUNT(*), CONNECTION_ID() FROM users")
        count, cid = cur.fetchone()
        cur.close()
        return n, count, cid
    except Error as e:
        return n, None, str(e)
    finally:
        if conn:
            conn.close()                       # returns the connection to the pool


if __name__ == "__main__":
    start = time.perf_counter()
    with ThreadPoolExecutor(max_workers=20) as ex:
        results = list(ex.map(run_query, range(50)))
    elapsed = time.perf_counter() - start

    ids = {r[2] for r in results if r[1] is not None}
    print(f"50 queries finished in {elapsed:.2f}s")
    print(f"Distinct MySQL connections used: {len(ids)} (pool size = 5)")
    print("Failures:", sum(1 for r in results if r[1] is None))
