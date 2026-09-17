"""Pushing readings into the app via config/meter-data.php.

Improvements over the old inline POST:
  - one requests.Session, instead of a fresh TCP connection per meter per cycle
  - the response body is actually inspected; previously only the status code
    was printed, so a PHP fatal returning HTTP 200 counted as success
  - failed POSTs are queued and retried; they used to be lost for good
"""

import logging
from collections import deque

import requests

from . import settings

log = logging.getLogger(__name__)


class Ingestor:
    def __init__(self, url=None, timeout=None, queue_max=None):
        self.url = url or settings.API_URL
        self.timeout = timeout or settings.API_TIMEOUT
        self._session = requests.Session()
        # Bounded: a long Apache outage must not grow until the process dies.
        self._retry = deque(maxlen=queue_max or settings.RETRY_QUEUE_MAX)

    def _post(self, payload):
        """Returns (ok, detail)."""
        try:
            response = self._session.post(self.url, json=payload, timeout=self.timeout)
        except Exception as exc:
            return False, str(exc)

        if response.status_code != 200:
            return False, "HTTP {}".format(response.status_code)

        # meter-data.php answers {"success":bool,"inserted":N,"errors":N}.
        # Anything else means PHP emitted a warning or fatal ahead of the JSON.
        try:
            body = response.json()
        except ValueError:
            return False, "non-JSON response: {}".format(response.text[:200])

        if not body.get("success"):
            return False, "api rejected: {}".format(body.get("message", body))
        if body.get("errors"):
            log.warning(
                "Meter %s: %s value(s) failed to insert",
                payload.get("meter_id"), body["errors"],
            )
        return True, body

    def send(self, meter_id, timestamp, readings):
        """POST one meter's readings, queueing for retry on failure."""
        if not readings:
            return True  # nothing read; meter_health records why

        payload = {
            "meter_id": meter_id,
            # MySQL parses the ISO 'T' separator, but a space is what the
            # DATETIME column expects and avoids strict-mode warnings.
            "datetime": timestamp.strftime("%Y-%m-%d %H:%M:%S"),
            "data": readings,
        }
        ok, detail = self._post(payload)
        if not ok:
            log.error("Ingest failed for meter %s: %s", meter_id, detail)
            self._retry.append(payload)
        return ok

    def flush_retries(self):
        """Re-attempt queued payloads. Called once per cycle."""
        if not self._retry:
            return 0

        pending, self._retry = list(self._retry), deque(maxlen=self._retry.maxlen)
        sent = 0
        for payload in pending:
            ok, detail = self._post(payload)
            if ok:
                sent += 1
            else:
                self._retry.append(payload)
        if sent:
            log.info("Re-sent %s queued reading(s)", sent)
        if self._retry:
            log.warning("%s reading(s) still queued", len(self._retry))
        return sent

    @property
    def queued(self):
        return len(self._retry)

    def close(self):
        self._session.close()
