"""A fake Modbus TCP meter, for testing a model's register map before real
hardware is on site.

Serves whatever register_map rows are passed in, encoding live-looking values
with exactly the same word-order/scale rules read_meter() decodes with — so a
green Test button here means the map itself is consistent, not that it matches
real hardware. When a physical meter arrives, its readings must still be
checked against the datasheet.
"""

import logging
import random
import threading
import time

from pymodbus.datastore import (ModbusSequentialDataBlock, ModbusServerContext,
                                 ModbusSlaveContext)
from pymodbus.server.sync import StartTcpServer

from . import modbus

log = logging.getLogger(__name__)

# Plausible live ranges, keyed by data_type.name. Only cosmetic — picked so a
# human watching the numbers sees something meter-shaped, not a claim about
# any real device's output.
_RANGES = {
    "Flow": (0.5, 15.0),
    "Velocity": (0.05, 2.0),
    "kW": (5, 80), "kVA": (5, 100), "kVAR": (1, 30),
    "Voltage A-N": (215, 235), "Voltage B-N": (215, 235), "Voltage C-N": (215, 235),
    "Voltage A_B": (370, 400), "Voltage B_C": (370, 400), "Voltage C_A": (370, 400),
    "Current A": (2, 40), "Current B": (2, 40), "Current C": (2, 40), "Current avg": (2, 40),
    "Pf": (0.85, 0.99), "Frequency": (49.8, 50.2),
}
# Counters only ever go up, like a real meter's totalizer.
_COUNTERS_UP = {"Positive Cumulative", "kWh", "kVAh", "kVARh"}
_COUNTERS_UP_SLOW = {"Negative Cumulative"}


def _initial_value(name):
    if name in _COUNTERS_UP:
        return random.uniform(1000, 5000)
    if name in _COUNTERS_UP_SLOW:
        return random.uniform(5, 50)
    lo, hi = _RANGES.get(name, (1, 100))
    return random.uniform(lo, hi)


def _step(name, current):
    if name in _COUNTERS_UP:
        return current + random.uniform(0.01, 0.2)
    if name in _COUNTERS_UP_SLOW:
        return current + random.uniform(0, 0.01)
    lo, hi = _RANGES.get(name, (1, 100))
    span = hi - lo
    return min(hi, max(lo, current + random.uniform(-span * 0.05, span * 0.05)))


def run(register_rows, host="127.0.0.1", port=5020, slave_id=1, interval=2.0):
    """Block forever, serving register_rows over Modbus TCP.

    register_rows: list of registers.Register (name, register, word_count,
    function_code, encoding, scale) — normally registers.load_map(db)[model_id].
    """
    if not register_rows:
        raise SystemExit(
            "This model has no register map yet — add rows on the "
            "Meter models page before simulating it."
        )

    span = max(r.register + r.word_count for r in register_rows) + 4
    hr_block = ModbusSequentialDataBlock(0, [0] * span)
    ir_block = ModbusSequentialDataBlock(0, [0] * span)
    store = ModbusSlaveContext(hr=hr_block, ir=ir_block, zero_mode=True)
    context = ModbusServerContext(slaves={slave_id: store}, single=False)

    values = {r.name: _initial_value(r.name) for r in register_rows}

    def updater():
        while True:
            for r in register_rows:
                values[r.name] = _step(r.name, values[r.name])
                words = modbus.encode(values[r.name], r.encoding, r.scale, r.word_count)
                block = hr_block if r.function_code != 4 else ir_block
                block.setValues(r.register, words)
            time.sleep(interval)

    threading.Thread(target=updater, daemon=True).start()

    print("Simulating {} register(s) on {}:{}, slave id {}".format(
        len(register_rows), host, port, slave_id))
    for r in sorted(register_rows, key=lambda r: r.register):
        print("  {:<24} reg {:<6} FC{} {} x{}".format(
            r.name, r.register, r.function_code, r.encoding, r.scale))
    print("Ctrl+C to stop.")

    StartTcpServer(context, address=(host, port))
