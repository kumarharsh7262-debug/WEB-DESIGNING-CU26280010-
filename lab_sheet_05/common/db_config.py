"""Shared MySQL settings used by every solution.

Override with environment variables if needed:
    export DB_HOST=localhost DB_USER=root DB_PASSWORD=secret DB_NAME=python_web_db
"""
import os

import mysql.connector

DB_CONFIG = {
    "host": os.getenv("DB_HOST", "localhost"),
    "port": int(os.getenv("DB_PORT", "3306")),
    "user": os.getenv("DB_USER", "root"),
    "password": os.getenv("DB_PASSWORD", ""),
    "database": os.getenv("DB_NAME", "python_web_db"),
}


def get_connection(with_database=True):
    """Return a new MySQL connection (without selecting a DB if with_database=False)."""
    cfg = dict(DB_CONFIG)
    if not with_database:
        cfg.pop("database")
    return mysql.connector.connect(**cfg)
