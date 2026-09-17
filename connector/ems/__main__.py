"""CLI entry point.

    python -m ems run                        poll forever (the Windows service)
    python -m ems test --meter-id 7          read once, print JSON (Test button)
    python -m ems once [--meter-id 7]        one cycle, storing readings
    python -m ems scan --meter-id 3          sweep registers to find addresses
    python -m ems simulate --model-id 2      serve a fake meter for a model, over Modbus TCP

`test` prints JSON on stdout and nothing else, because config/test-meter.php
parses it. Every other command logs to stderr so the two never mix.
"""

import argparse
import json
import logging
import sys
from datetime import datetime

from . import db as dbmod
from . import reader, registers, simulate
from .runner import Collector


def _setup_logging(level, quiet_stdout=False):
    handler = logging.StreamHandler(sys.stderr if quiet_stdout else sys.stdout)
    handler.setFormatter(
        logging.Formatter("%(asctime)s %(levelname)-7s %(name)s: %(message)s")
    )
    root = logging.getLogger()
    root.handlers[:] = [handler]
    root.setLevel(level)


def _load_meter(db, meter_id):
    meter = dbmod.fetch_meter(db, meter_id)
    if meter is None:
        raise SystemExit("meter {} not found".format(meter_id))
    return meter


def cmd_run(args):
    _setup_logging(logging.DEBUG if args.verbose else logging.INFO)
    Collector().run_forever()
    return 0


def cmd_once(args):
    _setup_logging(logging.DEBUG if args.verbose else logging.INFO)
    collector = Collector()
    try:
        if args.meter_id:
            register_map = registers.load_map(collector.db)
            meter = _load_meter(collector.db, args.meter_id)
            ok = collector.process_meter(meter, register_map)
            return 0 if ok else 1
        collector.run_cycle()
        return 0
    finally:
        collector.close()


def cmd_test(args):
    """Read one meter and report the result as JSON, without storing anything.

    This deliberately shares read_meter() with the service: if the Test button
    says a meter works, the collector can poll it. The old one-shot scripts had
    their own copy of the logic and disagreed — notably they never normalised
    the parity value, so RS485 tests failed on meters the poller could read.
    """
    _setup_logging(logging.WARNING, quiet_stdout=True)

    db = dbmod.Database()
    try:
        meter = dbmod.fetch_meter(db, args.meter_id)
        if meter is None:
            print(json.dumps({
                "success": False,
                "message": "Meter not found.",
                "output": "No meter with id {}".format(args.meter_id),
            }))
            return 1

        register_map = registers.load_map(db)
        try:
            readings, failed = reader.read_meter(meter, register_map)
        except reader.MeterReadError as exc:
            print(json.dumps({
                "success": False,
                "message": "Not found meter.",
                "output": str(exc),
            }))
            return 1

        if not readings:
            print(json.dumps({
                "success": False,
                "message": "Not found meter.",
                "output": "Connected, but no register responded.",
            }))
            return 1

        print(json.dumps({
            "success": True,
            "message": "Found meter.",
            "output": {
                "meter_id": meter["id"],
                "protocol": meter.get("protocol"),
                "datetime": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                "data": readings,
                "failed": failed,
            },
        }, ensure_ascii=False))
        return 0
    except Exception as exc:  # the PHP caller must always get JSON back
        print(json.dumps({
            "success": False,
            "message": "Test failed.",
            "output": str(exc),
        }))
        return 1
    finally:
        db.close()


def cmd_scan(args):
    _setup_logging(logging.INFO)
    db = dbmod.Database()
    try:
        meter = _load_meter(db, args.meter_id)
        print("Scanning registers {}-{} on meter {} ({})".format(
            args.start, args.end, meter["id"], meter.get("name")))
        codes = (args.function_code,) if args.function_code else (3, 4)
        found = reader.scan_meter(meter, args.start, args.end, args.step, codes)
        if not found:
            print("No plausible float values found in that range.")
            return 1
        print("\n{:>9} {:>3}  {:>16}  {:>16}  raw words".format(
            "register", "FC", "float32_be", "float32_le"))
        for item in found:
            print("{:>9} {:>3}  {:>16}  {:>16}  {}".format(
                item["register"], item["function_code"],
                "-" if item["float32_be"] is None else item["float32_be"],
                "-" if item["float32_le"] is None else item["float32_le"],
                item["raw"]))
        print("\n{} candidate(s). Add the ones you recognise to this model's "
              "register map on the Meter models page.".format(len(found)))
        return 0
    finally:
        db.close()


def cmd_simulate(args):
    """Serve a fake meter over Modbus TCP using a model's register map.

    For testing a model — and the rest of the pipeline (Test button, service
    polling, ingest) — before real hardware is available. Point a meter row's
    ip_address/port at this process to exercise it end-to-end.
    """
    _setup_logging(logging.INFO)
    db = dbmod.Database()
    try:
        register_map = registers.load_map(db)
        rows = register_map.get(args.model_id)
        if not rows:
            print("Model {} has no register map. Add rows on the Meter "
                  "models page first.".format(args.model_id))
            return 1
        simulate.run(rows, host=args.host, port=args.port,
                      slave_id=args.slave_id, interval=args.interval)
        return 0
    except KeyboardInterrupt:
        print("\nStopped.")
        return 0
    finally:
        db.close()


def main(argv=None):
    parser = argparse.ArgumentParser(prog="ems", description="EMS meter collector")
    parser.add_argument("-v", "--verbose", action="store_true")
    sub = parser.add_subparsers(dest="command", required=True)

    sub.add_parser("run", help="poll active meters forever")

    once = sub.add_parser("once", help="run a single polling cycle")
    once.add_argument("--meter-id", type=int, help="limit to one meter")

    test = sub.add_parser("test", help="read one meter, print JSON, store nothing")
    test.add_argument("--meter-id", type=int, required=True)

    scan = sub.add_parser("scan", help="sweep a register range looking for values")
    scan.add_argument("--meter-id", type=int, required=True)
    scan.add_argument("--start", type=int, default=0)
    scan.add_argument("--end", type=int, default=4000)
    scan.add_argument("--step", type=int, default=2)
    scan.add_argument("--function-code", type=int, choices=[3, 4],
                      help="limit to holding (3) or input (4) registers; both by default")

    sim = sub.add_parser("simulate", help="serve a fake meter for a model, over Modbus TCP")
    sim.add_argument("--model-id", type=int, required=True)
    sim.add_argument("--host", default="127.0.0.1")
    sim.add_argument("--port", type=int, default=5020)
    sim.add_argument("--slave-id", type=int, default=1)
    sim.add_argument("--interval", type=float, default=2.0,
                      help="seconds between value updates (default: 2)")

    args = parser.parse_args(argv)
    return {
        "run": cmd_run,
        "once": cmd_once,
        "test": cmd_test,
        "scan": cmd_scan,
        "simulate": cmd_simulate,
    }[args.command](args)


if __name__ == "__main__":
    sys.exit(main())
