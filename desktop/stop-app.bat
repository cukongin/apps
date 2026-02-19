@echo off
echo [INFO] Stopping Siapps Desktop...

:: Kill PHP Server on Port 8899
for /f "tokens=5" %%a in ('netstat -aon ^| find ":8899" ^| find "LISTENING"') do taskkill /f /pid %%a >nul 2>&1

:: Kill MySQL on Port 3309
for /f "tokens=5" %%a in ('netstat -aon ^| find ":3309" ^| find "LISTENING"') do taskkill /f /pid %%a >nul 2>&1

echo [SUCCESS] Aplikasi berhenti.
ping 127.0.0.1 -n 2 >nul
exit
