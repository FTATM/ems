import requests
import json

def LINE_OA(token: str, user_id: str, message: str):
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