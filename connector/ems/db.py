"""MySQL access for the collector.

Wraps mysql-connector so the caller never has to think about a connection that
died while the process was sleeping between cycles, and never leaks a cursor on
an error path.
"""

import logging

import mysql.connector

from . import settings

log = logging.getLogger(__name__)


class Database:
    """A lazily-(re)connected MySQL handle.

    The poller sleeps for a minute at a time, which is long enough for MySQL to
    drop the connection (wait_timeout, or XAMPP being restarted). Every call
    checks liveness and reconnects rather than assuming the handle survived.
    """

    def __init__(self, config=None):
        self._config = config or settings.DB_CONFIG
        self._conn = None

    def connection(self):
        if self._conn is None or not self._conn.is_connected():
            if self._conn is not None:
                log.warning("Database connection lost, reconnecting")
                try:
                    self._conn.close()
                except Exception:
                    pass
            self._conn = mysql.connector.connect(**self._config)
        return self._conn

    def query(self, sql, params=None):
        """Run a SELECT and return all rows as dicts."""
        conn = self.connection()
        cursor = conn.cursor(dictionary=True)
        try:
            cursor.execute(sql, params or ())
            return cursor.fetchall()
        finally:
            cursor.close()

    def execute(self, sql, params=None):
        """Run a write and commit it."""
        conn = self.connection()
        cursor = conn.cursor()
        try:
            cursor.execute(sql, params or ())
            conn.commit()
            return cursor.rowcount
        finally:
            cursor.close()

    def close(self):
        if self._conn is not None:
            try:
                if self._conn.is_connected():
                    self._conn.close()
            except Exception:
                pass
            self._conn = None


def fetch_active_meters(db):
    """Meters the collector should poll, newest config each cycle.

    is_active gates polling; is_deleted is the app-wide soft delete.
    """
    return db.query(
        "SELECT * FROM meter WHERE is_active = 1 AND is_deleted = 0 ORDER BY id"
    )


def fetch_meter(db, meter_id):
    """One meter by id, regardless of is_active — the Test button needs to
    reach meters that have not been enabled yet."""
    rows = db.query(
        "SELECT * FROM meter WHERE id = %s AND is_deleted = 0", (meter_id,)
    )
    return rows[0] if rows else None
