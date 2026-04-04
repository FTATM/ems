import struct
import requests
import mysql.connector
import json
from datetime import datetime
import time
import threading
from pymodbus.client.sync import ModbusTcpClient
from pymodbus.exceptions import ConnectionException

# =========================
# CONFIG
# =========================
MAX_THREADS = 15   # 🔥 ปรับได้ (แนะนำ 5–10)
DELAY_BETWEEN_READ = 0.05

semaphore = threading.Semaphore(MAX_THREADS)

# =========================
# Utility
# =========================
def hex_to_float(hex_val):
    return struct.unpack('<f', struct.pack('<I', hex_val))[0]

def read_float_register(client, start_address, quantity=2, slaveid=1):
    result = client.read_holding_registers(
        address=start_address,
        count=quantity,
        unit=slaveid
    )

    if result.isError():
        return None

    regs = result.registers
    return round(hex_to_float((regs[0] << 16) + regs[1]), 2)

def safe_read(client, addr, quantity, slave):
    try:
        return read_float_register(client, addr, quantity, slave)
    except ConnectionException as e:
        print(f"⚠️ Connection lost: {e}")
        return None
    except Exception as e:
        print(f"❌ Other error: {e}")
        return None

# =========================
# Thread worker
# =========================
def process_meter(meter):
    with semaphore:  # 🔥 จำกัดจำนวน thread

        try:
            client = ModbusTcpClient(
                meter['ip_address'],
                meter['port'],
                timeout=3
            )

            if not client.connect():
                print(f"❌ ID {meter['id']} connect fail")
                return

            data = {}

            float_registers = {
                'kW': 3059,
                'kWh': 2699,
                'kVA': 3075,
                'kVAh': 2695,
                'kVAR': 3067,
                'kVARh': 2687,
                'Voltage A-N': 3027,
                'Voltage B-N': 3029,
                'Voltage C-N': 3031,
                'Current A': 2999,
                'Current B': 3001,
                'Current C': 3003,
                'Current avg': 3005,
                'Voltage A_B': 3019,
                'Voltage B_C': 3021,
                'Voltage C_A': 3023,
                'Pf': 3191,
                'Frequency': 3109,
            }

            for label, addr in float_registers.items():
                value = safe_read(
                    client,
                    addr,
                    meter['quality'],
                    meter['slave_id']
                )

                data[label] = value if value is not None else "Error"

                time.sleep(DELAY_BETWEEN_READ)  # 🔥 ลดการยิงถี่

            client.close()

            # === payload ===
            timestamp = datetime.now().isoformat()
            payload = {
                "meter_id": meter['id'],
                "datetime": timestamp,
                "is_active": 0,
                "data": data
            }

            print(json.dumps({
                "success": True,
                "meter": meter['id'],
                "output": payload
            }))

            # === send API ===
            try:
                url = "http://localhost/ems/config/meter-data.php"
                response = requests.post(url, json=payload, timeout=5)
                print(f"📡 API {meter['id']}:", response.status_code)
            except Exception as e:
                print(f"❌ API error meter {meter['id']}:", e)

        except Exception as e:
            print(f"🔥 Thread crash meter {meter['id']}:", e)


# =========================
# MAIN LOOP
# =========================
while True:

    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="ams"
        )

        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT * FROM meter")
        meters = cursor.fetchall()

        threads = []

        for meter in meters:
            t = threading.Thread(target=process_meter, args=(meter,))
            t.start()
            threads.append(t)

        # รอทุก thread ทำงานเสร็จ
        for t in threads:
            t.join()

        cursor.close()
        conn.close()

        print("⏱️ รอบนี้เสร็จแล้ว\n")

    except Exception as e:
        print("🔥 Main loop error:", e)

    time.sleep(60)