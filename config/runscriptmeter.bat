@echo off
cd /d C:\xampp\htdocs\ems
echo ==============================
echo          Fetch Meter
echo ==============================
echo result
python .\config\pymodbustcpAllmeters.py
pause