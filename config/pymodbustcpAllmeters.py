import struct
import requests
import sys
import mysql.connector
import json
from datetime import datetime, time
from pymodbus.client.sync import ModbusTcpClient

def hex_to_float(hex_val):
    return struct.unpack('<f', struct.pack('<I', hex_val))[0]

def read_float_register(client, start_address,quantity=2, slaveid=1):
    result = client.read_holding_registers(address=start_address, count=quantity, unit=slaveid)
    if result.isError():
        return None
    regs = result.registers
    return round(hex_to_float((regs[0] << 16) + regs[1]), 2)

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
    cursor.execute("SELECT * FROM meter")

    # ใช้ for loop อ่านทีละแถว
    for meter in cursor:
        client = ModbusTcpClient(meter['ip_address'], meter['port'], timeout=3)
        if not client.connect():
            print(f"ID : {meter['id']} can't connect, Will try again next round.")
            continue

        print(f"ID: {meter['id']} {type(meter['id'])}")
        print(f"ip_add: {meter['ip_address']} {type(meter['ip_address'])}")
        print(f"port: {meter['port']} {type(meter['port'])}")
        print(f"quality: {meter['quality']} {type(meter['quality'])}")
        print(f"slave : {meter['slave_id']} {type(meter['slave_id'])}")
        data = {}
        float_registers = {
            'kW': 3059,
            'kWh': 2679,
            'kVA': 3075,
            'kVAh' : 2695,
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
            value = read_float_register(client, addr, meter['quality'], meter['slave_id'])
            data[label] = value if value is not None else "Error"


        # === Add timestamp ===
        timestamp = datetime.now().isoformat()
        payload = {
            "meter_id": meter['id'],
            "datetime": timestamp,
            "data": data
        }
        
        print(json.dumps({"success" : True, "message" : "Found meter.", "output" : payload}))

        # === Send to API ===
        url = "http://localhost/ems/config/meter-data.php"
        response = requests.post(url, json=payload, timeout=5)
        print("API Response:", response.status_code, response.text)

    # ปิด cursor และการเชื่อมต่อ
    cursor.close()
    conn.close()

    time.sleep(60)

