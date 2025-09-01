from flask import Flask, jsonify
from flask_cors import CORS   # เพิ่มเข้ามา
from pymodbus.client.sync import ModbusTcpClient  # ✅ ใหม่ ใช้ได้
import struct

app = Flask(__name__)
CORS(app, resources={r"/modbus/data": {"origins": "*"}})

# Config
MODBUS_HOST = '192.168.0.7'
MODBUS_PORT = 8800
DEVICE_ID = 1

def hex_to_float(hex_val):
    return struct.unpack('<f', struct.pack('<I', hex_val))[0]

def read_float(client, addr):
    resp = client.read_holding_registers(address=addr, count=2, device_id=DEVICE_ID)
    if resp.isError():
        return None
    regs = resp.registers
    return round(hex_to_float((regs[0] << 16) + regs[1]), 2)

def read_reg(client, addr):
    resp = client.read_holding_registers(address=addr, count=1, device_id=DEVICE_ID)
    return None if resp.isError() else resp.registers[0]

@app.route('/modbus/data', methods=['GET'])
def get_modbus_data():
    client = ModbusTcpClient(MODBUS_HOST, port=MODBUS_PORT)
    if not client.connect():
        return jsonify({'error': 'Cannot connect modbus'}), 500

    data = {
        'year': read_reg(client, 1836),
        'voltage_an': read_float(client, 3027),
        'active_power_total': read_float(client, 3059)
    }
    client.close()
    return jsonify(data)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
