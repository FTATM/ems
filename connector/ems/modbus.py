"""Modbus client construction, field coercion and word decoding.

pymodbus 2.x API (`pymodbus.client.sync`, `unit=`). 3.x moved these modules and
renamed the kwarg to `slave=`; requirements.txt pins 2.5.3 accordingly.
"""

import logging
import struct
import threading

from pymodbus.client.sync import ModbusSerialClient, ModbusTcpClient
from pymodbus.exceptions import ConnectionException

from . import settings

log = logging.getLogger(__name__)

# One RS485 bus is shared by every meter on the same serial port (they differ
# only by slave_id), so reads must be serialised per port — two threads holding
# the port open at once corrupt each other's frames.
_serial_port_locks = {}
_serial_port_locks_guard = threading.Lock()


def _port_key(port_name):
    # "COM3" and "com3" are the same physical port. Keying the lock on the raw
    # string handed them separate locks and let them collide on the bus.
    return str(port_name).strip().upper()


def get_serial_port_lock(port_name):
    key = _port_key(port_name)
    with _serial_port_locks_guard:
        return _serial_port_locks.setdefault(key, threading.Lock())


def hex_to_float(hex_val):
    return struct.unpack("<f", struct.pack("<I", hex_val))[0]


def normalize_parity(value):
    """Map DB parity strings to the single-char code pymodbus expects.

    The `parily` column defaults to the literal string 'node' and the UI field
    is free text, so anything can land here. pyserial rejects unknown values
    outright, which is why RS485 test-connections used to fail on a meter the
    poller could read perfectly well.
    """
    v = str(value or "").strip().lower()
    if v in ("e", "even"):
        return "E"
    if v in ("o", "odd"):
        return "O"
    return "N"  # 'n', 'none', the stored default 'node', and anything unrecognised


def to_int(value, default=None):
    try:
        return int(str(value).strip())
    except (TypeError, ValueError):
        return default


def decode(regs, encoding, scale):
    """Turn raw 16-bit words into a number, or None if they can't be decoded."""
    if not regs:
        return None
    if encoding == "float32_be":
        if len(regs) < 2:
            return None
        value = hex_to_float((regs[0] << 16) + regs[1])
    elif encoding == "float32_le":
        # Same float, words swapped — some brands publish it this way round.
        if len(regs) < 2:
            return None
        value = hex_to_float((regs[1] << 16) + regs[0])
    elif encoding == "uint16":
        value = regs[0]
    elif encoding == "int16":
        value = struct.unpack("<h", struct.pack("<H", regs[0]))[0]
    elif encoding == "uint32_be":
        if len(regs) < 2:
            return None
        value = (regs[0] << 16) + regs[1]
    else:
        log.warning("Unknown encoding %r, decoding as float32_be", encoding)
        if len(regs) < 2:
            return None
        value = hex_to_float((regs[0] << 16) + regs[1])
    return round(value * scale, 2)


def encode(value, encoding, scale, word_count=2):
    """Inverse of decode(): turn a number into the raw words a meter would
    return. Used by the simulator so test data goes through exactly the same
    word-order and scaling rules as a real reading."""
    raw = value / scale if scale else value

    if encoding in ("float32_be", "float32_le"):
        bits = struct.unpack("<I", struct.pack("<f", raw))[0]
        hi, lo = (bits >> 16) & 0xFFFF, bits & 0xFFFF
        return [hi, lo] if encoding == "float32_be" else [lo, hi]
    if encoding == "uint32_be":
        bits = int(raw) & 0xFFFFFFFF
        return [(bits >> 16) & 0xFFFF, bits & 0xFFFF]
    if encoding == "uint16":
        return [int(raw) & 0xFFFF]
    if encoding == "int16":
        return [struct.unpack("<H", struct.pack("<h", int(raw)))[0]]

    log.warning("Unknown encoding %r, encoding as float32_be", encoding)
    bits = struct.unpack("<I", struct.pack("<f", raw))[0]
    return [(bits >> 16) & 0xFFFF, bits & 0xFFFF]


def validate_tcp_fields(meter):
    """Returns (ok, fields, error_message).

    The old TCP path had no validation at all: a blank ip_address raised deep
    inside the read and surfaced as a generic "Thread crash" that wasn't even
    counted as a connection failure.
    """
    ip = str(meter.get("ip_address") or "").strip()
    if not ip:
        return False, {}, "blank ip_address"

    fields = {
        "ip_address": ip,
        "port": to_int(meter.get("port")),
        "slave_id": to_int(meter.get("slave_id")),
        "quality": to_int(meter.get("quality")),
        "address": to_int(meter.get("address"), default=0),
    }
    missing = [n for n in ("port", "slave_id", "quality") if fields[n] is None]
    if missing:
        return False, {}, "invalid/missing fields: " + ", ".join(missing)
    return True, fields, None


def validate_rs485_fields(meter):
    """Returns (ok, fields, error_message)."""
    serial_port = str(meter.get("serial_port") or "").strip()
    if not serial_port:
        return False, {}, "blank serial_port"

    fields = {
        "serial_port": serial_port,
        "baudrate": to_int(meter.get("buad_rate")),
        "databits": to_int(meter.get("data_bits")),
        "stopbits": to_int(meter.get("stop_bits")),
        "slave_id": to_int(meter.get("slave_id")),
        "quality": to_int(meter.get("quality")),
        "address": to_int(meter.get("address"), default=0),
    }
    missing = [
        n for n in ("baudrate", "databits", "stopbits", "slave_id", "quality")
        if fields[n] is None
    ]
    if missing:
        return False, {}, "invalid/missing fields: " + ", ".join(missing)

    fields["parity"] = normalize_parity(meter.get("parily"))
    return True, fields, None


def make_tcp_client(fields):
    return ModbusTcpClient(
        fields["ip_address"], fields["port"], timeout=settings.TCP_TIMEOUT
    )


def make_rtu_client(fields):
    return ModbusSerialClient(
        method="rtu",
        port=fields["serial_port"],
        baudrate=fields["baudrate"],
        bytesize=fields["databits"],
        parity=fields["parity"],
        stopbits=fields["stopbits"],
        timeout=settings.RTU_TIMEOUT,
    )


def read_registers(client, address, count, slave_id, function_code=3):
    """Read `count` registers, or None if the read failed.

    function_code 3 reads holding registers and 4 reads input registers; which
    one a meter answers on is a property of the model, so it comes from the
    register map rather than being assumed.

    Never raises: a dead register on one meter must not abort the cycle.
    """
    try:
        if function_code == 4:
            result = client.read_input_registers(
                address=address, count=count, unit=slave_id
            )
        else:
            result = client.read_holding_registers(
                address=address, count=count, unit=slave_id
            )
        if result.isError():
            return None
        return result.registers
    except ConnectionException as exc:
        log.debug("Connection lost reading %s: %s", address, exc)
        return None
    except Exception as exc:
        log.debug("Error reading %s: %s", address, exc)
        return None
