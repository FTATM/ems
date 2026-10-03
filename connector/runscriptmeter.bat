@echo off
REM Run the collector in the foreground, for debugging.
REM For unattended operation install it as a service: install-service.ps1
cd /d "%~dp0"
echo ==============================
echo       EMS Meter Collector
echo ==============================
python -m ems run
pause
