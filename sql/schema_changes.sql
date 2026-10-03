-- Schema changes for TOR 2.14-2.22 (water module + room/dorm billing module)
-- This app has no migration tool; apply these statements manually, in order,
-- against the `ams` database on any environment that needs them.
-- Each block is idempotent-checked where practical; review before running on prod.

-- ── Phase 0: seed data_type rows needed by the water module (2.18/2.20) ──
-- Only run if these rows don't already exist (check by name first).
INSERT INTO data_type (name, maxvaluecolumn, create_date, is_deleted) VALUES
('Flow', 100, CURDATE(), 0),
('Velocity', 10, CURDATE(), 0),
('Positive Cumulative', 100000, CURDATE(), 0),
('Negative Cumulative', 100000, CURDATE(), 0);

-- ── Phase 8: room billing (2.22) — assign meters to rooms + a flat water rate ──
-- meter.room_id: which room a meter belongs to (NULL = building-level / unassigned).
-- ON DELETE SET NULL so deleting a room never fails/cascades onto meter rows.
ALTER TABLE meter ADD COLUMN room_id INT NULL AFTER group_id;
ALTER TABLE meter ADD CONSTRAINT fk_meter_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL;

-- water_rates: flat (non-tiered) price per unit, time-boxed like ft_rates.
-- end_date NULL = currently in effect.
CREATE TABLE water_rates (
  id INT NOT NULL AUTO_INCREMENT,
  unit_price DECIMAL(10,4) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  is_deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
);
-- Seed an initial rate — adjust unit_price to the real tariff before going live.
INSERT INTO water_rates (unit_price, start_date, end_date, is_deleted) VALUES (18.0000, CURDATE(), NULL, 0);

-- ── Bug fix (found while testing the billing engine): bill/contract/payments.id
-- were plain PRIMARY KEY INT columns with NO AUTO_INCREMENT — every INSERT that
-- didn't supply an explicit id defaulted to 0 and collided on the 2nd insert.
-- This was latent because nothing previously inserted into these tables in bulk.
-- Check MAX(id) on each table first and set the AUTO_INCREMENT starting value
-- above it before running on an environment with existing rows.
ALTER TABLE bill MODIFY id INT NOT NULL AUTO_INCREMENT;
ALTER TABLE contract MODIFY id INT NOT NULL AUTO_INCREMENT;
ALTER TABLE payments MODIFY id INT NOT NULL AUTO_INCREMENT;

-- ── Global theme accent-color preset (small generic key/value settings store) ──
CREATE TABLE IF NOT EXISTS app_settings (
  id INT NOT NULL AUTO_INCREMENT,
  setting_key VARCHAR(50) NOT NULL,
  setting_value VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_setting_key (setting_key)
);
INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('theme_preset', 'green');

-- ── Collector service: DB-driven register map + health/heartbeat ──
-- Previously the Modbus register map was hard-coded in four separate Python
-- files, and only covered electrical meters — water meters (meter_type_id 2)
-- were polled with the electrical map, which can never return valid readings.
-- register_map makes the map data instead of code: one row per value a given
-- meter type exposes.
CREATE TABLE IF NOT EXISTS register_map (
  id INT NOT NULL AUTO_INCREMENT,
  meter_type_id INT NOT NULL,
  data_type_id INT NOT NULL,
  register INT NOT NULL,
  word_count INT NOT NULL DEFAULT 2,
  -- how to decode the words into a number. float32_be = two 16-bit words,
  -- high word first, IEEE-754 single — what every existing meter here uses.
  encoding VARCHAR(20) NOT NULL DEFAULT 'float32_be',
  scale DECIMAL(10,4) NOT NULL DEFAULT 1.0000,
  is_deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_type_data (meter_type_id, data_type_id),
  CONSTRAINT fk_rm_meter_type FOREIGN KEY (meter_type_id) REFERENCES meter_type(id),
  CONSTRAINT fk_rm_data_type FOREIGN KEY (data_type_id) REFERENCES data_type(id)
) ENGINE=InnoDB;

-- Seed the electrical map (meter_type_id 1) from the addresses that were
-- hard-coded in connector/pymodbustcpAllmeters.py. Joined on data_type.name so
-- the ids stay correct regardless of how data_type was seeded.
INSERT IGNORE INTO register_map (meter_type_id, data_type_id, register)
SELECT 1, dt.id, m.reg FROM data_type dt JOIN (
  SELECT 'kW' AS nm, 3059 AS reg UNION ALL
  SELECT 'kWh', 2699 UNION ALL
  SELECT 'kVA', 3075 UNION ALL
  SELECT 'kVAh', 2695 UNION ALL
  SELECT 'kVAR', 3067 UNION ALL
  SELECT 'kVARh', 2687 UNION ALL
  SELECT 'Voltage A-N', 3027 UNION ALL
  SELECT 'Voltage B-N', 3029 UNION ALL
  SELECT 'Voltage C-N', 3031 UNION ALL
  SELECT 'Current A', 2999 UNION ALL
  SELECT 'Current B', 3001 UNION ALL
  SELECT 'Current C', 3003 UNION ALL
  SELECT 'Current avg', 3005 UNION ALL
  SELECT 'Voltage A_B', 3019 UNION ALL
  SELECT 'Voltage B_C', 3021 UNION ALL
  SELECT 'Voltage C_A', 3023 UNION ALL
  SELECT 'Pf', 3191 UNION ALL
  SELECT 'Frequency', 3109
) m ON m.nm = dt.name
WHERE dt.is_deleted = 0;

-- Water meters (meter_type_id 2) have NO rows yet on purpose: the register
-- addresses depend on the meter model's datasheet. Until they are added the
-- collector skips water meters and logs why, rather than reading garbage.
-- Use `python -m ems scan --meter-id <id>` to hunt for the addresses, then:
--   INSERT INTO register_map (meter_type_id, data_type_id, register) VALUES
--     (2, (SELECT id FROM data_type WHERE name='Flow'), <reg>), ... ;

-- collector_status: single-row heartbeat so the web UI can tell whether the
-- collector service is alive without shelling out or scanning the process list.
CREATE TABLE IF NOT EXISTS collector_status (
  id TINYINT(1) NOT NULL DEFAULT 1,
  started_at DATETIME NULL,
  last_cycle_at DATETIME NULL,
  last_cycle_ms INT NULL,
  meters_ok INT NOT NULL DEFAULT 0,
  meters_failed INT NOT NULL DEFAULT 0,
  pid INT NULL,
  host VARCHAR(100) NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;
INSERT IGNORE INTO collector_status (id) VALUES (1);

-- meter_health: per-meter result of the last poll. This is where read failures
-- are recorded now — they used to be written into meter_data as 0.00 values,
-- which was indistinguishable from a genuine zero reading.
CREATE TABLE IF NOT EXISTS meter_health (
  meter_id INT NOT NULL,
  last_ok_at DATETIME NULL,
  last_error_at DATETIME NULL,
  last_error VARCHAR(255) NULL,
  consecutive_failures INT NOT NULL DEFAULT 0,
  PRIMARY KEY (meter_id),
  CONSTRAINT fk_mh_meter FOREIGN KEY (meter_id) REFERENCES meter(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Meter models (device profiles) ──
-- register_map was first keyed by meter_type_id, which only distinguishes
-- electrical from water. That cannot hold two brands at once: its
-- UNIQUE(meter_type_id, data_type_id) allows exactly one "kW" row for all
-- electrical meters in the system. Different brands put the same measurement
-- at different registers, in different word orders, at different scales, and
-- some answer on FC4 (input registers) rather than FC3 (holding).
--
-- So the map belongs to a *model*, and each meter says which model it is.
CREATE TABLE IF NOT EXISTS meter_model (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  brand VARCHAR(50) NULL,
  meter_type_id INT NOT NULL,
  note TEXT NULL,
  is_deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_model_type (meter_type_id),
  CONSTRAINT fk_model_type FOREIGN KEY (meter_type_id) REFERENCES meter_type(id)
) ENGINE=InnoDB;

ALTER TABLE meter ADD COLUMN model_id INT NULL AFTER meter_type_id;
ALTER TABLE meter ADD CONSTRAINT fk_meter_model FOREIGN KEY (model_id) REFERENCES meter_model(id) ON DELETE SET NULL;

ALTER TABLE register_map ADD COLUMN model_id INT NULL AFTER id;
-- FC3 = read holding registers (the default and by far the most common),
-- FC4 = read input registers. Per row, because some models mix the two.
ALTER TABLE register_map ADD COLUMN function_code TINYINT NOT NULL DEFAULT 3 AFTER word_count;

-- Everything electrical in service today is Schneider PM, and the seeded
-- addresses above are that family's. Turn them into the first model rather
-- than asking anyone to re-enter them.
INSERT INTO meter_model (name, brand, meter_type_id, note)
SELECT 'PM series', 'Schneider', 1,
       'Migrated from the register map that was hard-coded in the old Python pollers.'
WHERE NOT EXISTS (SELECT 1 FROM meter_model WHERE name = 'PM series' AND brand = 'Schneider');

UPDATE register_map
   SET model_id = (SELECT id FROM meter_model WHERE name = 'PM series' AND brand = 'Schneider')
 WHERE model_id IS NULL AND meter_type_id = 1;

UPDATE meter
   SET model_id = (SELECT id FROM meter_model WHERE name = 'PM series' AND brand = 'Schneider')
 WHERE model_id IS NULL AND meter_type_id = 1 AND is_deleted = 0;

-- meter_type_id on register_map is now derivable from the model, and keeping
-- both invites them to disagree. Swap the uniqueness over to the model.
ALTER TABLE register_map DROP FOREIGN KEY fk_rm_meter_type;
ALTER TABLE register_map DROP INDEX uq_type_data;
ALTER TABLE register_map DROP COLUMN meter_type_id;
ALTER TABLE register_map ADD UNIQUE KEY uq_model_data (model_id, data_type_id);
ALTER TABLE register_map ADD CONSTRAINT fk_rm_model FOREIGN KEY (model_id) REFERENCES meter_model(id) ON DELETE CASCADE;
