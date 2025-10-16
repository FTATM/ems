import struct
import requests
import sys
import json  # 🔹 เพิ่ม json สำหรับ debug print
from datetime import datetime
from pymodbus.client.sync import ModbusSerialClient
try:
    if len(sys.argv) < 10:
        print(json.dumps({ "success": False, "message": "Usage: pymodbusrs485.py pymodbusrs485 <meter_id> <Serial_port> <baudrate> <databits> <parity> <stopbits> <slaveid> <address> <quality>" }))
        sys.exit(1)

    # pymodbusrs485 meter_id Serial_port baudrate databits parity stopbits
    meter_id = int(sys.argv[1])
    port = sys.argv[2]
    baudrate = int(sys.argv[3])
    databits = int(sys.argv[4])
    parity = sys.argv[5]
    stopbits = int(sys.argv[6])
    slaveid = int(sys.argv[7])
    address = int(sys.argv[8])
    quality = int(sys.argv[9])

    # === Modbus Functions ===
    def hex_to_float(hex_val):
        return struct.unpack('<f', struct.pack('<I', hex_val))[0]

    def read_float_register(client, start_address):
        result = client.read_holding_registers(address=start_address, count=quality, unit=slaveid)
        if result.isError():
            return None
        regs = result.registers
        return round(hex_to_float((regs[0] << 16) + regs[1]), 2)
    # === Connect to Modbus RTU via RS485 ===
    client = ModbusSerialClient(
        method='rtu',
        port=port,
        baudrate=baudrate,
        bytesize=databits,
        parity=parity,
        stopbits=stopbits,
        timeout=1
    )

    if not client.connect():
        print(json.dumps({ "success": False, "message": f"Connection failed to {port} : {baudrate}" }))
        exit(1)

    # === Read Sample Data ===
    data = {}
    float_registers = {
        'kW':address + 3059,
        'kWh':address +  2699,
        'kVA':address +  3075,
        'kVAh' :address +  2695,
        'kVAR':address +  3067,
        'kVARh':address +  2687,
        'Voltage A-N':address +  3027,
        'Voltage B-N':address +  3029,
        'Voltage C-N':address +  3031,
        'Current A':address +  2999,
        'Current B':address +  3001,
        'Current C':address +  3003,
        'Current avg':address +  3005,
        'Voltage A_B':address +  3019,
        'Voltage B_C':address +  3021,
        'Voltage C_A':address +  3023,
        'Pf':address +  3191,
        'Frequency':address +  3109,
    }

    for label, addr in float_registers.items():
        value = read_float_register(client, addr)
        data[label] = value if value is not None else "-"


    # === Add timestamp ===
    timestamp = datetime.now().isoformat()
    payload = {
        "meter_id": meter_id,
        "datetime": timestamp,
        "data": data
    }
    
    print(json.dumps({"success" : True, "message" : "Found meter.", "output" : payload}))

    # === Send to API ===
    # url = "http://49.0.69.152/ams/config/meter-data.php"
    # response = requests.post(url, json=payload, timeout=5)
    # print("API Response:", response.status_code, response.text)
except Exception as e:
    print(json.dumps({ "success": False, "message": "Not found meter.", "output": str(e) }))
    exit(1)

finally:
    try:
        client.close()
    except:
        pass


