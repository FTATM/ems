from pymodbus.client.sync import ModbusSerialClient
import struct
import tkinter as tk
import requests
import threading
import time
from datetime import datetime
import json  # 🔹 เพิ่ม json สำหรับ debug print

# === API Setup ===
session = requests.Session()
session.headers.update({'Connection': 'keep-alive'})

# === Modbus Functions ===
def hex_to_float(hex_val):
    return struct.unpack('<f', struct.pack('<I', hex_val))[0]

def read_all_registers(start_address=2999, count=124):
    result = client.read_holding_registers(address=start_address, count=count, unit=1)
    if result.isError():
        return None
    return result.registers

def read_time_component(address):
    result = client.read_holding_registers(address=address, count=1, unit=1)
    if result.isError():
        return None
    return result.registers[0]

def send_data_to_api(payload):
    url = "http://49.0.69.152/ams/config/meter-data.php"
    try:
        print("📤 Sending payload JSON:")
        print(json.dumps(payload, indent=4))  # 🔹 พิมพ์ payload ออก
        response = session.post(url, json=payload, timeout=5)
        print("✅ API Response:", response.status_code, response.text)
        return response
    except requests.exceptions.RequestException as e:
        print("❌ API Error:", e)
        raise

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
    'Power Factor': 3191,
    'kVARh': 2687,
    'kVAh': 2695
}

batch_start = 2999
batch_count = 124
small_addresses = [2679, 2687, 2695]

read_interval = 5
send_interval = 30
latest_data = {}
timestamp = None
last_sent_timestamp = None
temp = []

def update_gui_and_data():
    global latest_data, timestamp

    if not client.connect():
        for label in labels.values():
            label.config(text="Disconnected")
        root.after(read_interval * 1000, update_gui_and_data)
        return

    regs = read_all_registers(batch_start, batch_count)
    batch_values = {}

    if regs is None or len(regs) < batch_count:
        print(f"⚠️ Error reading registers from {batch_start}, got {len(regs) if regs else 0} words.")
        for label in float_registers.keys():
            labels[label].config(text=f"{label}: Read Error")
            batch_values[label] = None
    else:
        for label, addr in float_registers.items():
            if addr < batch_start:
                continue
            idx = addr - batch_start
            if 0 <= idx < len(regs) - 1:
                try:
                    combined = (regs[idx] << 16) + regs[idx + 1]
                    val = round(hex_to_float(combined), 2)
                    batch_values[label] = val
                except Exception as e:
                    print(f"⚠️ Error converting {label} at idx {idx}: {e}")
                    batch_values[label] = None
            else:
                print(f"⚠️ Index out of range for {label} at address {addr}")
                batch_values[label] = None

    for addr in small_addresses:
        try:
            result = client.read_holding_registers(address=addr, count=2, unit=1)
            if not result.isError() and len(result.registers) == 2:
                regs_small = result.registers
                combined = (regs_small[0] << 16) + regs_small[1]
                val = round(hex_to_float(combined), 2)
            else:
                val = None
        except Exception as e:
            print(f"⚠️ Exception reading address {addr}: {e}")
            val = None

        if addr == 2679:
            batch_values['kWh'] = val
        elif addr == 2687:
            batch_values['kVARh'] = val
        elif addr == 2695:
            batch_values['kVAh'] = val

    for label in float_registers.keys():
        val = batch_values.get(label)
        if val is not None:
            labels[label].config(text=f"{label}: {val}")
            latest_data[label] = val
        else:
            labels[label].config(text=f"{label}: Read Error")

    for field in ['kWh', 'kVARh', 'kVAh']:
        val = batch_values.get(field)
        if val is not None:
            labels[field].config(text=f"{field}: {val}")
            latest_data[field] = val
        else:
            labels[field].config(text=f"{field}: Read Error")

    root.after(read_interval * 1000, update_gui_and_data)
    
# === Add timestamp ===
timestamp = datetime.now().isoformat()
def api_sender_loop():
    global last_sent_timestamp
    print("🟢 API thread started")  # 🔹 debug

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

                    print("📤 Preparing to send to API...")
                    response = send_data_to_api(payload)
                    last_sent_timestamp = timestamp

        except Exception as e:
            print("⚠️ Error in API sender:", e)
            if payload:
                temp.append(payload)
                print("📦 Saved to temp for retry.")

        print(f"🧊 Temp retry queue size: {len(temp)}")
        time.sleep(send_interval)

# === On Closing ===
def on_closing():
    if client.is_socket_open():
        client.close()
    root.destroy()

# === Connect to Modbus RTU via RS485 ===
client = ModbusSerialClient(
    method='rtu',
    port='COM4',        # ✅ ตรวจสอบว่า COM4 ถูกต้อง (เปลี่ยนตามเครื่องคุณ)
    baudrate=9600,
    bytesize=8,
    parity='N',
    stopbits=1,
    timeout=1
)

if not client.connect():
    for label in labels.values():
        label.config(text="Connection Failed")
    print("❌ Failed to connect to Modbus.")

# === Start Main GUI Loop ===
update_gui_and_data()

# === Start API Sender Thread ===
api_thread = threading.Thread(target=api_sender_loop, daemon=True)
api_thread.start()

# === Handle GUI Close ===
root.protocol("WM_DELETE_WINDOW", on_closing)
root.mainloop()

