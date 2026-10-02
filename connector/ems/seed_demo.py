"""Demo meters with a month of minute-by-minute history.

For showcasing and testing the dashboards, reports, gauges and billing pages
without hardware: two meters for each of electrical-active, electrical-inactive,
water-active and water-inactive, every one of them carrying readings.

Inactive meters get history too on purpose, but where it shows is limited by the
app: fetch-meters-filter.php and fetch-meter-filterById.php only serve
is_active = 1 meters, so the meter pickers and chart/report data never include
an inactive one. It is visible where the realtime endpoint and the management
pages list every meter.

Dashboards default to "last 1 hour" counted back from now, so seeded history
alone goes blank after an hour. `follow()` keeps the active meters current.

The values are shaped like a real site rather than uniform noise: daily and
weekly load cycles, kVA/kVAR/Pf/current that agree with kW and voltage, and
counters (kWh, kVAh, kVARh, Positive/Negative Cumulative) that only ever rise —
billing subtracts two counter readings, so a counter that wobbles would produce
negative usage.

Rows are written straight to meter_data rather than through
config/meter-data.php: that endpoint is one HTTP request per minute per meter,
which for a month is ~350k requests.
"""

import logging
import math
import random
import time
from datetime import datetime, timedelta
from itertools import islice

log = logging.getLogger(__name__)

# 4000 rows is ~160 KB of SQL; XAMPP ships max_allowed_packet at 1 MB.
BATCH_ROWS = 4000
_SQRT3 = math.sqrt(3)

# Load shapes as (hour, fraction of peak) control points, linearly interpolated.
# "wd" is Mon-Fri, "we" is Sat-Sun.
_SHAPES = {
    "office": {
        "wd": [(0, .05), (6, .05), (8, .55), (9, .9), (11.5, 1.0), (12, .7), (13, .7),
               (13.5, .95), (17.5, .9), (19, .15), (21, .06), (24, .05)],
        "we": [(0, .05), (9, .05), (11, .15), (15, .15), (17, .05), (24, .05)],
    },
    "shop": {
        "wd": [(0, .08), (8, .08), (10, .6), (14, .75), (18, 1.0), (21, .9),
               (22, .3), (23, .1), (24, .08)],
        "we": [(0, .08), (8, .08), (10, .7), (14, .9), (18, 1.0), (21, .95),
               (22, .3), (23, .1), (24, .08)],
    },
    "workshop": {
        "wd": [(0, .06), (7, .06), (8, .7), (12, .95), (12.5, .3), (13, .3),
               (13.5, .95), (17, .9), (17.5, .1), (24, .06)],
        "we": [(0, .05), (24, .05)],
    },
    "home": {
        "wd": [(0, .15), (5, .12), (6.5, .5), (8, .35), (10, .2), (16, .25),
               (18, .7), (20, 1.0), (22.5, .7), (24, .18)],
        "we": [(0, .15), (6, .1), (9, .5), (12, .6), (15, .5), (18, .8),
               (20, 1.0), (22.5, .7), (24, .18)],
    },
}

# Names are exact-matched when re-seeding or purging, so nothing but these rows
# is ever touched. Ports are one per meter on loopback: start
#   python -m ems simulate --model-id N --port P
# and the Test button works against that meter. Nothing listens there by
# default, which is also a fair demo of a meter that fails its test.
# Sized like one dorm room, not a building main, because the room/billing pages
# attach these to rooms: ~1 kW average is a few hundred kWh a month, and water
# is a few m3 a month. Building-scale numbers produced absurd bills (tens of
# thousands of baht of water for a studio).
DEMO_METERS = [
    dict(name="DEMO Elec Active 01", type=1, active=1, port=5021, seed=101,
         shape="office", base=0.25, peak=2.4, start=1850.40,
         imbalance=(1.04, 0.97, 0.99), spike_p=0.0015),
    dict(name="DEMO Elec Active 02", type=1, active=1, port=5022, seed=102,
         shape="shop", base=0.20, peak=2.0, start=940.10,
         imbalance=(0.98, 1.05, 0.97), spike_p=0.0015),
    dict(name="DEMO Elec Inactive 01", type=1, active=0, port=5023, seed=103,
         shape="workshop", base=0.15, peak=2.6, start=3120.75,
         imbalance=(1.06, 0.96, 0.98), spike_p=0.004),
    dict(name="DEMO Elec Inactive 02", type=1, active=0, port=5024, seed=104,
         shape="home", base=0.15, peak=1.8, start=610.25,
         imbalance=(1.02, 1.00, 0.98), spike_p=0.002),
    # Water: flow is m3/h and arrives in short draws (a shower, a tap) at a
    # per-minute start probability of draw_p x the daily shape; counters are m3.
    dict(name="DEMO Water Active 01", type=2, active=1, port=5031, seed=201,
         shape="home", draw_p=0.020, pipe_mm=15, start=312.40, start_neg=0.00),
    dict(name="DEMO Water Active 02", type=2, active=1, port=5032, seed=202,
         shape="home", draw_p=0.030, pipe_mm=15, start=145.75, start_neg=0.00),
    dict(name="DEMO Water Inactive 01", type=2, active=0, port=5033, seed=203,
         shape="office", draw_p=0.015, pipe_mm=15, start=88.20, start_neg=0.00),
    dict(name="DEMO Water Inactive 02", type=2, active=0, port=5034, seed=204,
         shape="shop", draw_p=0.012, pipe_mm=20, start=421.10, start_neg=0.35),
]

ELECTRICAL_TYPES = [
    "kW", "kWh", "kVA", "kVAh", "kVAR", "kVARh",
    "Voltage A-N", "Voltage B-N", "Voltage C-N",
    "Voltage A_B", "Voltage B_C", "Voltage C_A",
    "Current A", "Current B", "Current C", "Current avg", "Pf", "Frequency",
]
WATER_TYPES = ["Flow", "Velocity", "Positive Cumulative", "Negative Cumulative"]


def _shape(name, ts):
    pts = _SHAPES[name]["we" if ts.weekday() >= 5 else "wd"]
    hour = ts.hour + ts.minute / 60.0
    for (h0, f0), (h1, f1) in zip(pts, pts[1:]):
        if hour <= h1:
            return f0 + (f1 - f0) * (hour - h0) / (h1 - h0)
    return pts[-1][1]


def _electrical(spec, points, start):
    """Yield (timestamp, {data_type name: value}) once a minute."""
    rng = random.Random(spec["seed"])
    base, peak = spec["base"], spec["peak"]
    mean = sum(spec["imbalance"]) / 3
    imbalance = [x / mean for x in spec["imbalance"]]

    kwh = spec["start"]
    kvah = kwh * 1.10
    kvarh = kwh * 0.35
    day_factor = {}
    noise = vdrift = fdrift = 0.0
    spike_left = 0
    spike_kw = 0.0

    for i in range(points):
        ts = start + timedelta(minutes=i)
        # A busier or quieter day than its neighbours, so the daily chart isn't
        # the same curve stamped 30 times.
        day = day_factor.setdefault(ts.date(), rng.uniform(0.9, 1.1))

        noise = 0.92 * noise + rng.gauss(0, 0.025)
        if spike_left:
            spike_left -= 1
        elif rng.random() < spec["spike_p"]:
            spike_left = rng.randint(3, 15)
            spike_kw = rng.uniform(0.15, 0.35) * peak
        level = base + (peak - base) * _shape(spec["shape"], ts) * day
        kw = max(level * (1 + noise) + (spike_kw if spike_left else 0.0), 0.12)

        pf = min(0.995, max(0.80, 0.86 + 0.11 * min(kw / peak, 1.0) + rng.gauss(0, 0.006)))
        kva = kw / pf
        kvar = math.sqrt(max(kva * kva - kw * kw, 0.0))

        # Grid voltage wanders slowly, and sags a little as the load rises.
        vdrift = 0.995 * vdrift + rng.gauss(0, 0.15)
        sag = 1.8 * min(kw / peak, 1.2)
        v_ln = [min(241.0, max(218.0, 231.0 + vdrift - sag + off + rng.gauss(0, 0.25)))
                for off in (0.0, -0.7, 0.5)]
        v_ll = [_SQRT3 * (v_ln[a] + v_ln[b]) / 2 for a, b in ((0, 1), (1, 2), (2, 0))]

        i_avg = kva * 1000 / (_SQRT3 * sum(v_ll) / 3)
        amps = [i_avg * f * (1 + rng.gauss(0, 0.008)) for f in imbalance]

        fdrift = 0.98 * fdrift + rng.gauss(0, 0.012)
        freq = min(50.15, max(49.85, 50.0 + fdrift))

        kwh += kw / 60
        kvah += kva / 60
        kvarh += kvar / 60

        yield ts, {
            "kW": kw, "kWh": kwh, "kVA": kva, "kVAh": kvah, "kVAR": kvar, "kVARh": kvarh,
            "Voltage A-N": v_ln[0], "Voltage B-N": v_ln[1], "Voltage C-N": v_ln[2],
            "Voltage A_B": v_ll[0], "Voltage B_C": v_ll[1], "Voltage C_A": v_ll[2],
            "Current A": amps[0], "Current B": amps[1], "Current C": amps[2],
            "Current avg": sum(amps) / 3, "Pf": pf, "Frequency": freq,
        }


def _water(spec, points, start):
    rng = random.Random(spec["seed"])
    area = math.pi * (spec["pipe_mm"] / 1000.0 / 2) ** 2

    positive = spec["start"]
    negative = spec["start_neg"]
    day_factor = {}
    draw_left = 0
    draw_flow = 0.0

    for i in range(points):
        ts = start + timedelta(minutes=i)
        day = day_factor.setdefault(ts.date(), rng.uniform(0.7, 1.3))

        if draw_left:
            draw_left -= 1
        elif rng.random() < spec["draw_p"] * _shape(spec["shape"], ts) * day:
            draw_left = rng.randint(1, 8)
            draw_flow = rng.uniform(0.15, 0.5)
        flow = draw_flow * (1 + rng.gauss(0, 0.06)) if draw_left else 0.0

        # Rare short backflow (a valve closing): the meter reads no forward
        # flow and the negative totalizer creeps up.
        if rng.random() < 0.0001:
            negative += rng.uniform(0.01, 0.03)
            flow = 0.0
        flow = max(flow, 0.0)

        positive += flow / 60
        yield ts, {
            "Flow": flow,
            "Velocity": flow / 3600 / area,
            "Positive Cumulative": positive,
            "Negative Cumulative": negative,
        }


def _generator(spec):
    return _electrical if spec["type"] == 1 else _water


def _rows(meter_id, series, type_ids):
    for ts, values in series:
        stamp = ts.strftime("%Y-%m-%d %H:%M:%S")
        for name, value in values.items():
            yield (meter_id, stamp, type_ids[name], round(value, 2))


def _insert_batches(conn, rows):
    cursor = conn.cursor()
    total = 0
    try:
        while True:
            batch = list(islice(rows, BATCH_ROWS))
            if not batch:
                break
            cursor.executemany(
                "INSERT INTO meter_data (meter_id, create_date, type_value_id, value) "
                "VALUES (%s, %s, %s, %s)",
                batch,
            )
            conn.commit()
            total += len(batch)
    finally:
        cursor.close()
    return total


def _delete_data(conn, meter_id):
    """Chunked so a re-seed doesn't hold one giant transaction open."""
    cursor = conn.cursor()
    try:
        while True:
            cursor.execute("DELETE FROM meter_data WHERE meter_id = %s LIMIT 100000", (meter_id,))
            conn.commit()
            if cursor.rowcount == 0:
                return
    finally:
        cursor.close()


def _default_group(db):
    rows = db.query(
        "SELECT id FROM `groups` WHERE is_deleted = 0 ORDER BY is_active DESC, id LIMIT 1"
    )
    if not rows:
        raise SystemExit("No group exists to put the demo meters in; create one first.")
    return rows[0]["id"]


def _model_for(db, meter_type_id):
    """Lowest-id live model of this type that actually has a register map."""
    rows = db.query(
        "SELECT mm.id FROM meter_model mm WHERE mm.is_deleted = 0 AND mm.meter_type_id = %s "
        "AND EXISTS (SELECT 1 FROM register_map rm WHERE rm.model_id = mm.id AND rm.is_deleted = 0) "
        "ORDER BY mm.id LIMIT 1",
        (meter_type_id,),
    )
    return rows[0]["id"] if rows else None


def _existing_meter(db, name):
    rows = db.query("SELECT id FROM meter WHERE name = %s ORDER BY is_deleted, id LIMIT 1", (name,))
    return rows[0]["id"] if rows else None


def _ensure_meter(db, spec, group_id, models):
    """Reuse the demo meter's row if it exists (ids stay stable across re-seeds)."""
    meter_id = _existing_meter(db, spec["name"])
    if meter_id is not None:
        db.execute(
            "UPDATE meter SET is_active = %s, is_deleted = 0 WHERE id = %s",
            (spec["active"], meter_id),
        )
        return meter_id
    conn = db.connection()
    cursor = conn.cursor()
    try:
        # address is the register offset added to every mapped address: 0.
        cursor.execute(
            "INSERT INTO meter (name, is_active, group_id, meter_type_id, model_id, protocol, dns, "
            "port, ip_address, submask, serial_port, buad_rate, data_bits, parily, stop_bits, "
            "slave_id, address, quality, x, y, z, is_deleted) "
            "VALUES (%s, %s, %s, %s, %s, 'tcp', '', %s, '127.0.0.1', '255.255.255.0', '', '', 0, "
            "'node', 0, 1, '0', 0, 0, 0, 0, 0)",
            (spec["name"], spec["active"], group_id, spec["type"], models[spec["type"]], spec["port"]),
        )
        conn.commit()
        return cursor.lastrowid
    finally:
        cursor.close()


def purge(db):
    """Remove the demo meters and every reading they hold."""
    conn = db.connection()
    for spec in DEMO_METERS:
        while True:
            meter_id = _existing_meter(db, spec["name"])
            if meter_id is None:
                break
            _delete_data(conn, meter_id)
            db.execute("DELETE FROM meter WHERE id = %s", (meter_id,))
            log.info("Removed %s (id %s)", spec["name"], meter_id)


def seed(db, days=30, group_id=None):
    """Create/refresh the demo meters and regenerate `days` of 1-minute history.

    Ends at the current minute, so the newest reading is "now". Re-running
    replaces the demo meters' rows only; nothing else in meter_data is touched.
    """
    points = days * 24 * 60
    end_ts = datetime.now().replace(second=0, microsecond=0)
    start_ts = end_ts - timedelta(minutes=points - 1)

    type_ids = {r["name"]: r["id"] for r in db.query(
        "SELECT id, name FROM data_type WHERE is_deleted = 0")}
    missing = [n for n in ELECTRICAL_TYPES + WATER_TYPES if n not in type_ids]
    if missing:
        raise SystemExit("data_type is missing: {} (see sql/schema_changes.sql)".format(", ".join(missing)))

    group_id = group_id or _default_group(db)
    models = {1: _model_for(db, 1), 2: _model_for(db, 2)}
    for type_id, model_id in models.items():
        if model_id is None:
            log.warning("No meter model with a register map for meter type %s; "
                        "those demo meters get model_id NULL and the Test button will skip them", type_id)

    log.info("Seeding %d demo meters into group %s: %s -> %s (%d readings each)",
             len(DEMO_METERS), group_id, start_ts, end_ts, points)

    conn = db.connection()
    grand_total = 0
    for spec in DEMO_METERS:
        began = time.time()
        meter_id = _ensure_meter(db, spec, group_id, models)
        _delete_data(conn, meter_id)
        series = _generator(spec)(spec, points, start_ts)
        written = _insert_batches(conn, _rows(meter_id, series, type_ids))
        grand_total += written
        log.info("%-24s id %-4s %-8s %9d rows in %.0fs", spec["name"], meter_id,
                 "active" if spec["active"] else "inactive", written, time.time() - began)
    log.info("Done: %d rows.", grand_total)
    return grand_total


def _resume(db, spec, meter_id):
    """Rebuild a meter's generator and fast-forward it past what is stored.

    The series is a pure function of (spec, first timestamp), so replaying from
    the first stored minute lands on exactly the value a longer seed would have
    produced next — counters carry on from the history instead of jumping.
    Returns (series, next_item, last_stored) or None if the meter has no rows.
    """
    row = db.query(
        "SELECT MIN(create_date) AS first, MAX(create_date) AS last FROM meter_data WHERE meter_id = %s",
        (meter_id,),
    )[0]
    if row["first"] is None:
        return None
    series = _generator(spec)(spec, 10 ** 9, row["first"])
    pending = next(series)
    while pending[0] <= row["last"]:
        pending = next(series)
    return series, pending, row["last"]


def follow(db):
    """Keep the *active* demo meters current: backfill to now, then one reading a minute.

    Needed because the dashboards default to "last 1 hour" counted back from
    now — seeded history alone goes blank an hour after it was written. Only
    meters whose is_active is 1 *in the database* are fed, so an inactive meter
    stays frozen at its last reading, as a deactivated real one would, and
    switching one on in the UI starts it filling (from where it stopped).
    Runs until interrupted; never regenerates history. Stop it before re-seeding.
    """
    type_ids = {r["name"]: r["id"] for r in db.query(
        "SELECT id, name FROM data_type WHERE is_deleted = 0")}
    feeds = {}  # meter name -> [series, pending, last_written]
    log.info("Following active demo meters (Ctrl+C to stop)")

    while True:
        end = datetime.now().replace(second=0, microsecond=0)
        written = 0
        for spec in DEMO_METERS:
            row = db.query("SELECT id, is_active FROM meter WHERE name = %s AND is_deleted = 0", (spec["name"],))
            if not row:
                raise SystemExit("{} not found; run `python -m ems seed-demo` first.".format(spec["name"]))
            meter_id = row[0]["id"]
            if not row[0]["is_active"]:
                feeds.pop(spec["name"], None)
                continue

            stored = db.query("SELECT MAX(create_date) AS last FROM meter_data WHERE meter_id = %s", (meter_id,))[0]["last"]
            feed = feeds.get(spec["name"])
            if feed is None or feed[2] != stored:
                # First tick, or someone re-seeded/deleted underneath us.
                resumed = _resume(db, spec, meter_id)
                if resumed is None:
                    raise SystemExit("{} has no readings; run `python -m ems seed-demo` first.".format(spec["name"]))
                feed = feeds[spec["name"]] = list(resumed)

            batch = []
            while feed[1][0] <= end:
                batch.append(feed[1])
                feed[2] = feed[1][0]
                feed[1] = next(feed[0])
            if batch:
                written += _insert_batches(db.connection(), _rows(meter_id, batch, type_ids))

        if written:
            log.info("Appended %d rows, current through %s", written, end)
        time.sleep(61 - datetime.now().second)
