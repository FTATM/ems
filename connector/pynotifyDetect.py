"""LINE alerts for the `notify` table.

WARNING — this script does not do what its name suggests. It pushes a LINE
message for *every* active notify row on *every* 60s cycle: it never compares
`mark` / `value_condition` against the meter's actual readings, so there is no
threshold logic at all and no de-duplication. Treat it as a stub. It is kept
here only so the existing notify rows aren't orphaned; fixing it is a separate
piece of work from the collector rewrite.

Config and the LINE helper now come from the ems package rather than from the
deleted connector/config.py and connector/function.py.
"""

import mysql.connector
import time

from ems.settings import DB_CONFIG, LINE_TOKEN
from ems.notify import push as send_line_oa

# ฟังก์ชันสำหรับสร้างการเชื่อมต่อใหม่
def get_db_connection():
    return mysql.connector.connect(**DB_CONFIG)

# เริ่มต้นเชื่อมต่อครั้งแรก
conn = get_db_connection()

try:
    while True:
        try:
            # ตรวจสอบว่า Connection ยังใช้งานได้ไหม ถ้าไม่ให้ต่อใหม่
            if not conn.is_connected():
                print("Database disconnected. Reconnecting...")
                conn = get_db_connection()

            cursor = conn.cursor(dictionary=True)

            # ดึงข้อมูลจากตาราง notify
            cursor.execute("SELECT * FROM notify WHERE is_deleted = 0 AND is_active = 1")
            rows = cursor.fetchall() # ดึงข้อมูลมาเก็บในตัวแปรก่อนเพื่อลดภาระ cursor

            for meter in rows:
                user_id = meter['token_line']
                message = f"{meter['name']}\n\nDetected: {meter['mark']} {meter['value_condition']}"

                # ใช้ LINE_TOKEN จาก config
                send_line_oa(LINE_TOKEN, user_id, message)

            cursor.close()
            print("Cycle completed. Sleeping for 60s...")

        except mysql.connector.Error as db_err:
            print(f"Database Error: {db_err}")
        
        time.sleep(60)

except KeyboardInterrupt:
    print("Program stopped by user.")
finally:
    if conn.is_connected():
        conn.close()
        print("Database connection closed.")