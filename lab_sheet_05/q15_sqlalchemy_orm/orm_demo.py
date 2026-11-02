"""Q15 – SQLAlchemy ORM: map a Python class to the MySQL `users` table.

Install: pip install SQLAlchemy PyMySQL      (SQLAlchemy 2.x)
"""
import os
import sys
from datetime import datetime
from typing import Optional

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "common"))
from sqlalchemy import DateTime, String, create_engine, func, select
from sqlalchemy.orm import DeclarativeBase, Mapped, Session, mapped_column

from db_config import DB_CONFIG

URL = (f"mysql+pymysql://{DB_CONFIG['user']}:{DB_CONFIG['password']}"
       f"@{DB_CONFIG['host']}:{DB_CONFIG['port']}/{DB_CONFIG['database']}?charset=utf8mb4")
engine = create_engine(URL, pool_size=5, pool_pre_ping=True, echo=False)


class Base(DeclarativeBase):
    pass


class User(Base):
    __tablename__ = "users"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(100))
    email: Mapped[str] = mapped_column(String(150), unique=True)
    age: Mapped[Optional[int]]
    city: Mapped[Optional[str]] = mapped_column(String(100))
    created_at: Mapped[datetime] = mapped_column(DateTime, server_default=func.now())

    def __repr__(self):
        return f"<User {self.id} {self.name} <{self.email}>>"


Base.metadata.create_all(engine)       # creates the table only if it does not exist

with Session(engine) as session:
    # CREATE
    if not session.scalar(select(User).where(User.email == "orm.user@example.com")):
        session.add(User(name="ORM User", email="orm.user@example.com", age=30, city="Noida"))
        session.commit()

    # READ
    print("All users:")
    for u in session.scalars(select(User).order_by(User.id)):
        print("  ", u)

    # UPDATE
    u = session.scalar(select(User).where(User.email == "orm.user@example.com"))
    u.city = "Gurugram"
    session.commit()
    print("Updated:", u.name, "->", u.city)

    # DELETE
    session.delete(u)
    session.commit()
    print("Deleted ORM User. Remaining:", session.scalar(select(func.count()).select_from(User)))
