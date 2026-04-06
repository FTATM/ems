import requests
import json
import mysql.connector
import time
from config import DB_CONFIG, LINE_TOKEN # ดึง Config มาใช้งาน

def send_line_oa(token: str, user_id: str, message: str):
    url = "https://api.line.me/v2/bot/message/push"
    headers = {
        "Content-Type": "application/json",
        "Authorization": f"Bearer {token}"
    }
    data = {
        "to": user_id,
        "messages": [
            {
                "type": "text",
                "text": message
            }
        ]
    }

    try:
        response = requests.post(url, headers=headers, data=json.dumps(data))
        if response.status_code == 200:
            print(f"ส่งข้อความหา {user_id} เรียบร้อย")
        else:
            print(f"ส่งข้อความไม่สำเร็จ: {response.status_code} {response.text}")
    except Exception as e:
        print(f"เกิดข้อผิดพลาดในการส่ง LINE: {e}")

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
            cursor.execute("SELECT * FROM notify")
            rows = cursor.fetchall() # ดึงข้อมูลมาเก็บในตัวแปรก่อนเพื่อลดภาระ cursor

            for meter in rows:
                user_id = meter['token_line']
                message = "สวัสดี! นี่คือข้อความจาก LINE OA"
                
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