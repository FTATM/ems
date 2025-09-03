import json
import struct
import tkinter as tk
import requests
import threading
import time
from datetime import datetime
from pymodbus.client.sync import ModbusTcpClient
import logging

# === DEBUG LOGGING ===
logging.basicConfig()
log = logging.getLogger()
log.setLevel(logging.ERROR)  # เปลี่ยนเป็น DEBUG ถ้าต้องการดูทุกอย่าง

# === API Setup ===
session = requests.Session()
session.headers.update({'Connection': 'keep-alive'})

# === Modbus Functions ===
def hex_to_float(hex_val):
    return struct.unpack('<f', struct.pack('<I', hex_val))[0]

def read_float_register(start_address):
    try:
        if not client.connect():
            print(f"❌ Connection failed at address {start_address}")
            return None

        result = client.read_holding_registers(address=start_address, count=2, unit=1)
        if result.isError():
            print(f"⚠️ Modbus error at {start_address}: {result}")
            return None

        regs = result.registers
        return round(hex_to_float((regs[0] << 16) + regs[1]), 2)
    except Exception as e:
        print(f"❌ Error reading register {start_address}:", e)
        return None

def read_time_component(address):
    try:
        result = client.read_holding_registers(address=address, count=1, unit=1)
        if result.isError():
            print(f"⚠️ Time read error at {address}: {result}")
            return None
        return result.registers[0]
    except Exception as e:
        print(f"❌ Exception reading time register {address}:", e)
        return None

def send_data_to_api(payload):
    url = "http://49.0.69.152/ams/config/meter-data.php"
    response = session.post(url, json=payload, timeout=5)
    print("✅ API Response:", response.status_code, response.text)
    return response

# === GUI Setup ===
root = tk.Tk()
root.title("Modbus Electrical Monitor")

labels = {}
fields = [
    'time',
    'Current A', 'Current B', 'Current C', 'Current N',
    'Voltage A-B', 'Voltage B-C', 'Voltage C-A',
    'Voltage A-N', 'Voltage B-N', 'Voltage C-N',
    'Active Power A', 'Active Power B', 'Active Power C', 'Active Power Total',
    'Reactive Power A', 'Reactive Power B', 'Reactive Power C', 'Reactive Power Total',
    'Apparent Power A', 'Apparent Power B', 'Apparent Power C', 'Apparent Power Total',
    'Frequency', 'kWh', 'Power Factor', 'kVARh', 'kVAh'
]

temp = []

for field in fields:
    labels[field] = tk.Label(root, text=f"{field}: --", font=('Arial', 14))
    labels[field].pack(pady=2)

# === Register Mapping ===
float_registers = {
    'Current A': 2999,
    'Current B': 3001,
    'Current C': 3003,
    'Current N': 3005,
    'Voltage A-B': 3019,
    'Voltage B-C': 3021,
    'Voltage C-A': 3023,
    'Voltage A-N': 3027,
    'Voltage B-N': 3029,
    'Voltage C-N': 3031,
    'Active Power A': 3053,
    'Active Power B': 3055,
    'Active Power C': 3057,
    'Active Power Total': 3059,
    'Reactive Power A': 3061,
    'Reactive Power B': 3063,
    'Reactive Power C': 3065,
    'Reactive Power Total': 3067,
    'Apparent Power A': 3069,
    'Apparent Power B': 3071,
    'Apparent Power C': 3073,
    'Apparent Power Total': 3075,
    'Frequency': 3109,
    'kWh': 2679,
    'Power Factor': 3083,
    'kVARh': 2687,
    'kVAh' : 2695
}

# === Global State ===
read_interval = 5
send_interval = 30
latest_data = {}
timestamp = None
last_sent_timestamp = None

# === Update GUI and Read Data ===
def update_gui_and_data():
    global latest_data, timestamp
    try:
        if not client.connect():
            for label in labels.values():
                label.config(text="Disconnected")
            root.after(read_interval * 1000, update_gui_and_data)
            return

        # Read time
        year = read_time_component(1836)
        month = read_time_component(1837)
        day = read_time_component(1838)
        hour = read_time_component(1839)
        minute = read_time_component(1840)
        second = read_time_component(1841)

        if None not in (year, month, day, hour, minute, second):
            labels['time'].config(text=f"Time: {year}-{month:02d}-{day:02d} {hour:02d}:{minute:02d}:{second:02d}")
            timestamp_dt = datetime(year, month, day, hour, minute, second)
            timestamp = timestamp_dt.isoformat()
        else:
            labels['time'].config(text="Time: Read Error")
            timestamp = None

        # Read float registers
        for label, addr in float_registers.items():
            value = read_float_register(addr)
            if value is not None:
                labels[label].config(text=f"{label}: {value}")
                latest_data[label] = value
            else:
                labels[label].config(text=f"{label}: Read Error")

    except Exception as e:
        print("❌ Exception in update_gui_and_data:", e)

    root.after(read_interval * 1000, update_gui_and_data)

# === API Sender Thread ===
def api_sender_loop():
    global last_sent_timestamp
    while True:
        payload = None
        try:
            for retry_data in temp[:]:
                try:
                    print("🔁 Retrying:", retry_data)
                    send_data_to_api(retry_data)
                    temp.remove(retry_data)
                except Exception as retry_err:
                    print("⚠️ Retry failed:", retry_err)
                    continue

            if 'Active Power Total' in latest_data and timestamp is not None:
                if timestamp != last_sent_timestamp:
                    payload = {
                        "meter_id": 1,
                        "datetime": timestamp,
                        "data": {
                            "kW": latest_data.get('Active Power Total', 0),
                            "kWh": latest_data.get('kWh', 0),
                            "kVA": latest_data.get('Apparent Power Total', 0),
                            "kVAh": latest_data.get('kVAh', 0),
                            "kVAR": latest_data.get('Reactive Power Total', 0),
                            "kVARh": latest_data.get('kVARh', 0),
                            "Voltage A-N": latest_data.get('Voltage A-N', 0),
                            "Voltage B-N": latest_data.get('Voltage B-N', 0),
                            "Voltage C-N": latest_data.get('Voltage C-N', 0),
                            "Current A": latest_data.get('Current A', 0),
                            "Current B": latest_data.get('Current B', 0),
                            "Current C": latest_data.get('Current C', 0),
                            "Current avg": (latest_data.get('Current A', 0) + latest_data.get('Current B', 0) + latest_data.get('Current C', 0)) / 3,
                            "Voltage A_B": latest_data.get('Voltage A-B', 0),
                            "Voltage B_C": latest_data.get('Voltage B-C', 0),
                            "Voltage C_A": latest_data.get('Voltage C-A', 0),
                            "Pf": latest_data.get('Power Factor', 0),
                            "Frequency": latest_data.get('Frequency', 0),
                        }
                    }
                    print("📤 Sending to API:", payload)
                    response = send_data_to_api(payload)
                    last_sent_timestamp = timestamp

        except Exception as e:
            print("⚠️ Error in API sender:", e)
            if payload:
                temp.append(payload)
                print("📦 Saved to temp for retry.")

        time.sleep(send_interval)

# === On Close ===
def on_closing():
    if client.is_socket_open():
        client.close()
    root.destroy()

# === Start Modbus Client ===
client = ModbusTcpClient('192.168.0.7', port=8800, timeout=3)

if not client.connect():
    print("❌ Initial connection failed.")
    for label in labels.values():
        label.config(text="Connection Failed")

# === Start Reading and Sending ===
update_gui_and_data()

api_thread = threading.Thread(target=api_sender_loop, daemon=True)
api_thread.start()

root.protocol("WM_DELETE_WINDOW", on_closing)
root.mainloop()
