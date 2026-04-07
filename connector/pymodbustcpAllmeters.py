import struct
import requests
import mysql.connector
import json
from datetime import datetime
import time
import threading
from config import DB_CONFIG, LINE_TOKEN, CHAT_LINE_TOKEN  # ดึง Config มาใช้งาน
from function import LINE_OA
from pymodbus.client.sync import ModbusTcpClient
from pymodbus.exceptions import ConnectionException

# =========================
# CONFIG
# =========================
counter_lock = threading.Lock()
TIME_AT_CHECK_REPORT = ["6", "18"]
count_failed_connect = 0

MAX_THREADS = 15  # 🔥 ปรับได้ (แนะนำ 5–10)
DELAY_BETWEEN_READ = 0.05

semaphore = threading.Semaphore(MAX_THREADS)


# =========================
# Utility
# =========================
def hex_to_float(hex_val):
    return struct.unpack("<f", struct.pack("<I", hex_val))[0]


def read_float_register(client, start_address, quantity=2, slaveid=1):
    result = client.read_holding_registers(
        address=start_address, count=quantity, unit=slaveid
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
    global count_failed_connect
    with semaphore:  # 🔥 จำกัดจำนวน thread

        try:
            client = ModbusTcpClient(meter["ip_address"], meter["port"], timeout=3)

            if not client.connect():
                print(f"❌ ID {meter['id']} connect fail")
                with counter_lock:
                    count_failed_connect += 1
                return

            data = {}

            float_registers = {
                "kW": 3059,
                "kWh": 2699,
                "kVA": 3075,
                "kVAh": 2695,
                "kVAR": 3067,
                "kVARh": 2687,
                "Voltage A-N": 3027,
                "Voltage B-N": 3029,
                "Voltage C-N": 3031,
                "Current A": 2999,
                "Current B": 3001,
                "Current C": 3003,
                "Current avg": 3005,
                "Voltage A_B": 3019,
                "Voltage B_C": 3021,
                "Voltage C_A": 3023,
                "Pf": 3191,
                "Frequency": 3109,
            }

            for label, addr in float_registers.items():
                value = safe_read(client, addr, meter["quality"], meter["slave_id"])

                data[label] = value if value is not None else "Error"

                time.sleep(DELAY_BETWEEN_READ)  # 🔥 ลดการยิงถี่

            client.close()

            # === payload ===
            timestamp = datetime.now().isoformat()
            payload = {
                "meter_id": meter["id"],
                "datetime": timestamp,
                "is_active": 0,
                "data": data,
            }

            print(
                json.dumps({"success": True, "meter": meter["id"], "output": payload})
            )

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
# ฟังก์ชันสำหรับสร้างการเชื่อมต่อใหม่
def get_db_connection():
    return mysql.connector.connect(**DB_CONFIG)


# เริ่มต้นเชื่อมต่อครั้งแรก
conn = get_db_connection()

try:
    while True:
        try:
            # 1. รีเซ็ตค่าการนับในแต่ละรอบ
            count_failed_connect = 0

            # ตรวจสอบว่า Connection ยังใช้งานได้ไหม ถ้าไม่ให้ต่อใหม่
            if not conn.is_connected():
                print("Database disconnected. Reconnecting...")
                conn = get_db_connection()

            cursor = conn.cursor(dictionary=True)
            cursor.execute("SELECT * FROM meter WHERE is_active = 1 AND is_deleted = 0")
            meters = cursor.fetchall()

            threads = []

            for meter in meters:
                t = threading.Thread(target=process_meter, args=(meter,))
                t.start()
                threads.append(t)

            # รอทุก thread ทำงานเสร็จ
            for t in threads:
                t.join()


            # 2. ดึงเวลาปัจจุบันมาเช็ค
            now = datetime.now()
            current_hour = now.hour
            current_minute = now.minute

            # ==========================================
            # 3. เงื่อนไขการส่ง Line (Lock เฉพาะช่วงเวลา)
            # ==========================================
            is_time_to_send = current_hour in [6, 18] and current_minute in [0, 4]
            if is_time_to_send:
                if count_failed_connect > 0:
                    message_line = f"⚠️ [Daily Report] พบมิเตอร์ที่ไม่สามารถ CONNECT ได้จำนวน : {count_failed_connect} ตัว"
                else:
                    message_line = f"✅ [Daily Report] มิเตอร์ทุกตัวเชื่อมต่อปกติ"
                
                # ส่ง Line
                LINE_OA(LINE_TOKEN, CHAT_LINE_TOKEN, message_line)
                print(f"📢 Line Sent at {now.strftime('%H:%M')}")
            else:
                print(f"🕒 Current time {now.strftime('%H:%M')} (Not in report window)")

            print("Cycle completed. Sleeping for 60s...")

        except Exception as e:
            print("🔥 Main loop error:", e)
        finally:
            if cursor:
                cursor.close()

        time.sleep(60)

except KeyboardInterrupt:
    print("Program stopped by user.")
finally:
    if conn.is_connected():
        conn.close()
        print("Database connection closed.")
