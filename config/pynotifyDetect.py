import requests
import json

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

    response = requests.post(url, headers=headers, data=json.dumps(data))
    if response.status_code == 200:
        print("ส่งข้อความเรียบร้อย")
    else:
        print("ส่งข้อความไม่สำเร็จ:", response.status_code, response.text)

while True:
    # เชื่อมต่อฐานข้อมูล
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="ams"  
    )

    cursor = conn.cursor(dictionary=True)

    # ดึงข้อมูลทั้งหมดจากตาราง meters
    cursor.execute("SELECT * FROM notify")

    # ใช้ for loop อ่านทีละแถว
    for meter in cursor:
        channel_token = "U7a1981400e9940fe37957b8aa27007e1"
        user_id = meter['token_line']
        message = "สวัสดี! นี่คือข้อความจาก LINE OA"
        
        send_line_oa(channel_token, user_id, message)

    cursor.close()
    conn.close()

    time.sleep(60)
