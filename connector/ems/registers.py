"""The Modbus register map, loaded from the `register_map` table.

This used to be a dict literal copy-pasted into four Python files (and a fifth
copy in config/simulate-data.php), covering one brand of electrical meter only.

The map belongs to a meter *model*, not to a meter type: two brands of
electrical meter put the same measurement at different registers, in different
word orders, at different scales, and some answer on FC4 rather than FC3.
Adding a brand means inserting rows, not editing code.
"""

import logging
from collections import namedtuple

log = logging.getLogger(__name__)

# name is data_type.name — the exact string config/meter-data.php matches
# against to resolve type_value_id, so it is the contract with the DB.
Register = namedtuple("Register", "name register word_count function_code encoding scale")

_SQL = """
    SELECT rm.model_id, dt.name, rm.register, rm.word_count, rm.function_code,
           rm.encoding, rm.scale
    FROM register_map rm
    JOIN data_type dt ON dt.id = rm.data_type_id
    JOIN meter_model mm ON mm.id = rm.model_id
    WHERE rm.is_deleted = 0 AND dt.is_deleted = 0 AND mm.is_deleted = 0
    ORDER BY rm.model_id, rm.register
"""


def load_map(db):
    """Return {model_id: [Register, ...]} for every configured model."""
    by_model = {}
    for row in db.query(_SQL):
        by_model.setdefault(row["model_id"], []).append(
            Register(
                name=row["name"],
                register=int(row["register"]),
                word_count=int(row["word_count"]),
                function_code=int(row["function_code"]),
                encoding=str(row["encoding"]),
                scale=float(row["scale"]),
            )
        )
    return by_model


def for_meter(register_map, meter):
    """Registers for this meter's model.

    Returns (registers, problem). `problem` is a human-readable reason when the
    meter cannot be read at all, so the caller can report *why* rather than
    silently falling back to another model's addresses — which would produce
    confident, wrong numbers.
    """
    model_id = meter.get("model_id")
    if not model_id:
        return [], "no meter model assigned"

    registers = register_map.get(model_id)
    if not registers:
        return [], "meter model {} has no register map".format(model_id)

    return registers, None
