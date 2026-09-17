"""LINE Messaging API push.

Moved from connector/function.py, which had no request timeout — a hung LINE
call stalled the entire polling loop.
"""

import logging

import requests

from . import settings

log = logging.getLogger(__name__)

_PUSH_URL = "https://api.line.me/v2/bot/message/push"


def push(token, to, message):
    """Send one text message. Returns True on success; never raises."""
    if not token or not to:
        log.warning("LINE push skipped: token or recipient not configured")
        return False

    try:
        response = requests.post(
            _PUSH_URL,
            headers={
                "Content-Type": "application/json",
                "Authorization": "Bearer {}".format(token),
            },
            json={"to": to, "messages": [{"type": "text", "text": message}]},
            timeout=settings.LINE_TIMEOUT,
        )
    except Exception as exc:
        log.error("LINE push failed: %s", exc)
        return False

    if response.status_code != 200:
        log.error("LINE push failed: HTTP %s %s", response.status_code, response.text[:200])
        return False
    return True
