"""Heartbeat and per-meter health, so the web UI can see the collector.

Nothing in the app could previously tell whether the poller was running — the
only signal was whether new meter_data rows kept appearing, which is invisible
from the meter management page.
"""

import logging
import os
import socket
from datetime import datetime

log = logging.getLogger(__name__)

_HOST = socket.gethostname()[:100]


def mark_started(db):
    db.execute(
        """
        INSERT INTO collector_status (id, started_at, pid, host)
        VALUES (1, %s, %s, %s)
        ON DUPLICATE KEY UPDATE started_at = VALUES(started_at),
                                pid = VALUES(pid),
                                host = VALUES(host)
        """,
        (datetime.now(), os.getpid(), _HOST),
    )


def record_cycle(db, duration_ms, meters_ok, meters_failed):
    db.execute(
        """
        INSERT INTO collector_status (id, last_cycle_at, last_cycle_ms, meters_ok, meters_failed, pid, host)
        VALUES (1, %s, %s, %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE last_cycle_at = VALUES(last_cycle_at),
                                last_cycle_ms = VALUES(last_cycle_ms),
                                meters_ok     = VALUES(meters_ok),
                                meters_failed = VALUES(meters_failed),
                                pid           = VALUES(pid),
                                host          = VALUES(host)
        """,
        (datetime.now(), duration_ms, meters_ok, meters_failed, os.getpid(), _HOST),
    )


def record_ok(db, meter_id, partial_failures=None):
    """The meter answered. partial_failures names registers that didn't."""
    note = None
    if partial_failures:
        note = "partial: " + ", ".join(partial_failures)
        note = note[:255]
    db.execute(
        """
        INSERT INTO meter_health (meter_id, last_ok_at, last_error, consecutive_failures)
        VALUES (%s, %s, %s, 0)
        ON DUPLICATE KEY UPDATE last_ok_at = VALUES(last_ok_at),
                                last_error = VALUES(last_error),
                                consecutive_failures = 0
        """,
        (meter_id, datetime.now(), note),
    )


def record_failure(db, meter_id, message):
    db.execute(
        """
        INSERT INTO meter_health (meter_id, last_error_at, last_error, consecutive_failures)
        VALUES (%s, %s, %s, 1)
        ON DUPLICATE KEY UPDATE last_error_at = VALUES(last_error_at),
                                last_error    = VALUES(last_error),
                                consecutive_failures = meter_health.consecutive_failures + 1
        """,
        (meter_id, datetime.now(), str(message)[:255]),
    )
