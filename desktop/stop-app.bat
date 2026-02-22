@echo off
setlocal EnableDelayedExpansion

echo [INFO] Stopping Siapps Desktop...

:: Default Ports
set WEB_PORT=8899
set DB_PORT=3309
set TUNNEL_ENABLED=0

:: Read Dynamic Configuration from .env.desktop
set APP_DIR=%~dp0..
if exist "%APP_DIR%\.env.desktop" (
    for /f "tokens=1,2 delims==" %%A in ('type "%APP_DIR%\.env.desktop" ^| findstr /r "^DESKTOP_"') do (
        if "%%A"=="DESKTOP_WEB_PORT" set WEB_PORT=%%B
        if "%%A"=="DESKTOP_DB_PORT" set DB_PORT=%%B
        if "%%A"=="DESKTOP_TUNNEL_ENABLED" set TUNNEL_ENABLED=%%B
    )
)

:: Kill PHP Server on dynamic port
for /f "tokens=5" %%a in ('netstat -aon ^| find ":!WEB_PORT!" ^| find "LISTENING"') do taskkill /f /pid %%a >nul 2>&1

:: Kill MySQL on dynamic port
for /f "tokens=5" %%a in ('netstat -aon ^| find ":!DB_PORT!" ^| find "LISTENING"') do taskkill /f /pid %%a >nul 2>&1

:: Kill Cloudflare Tunnel if enabled
if "!TUNNEL_ENABLED!"=="1" (
    taskkill /f /im cloudflared.exe >nul 2>&1
)

echo [SUCCESS] Aplikasi berhenti.
ping 127.0.0.1 -n 2 >nul
exit
