from pymodbus.client import ModbusTcpClient
import struct
import json

# === Helper ===
def hex_to_float(hex_val):
    return struct.unpack('<f', struct.pack('<I', hex_val))[0]

def read_float_register(client, start_address):
    result = client.read_holding_registers(address=start_address, count=2, slave=1)
    if result.isError():
        return None
    regs = result.registers
    return round(hex_to_float((regs[0] << 16) + regs[1]), 2)

# === Modbus Config ===
client = ModbusTcpClient('192.168.0.7', port=8800)
client.connect()

# === ตัวอย่าง Register ที่ต้องการอ่าน ===
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

data = {}
for label, addr in float_registers.items():
    value = read_float_register(client, addr)
    data[label] = value if value is not None else "Error"

client.close()

# === Print JSON สำหรับให้ PHP/JS ใช้ ===
print(json.dumps(data))
