from pymodbus.client import ModbusTcpClient
import struct
import tkinter as tk
import requests
import threading
import time
from datetime import datetime
import sys

# === config received from php ===
if len(sys.argv) >= 9:
    serial_port = sys.argv[1]
    buad_rate = int(sys.argv[2])
    data_bits = int(sys.argv[3])
    parily = sys.argv[4]
    stop_bits = int(sys.argv[5])
    slave_id = int(sys.argv[6])
    address = sys.argv[7]
    quality = int(sys.argv[8])

    print("🎉 Config received from PHP:")
    print(f"Serial Port: {serial_port}")
    print(f"Baud Rate: {buad_rate}")
    print(f"Data Bits: {data_bits}")
    print(f"Parily: {parily}")
    print(f"Stop Bits: {stop_bits}")
    print(f"Slave ID: {slave_id}")
    print(f"Address: {address}")
    print(f"Quality: {quality}")
else:
    print("❌ Missing arguments. Expected 8.")

# === API Setup ===
session = requests.Session()
session.headers.update({'Connection': 'keep-alive'})

# === Modbus Functions ===
def hex_to_float(hex_val):
    return struct.unpack('<f', struct.pack('<I', hex_val))[0]

def read_float_register(start_address):
    result = client.read_holding_registers(address=start_address, count=2, slave=1)
    if result.isError():
        return None
    regs = result.registers
    return round(hex_to_float((regs[0] << 16) + regs[1]), 2)

def read_time_component(address):
    result = client.read_holding_registers(address=address, count=1, slave=1)
    if result.isError():
        return None
    return result.registers[0]

def send_data_to_api(payload):
    url = "http://49.0.69.152/ams/config/meter-data.php"

    response = session.post(url, json=payload, timeout=5)
    print(" API Response:", response.status_code, response.text)
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

# === Modbus Register Mapping ===
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

# === Global Config ===
read_interval = 5       # seconds
send_interval = 30      # seconds
latest_data = {}        # cache for latest readings

# === Reading Function ===
def update_gui_and_data():
    global latest_data

    if not client.is_socket_open():
        client.connect()
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
        # ✅ สร้าง datetime object
        timestamp_dt = datetime(year, month, day, hour, minute, second)

        # ✅ แปลงเป็น ISO 8601 string: 2025-07-24T10:30:45
        global timestamp
        timestamp = timestamp_dt.isoformat()

    else:
        labels['time'].config(text="Time: Read Error")
        timestamp = None  # เคลียร์ timestamp ถ้าอ่านค่าเวลาไม่ได้

    # Read float registers
    for label, addr in float_registers.items():
        value = read_float_register(addr)
        if value is not None:
            labels[label].config(text=f"{label}: {value}")
            latest_data[label] = value
        else:
            labels[label].config(text=f"{label}: Read Error")

    root.after(read_interval * 1000, update_gui_and_data)

# === API Send Thread ===
def api_sender_loop():
    while True:
        payload = None  # ป้องกันกรณีใช้ตัวแปรที่ยังไม่ถูกประกาศ
        try:
            # === ส่งข้อมูล retry ค้างเก่า ===
            for retry_data in temp[:]:
                try:
                    print("🔁 Retrying:", retry_data)
                    send_data_to_api(retry_data)
                    temp.remove(retry_data)
                except Exception as retry_err:
                    print("⚠️ Retry failed:", retry_err)
                    continue

            # === ส่งข้อมูลล่าสุด ===
            if 'Active Power Total' in latest_data:
                payload = {
                    "meter_id": 1,
                    "datetime": timestamp,
                    "data": {
                        "kW": latest_data.get('Active Power Total', 0),
                        "kWh": latest_data.get('kWh', 0),
                        "kVA": latest_data.get('Active Power Total', 0),
                        "kVAh": latest_data.get('kVAh', 0),
                        "kVAR": latest_data.get('Reactive Power Total', 0),
                        "kVARh": latest_data.get('kVARh', 0),
                        "Vch_P1": latest_data.get('Voltage A-N', 0),
                        "Vch_P2": latest_data.get('Voltage B-N', 0),
                        "Vch_P3": latest_data.get('Voltage C-N', 0),
                        "Amp_L1": latest_data.get('Current A', 0),
                        "Amp_L2": latest_data.get('Current B', 0),
                        "Amp_L3": latest_data.get('Current C', 0),
                        "Amp_N": latest_data.get('Current Avg', 0),
                        "Voltage A_B": latest_data.get('Voltage A-B', 0),
                        "Voltage B_C": latest_data.get('Voltage B-C', 0),
                        "Voltage C_A": latest_data.get('Voltage C-A', 0),
                        "Pf": latest_data.get('Power Factor', 0),
                        "Frequency": latest_data.get('Frequency', 0),
                    }
                }
                print("📤 Sending to API:", payload)
                response = send_data_to_api(payload)

        except Exception as e:
            print("⚠️ Error in API sender:", e)
            temp.append(payload)
            print("📦 Saved to temp for retry (status code not OK).")


        print(f"🧊 Temp retry queue size: {len(temp)}")
        time.sleep(send_interval)


# === On Closing ===
def on_closing():
    if client.is_socket_open():
        client.close()
    root.destroy()

# === Connect to Modbus ===
client = ModbusTcpClient(address, port= serial_port)
if not client.connect():
    for label in labels.values():
        label.config(text="Connection Failed")

# === Start Main Loop ===
update_gui_and_data()

# === Start API Thread ===
api_thread = threading.Thread(target=api_sender_loop, daemon=True)
api_thread.start()

# === Handle GUI Close ===
root.protocol("WM_DELETE_WINDOW", on_closing)
root.mainloop()
