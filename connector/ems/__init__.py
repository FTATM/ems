"""EMS meter collector.

One package, two entry points, one code path:

    python -m ems run                 continuous poller (the Windows service)
    python -m ems test --meter-id 7   single read, JSON out (the Test button)

Both read their configuration from the `meter` table and their register map
from the `register_map` table, so a meter that tests OK is a meter the service
can poll.
"""

__version__ = "2.0.0"
