"""The polling loop that runs as the Windows service."""

import logging
import threading
import time
from concurrent.futures import ThreadPoolExecutor
from datetime import datetime

from . import db as dbmod
from . import ingest, notify, reader, registers, settings, status

log = logging.getLogger(__name__)


class Collector:
    def __init__(self):
        self.db = dbmod.Database()
        # Each worker thread writes its own health row, so it needs its own
        # MySQL handle — mysql-connector connections are not thread-safe.
        self._thread_dbs = {}
        self.ingestor = ingest.Ingestor()
        self._last_report_date = None

    def _worker_db(self):
        key = threading.get_ident()
        if key not in self._thread_dbs:
            self._thread_dbs[key] = dbmod.Database()
        return self._thread_dbs[key]

    def process_meter(self, meter, register_map):
        """Read one meter and record the outcome. Returns True if it answered."""
        meter_id = meter["id"]
        wdb = self._worker_db()
        try:
            readings, failed = reader.read_meter(meter, register_map)
        except reader.MeterReadError as exc:
            log.warning("Meter %s (%s): %s", meter_id, meter.get("name"), exc)
            status.record_failure(wdb, meter_id, exc)
            return False
        except Exception as exc:
            log.exception("Meter %s: unexpected error", meter_id)
            status.record_failure(wdb, meter_id, exc)
            return False

        if not readings:
            message = "no registers responded"
            log.warning("Meter %s (%s): %s", meter_id, meter.get("name"), message)
            status.record_failure(wdb, meter_id, message)
            return False

        self.ingestor.send(meter_id, datetime.now(), readings)
        status.record_ok(wdb, meter_id, failed)
        if failed:
            log.info("Meter %s: %s register(s) did not respond: %s",
                     meter_id, len(failed), ", ".join(failed))
        return True

    def run_cycle(self):
        """One full pass over the active meters. Returns (ok, failed)."""
        started = time.monotonic()

        register_map = registers.load_map(self.db)
        meters = dbmod.fetch_active_meters(self.db)
        if not meters:
            log.info("No active meters to poll")

        ok = failed = 0
        if meters:
            with ThreadPoolExecutor(max_workers=settings.MAX_THREADS) as pool:
                results = pool.map(lambda m: self.process_meter(m, register_map), meters)
                for succeeded in results:
                    if succeeded:
                        ok += 1
                    else:
                        failed += 1

        self.ingestor.flush_retries()

        duration_ms = int((time.monotonic() - started) * 1000)
        status.record_cycle(self.db, duration_ms, ok, failed)
        log.info("Cycle done in %sms — %s ok, %s failed, %s queued",
                 duration_ms, ok, failed, self.ingestor.queued)
        return ok, failed

    def maybe_send_report(self, failed):
        """Push the daily connectivity summary, at most once per hour slot.

        The old check was `hour in [6,18] and minute in [0,4]`, which with a
        drifting 60s+ cycle could match twice in a window or skip it entirely.
        """
        now = datetime.now()
        if now.hour not in settings.REPORT_HOURS:
            return
        slot = (now.date(), now.hour)
        if self._last_report_date == slot:
            return
        self._last_report_date = slot

        if failed > 0:
            message = "⚠️ [Daily Report] พบมิเตอร์ที่ไม่สามารถ CONNECT ได้จำนวน : {} ตัว".format(failed)
        else:
            message = "✅ [Daily Report] มิเตอร์ทุกตัวเชื่อมต่อปกติ"

        if notify.push(settings.LINE_TOKEN, settings.CHAT_LINE_TOKEN, message):
            log.info("Daily report sent at %s", now.strftime("%H:%M"))

    def run_forever(self):
        status.mark_started(self.db)
        log.info("Collector started — polling every %ss", settings.POLL_INTERVAL)

        try:
            while True:
                # Measured from the start of the cycle, so reading time doesn't
                # push each subsequent cycle later and later.
                cycle_start = time.monotonic()
                try:
                    _, failed = self.run_cycle()
                    self.maybe_send_report(failed)
                except Exception:
                    # Never let one bad cycle kill the service; the old loop
                    # could die outright on a NameError in its finally block.
                    log.exception("Cycle failed, continuing")

                elapsed = time.monotonic() - cycle_start
                sleep_for = max(0, settings.POLL_INTERVAL - elapsed)
                if sleep_for == 0:
                    log.warning("Cycle took %.1fs, longer than the %ss interval",
                                elapsed, settings.POLL_INTERVAL)
                time.sleep(sleep_for)
        except KeyboardInterrupt:
            log.info("Stopped by user")
        finally:
            self.close()

    def close(self):
        self.ingestor.close()
        self.db.close()
        for handle in self._thread_dbs.values():
            handle.close()
        self._thread_dbs.clear()
