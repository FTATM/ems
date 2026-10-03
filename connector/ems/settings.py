"""Configuration, loaded from the repo-root .env.

Replaces the old connector/config.py, which called a bare load_dotenv(). That
resolves relative to the *current working directory*, so running the poller
from inside connector/ left every DB_CONFIG value as None and failed silently
on connect. It only ever worked because runscriptmeter.bat cd'd to the repo
root first. Here the path is derived from __file__ instead.
"""

import os
from pathlib import Path

from dotenv import load_dotenv

# connector/ems/settings.py -> connector/ems -> connector -> repo root
REPO_ROOT = Path(__file__).resolve().parents[2]
load_dotenv(REPO_ROOT / ".env")


def _int(name, default):
    try:
        return int(str(os.getenv(name, default)).strip())
    except (TypeError, ValueError):
        return default


def _float(name, default):
    try:
        return float(str(os.getenv(name, default)).strip())
    except (TypeError, ValueError):
        return default


DB_CONFIG = {
    "host": os.getenv("DB_HOST", "127.0.0.1"),
    "port": _int("DB_PORT", 3306),
    "database": os.getenv("DB_NAME", "ams"),
    "user": os.getenv("DB_USER", "root"),
    "password": os.getenv("DB_PASS", ""),
}

LINE_TOKEN = os.getenv("LINE_CHANNEL_TOKEN")
CHAT_LINE_TOKEN = os.getenv("LINE_ROOM_TOKEN")

# Where readings are POSTed. Was hard-coded in two pollers.
API_URL = os.getenv("EMS_API_URL", "http://localhost/ems/config/meter-data.php")
API_TIMEOUT = _float("EMS_API_TIMEOUT", 5.0)

# Poll cadence. The loop targets this as a period, not as a trailing sleep, so
# reading time doesn't push each cycle progressively later.
POLL_INTERVAL = _int("EMS_POLL_INTERVAL", 60)

MAX_THREADS = _int("EMS_MAX_THREADS", 15)
DELAY_BETWEEN_READ = _float("EMS_DELAY_BETWEEN_READ", 0.05)

# Separate timeouts: a TCP gateway tolerates a longer wait than an RS485 bus,
# where a slow unit-id blocks every other meter sharing the port.
TCP_TIMEOUT = _float("EMS_TCP_TIMEOUT", 3.0)
RTU_TIMEOUT = _float("EMS_RTU_TIMEOUT", 1.0)

# Hours at which the daily LINE connectivity report is pushed.
REPORT_HOURS = [
    int(x) for x in os.getenv("EMS_REPORT_HOURS", "6,18").split(",") if x.strip().isdigit()
]
LINE_TIMEOUT = _float("EMS_LINE_TIMEOUT", 10.0)

# How many readings to hold when meter-data.php is unreachable, before the
# oldest are dropped. Bounded so a long Apache outage can't exhaust memory.
RETRY_QUEUE_MAX = _int("EMS_RETRY_QUEUE_MAX", 500)

LOG_DIR = REPO_ROOT / "connector" / "logs"
