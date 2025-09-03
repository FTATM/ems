import struct
import requests
import sys
from datetime import datetime
from pymodbus.client.sync import ModbusTcpClient

if len(sys.argv) < 4:
    print("Usage: pymodbustcp.py <meter_id> <ip> <port>")
    sys.exit(1)

meter_id = int(sys.argv[1])
ip = sys.argv[2]
port = int(sys.argv[3])

# === Modbus Functions ===

def hex_to_float(hex_val):
    return struct.unpack('<f', struct.pack('<I', hex_val))[0]

def read_float_register(client, start_address):
    result = client.read_holding_registers(address=start_address, count=2, unit=1)
    if result.isError():
        return None
    regs = result.registers
    return round(hex_to_float((regs[0] << 16) + regs[1]), 2)
# === Connect Modbus ===

client = ModbusTcpClient(ip, port=port, timeout=3)
client.connect()




if not client.connect():
    print("Connection failed")
    exit(1)

# === Read Sample Data ===
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
    'Pf': 3083,
    'Frequency': 3109,
}

for label, addr in float_registers.items():
    value = read_float_register(client, addr)
    data[label] = value if value is not None else "Error"

# === Add timestamp ===
timestamp = datetime.now().isoformat()
payload = {
    "meter_id": meter_id,
    "datetime": timestamp,
    "data": data
}

# === Send to API ===
try:
    # url = "http://49.0.69.152/ams/config/meter-data.php"
    # response = requests.post(url, json=payload, timeout=5)
    # print("API Response:", response.status_code, response.text)
    print("Payload", payload)
except Exception as e:
    print("API Error:", e)

