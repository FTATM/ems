"""Reading one meter, whatever protocol it speaks.

TCP and RS485 used to be separate functions that had drifted apart: only one
applied the meter's register offset, only one validated its fields, and they
used different timeouts by accident rather than by intent. There is one path
here; the protocol only decides how the client is built and whether the bus
needs locking.
"""

import logging
import time
from contextlib import contextmanager

from . import modbus, registers, settings

log = logging.getLogger(__name__)


class MeterReadError(Exception):
    """The meter could not be reached or configured at all."""


@contextmanager
def _client_for(meter):
    """Yield a connected Modbus client plus its coerced fields.

    Raises MeterReadError when the meter's stored configuration is unusable or
    the device does not answer.
    """
    protocol = str(meter.get("protocol") or "").strip().lower()

    if protocol == "tcp":
        ok, fields, err = modbus.validate_tcp_fields(meter)
        if not ok:
            raise MeterReadError(err)
        client = modbus.make_tcp_client(fields)
        if not client.connect():
            raise MeterReadError(
                "connect fail ({}:{})".format(fields["ip_address"], fields["port"])
            )
        try:
            yield client, fields
        finally:
            client.close()

    elif protocol == "rs485":
        ok, fields, err = modbus.validate_rs485_fields(meter)
        if not ok:
            raise MeterReadError(err)
        # Held across the whole read: the port is a shared bus, not a socket.
        with modbus.get_serial_port_lock(fields["serial_port"]):
            client = modbus.make_rtu_client(fields)
            if not client.connect():
                raise MeterReadError("connect fail ({})".format(fields["serial_port"]))
            try:
                yield client, fields
            finally:
                client.close()

    else:
        raise MeterReadError("unknown protocol {!r}".format(meter.get("protocol")))


def read_meter(meter, register_map):
    """Read every mapped register for `meter`.

    Returns (readings, failed_names):
      readings     {data_type name: value} — only registers that actually read
      failed_names [str] — registers that did not respond

    Failed registers are *omitted*, not zero-filled. They previously became the
    string "Error", which config/meter-data.php bound as a double and stored as
    0.00 — a dead register was indistinguishable from a genuine zero reading.

    Raises MeterReadError if the meter itself is unreachable or unmapped.
    """
    regs, problem = registers.for_meter(register_map, meter)
    if problem:
        raise MeterReadError(problem)

    readings = {}
    failed = []

    with _client_for(meter) as (client, fields):
        offset = fields["address"]
        if fields["quality"] and fields["quality"] != regs[0].word_count:
            # `quality` is the legacy per-meter read quantity. word_count on the
            # register map supersedes it; warn instead of changing counts silently.
            log.warning(
                "Meter %s: quality=%s differs from register word_count=%s; using word_count",
                meter.get("id"), fields["quality"], regs[0].word_count,
            )

        for reg in regs:
            words = modbus.read_registers(
                client, reg.register + offset, reg.word_count,
                fields["slave_id"], reg.function_code,
            )
            value = modbus.decode(words, reg.encoding, reg.scale) if words else None
            if value is None:
                failed.append(reg.name)
            else:
                readings[reg.name] = value
            # Meters (especially on RS485) drop frames when polled back-to-back.
            time.sleep(settings.DELAY_BETWEEN_READ)

    return readings, failed


def scan_meter(meter, start, end, step=2, function_codes=(3, 4)):
    """Sweep a register range and report anything that decodes plausibly.

    A hunting aid for a meter model whose datasheet isn't at hand — notably the
    water meters, whose addresses are still unknown. Both word orders are
    reported because which one a brand uses is exactly what you're trying to
    find out. It is up to a human to decide which candidates are real and put
    them in that model's register map.
    """
    found = []
    with _client_for(meter) as (client, fields):
        for function_code in function_codes:
            for address in range(start, end + 1, step):
                words = modbus.read_registers(
                    client, address, 2, fields["slave_id"], function_code
                )
                if not words:
                    continue
                be = modbus.decode(words, "float32_be", 1.0)
                le = modbus.decode(words, "float32_le", 1.0)
                if not _plausible(be) and not _plausible(le):
                    continue
                found.append({
                    "register": address,
                    "function_code": function_code,
                    "float32_be": be if _plausible(be) else None,
                    "float32_le": le if _plausible(le) else None,
                    "raw": words,
                })
                time.sleep(settings.DELAY_BETWEEN_READ)
    return found


def _plausible(value):
    """Reject zeros and magnitudes no physical measurement takes — otherwise a
    sweep of empty registers buries the handful of real ones."""
    if value is None or value == 0.0:
        return False
    return 1e-4 <= abs(value) <= 1e7
