@echo off
setlocal EnableDelayedExpansion

:: Set Base Directory
set APP_DIR=%~dp0..
set PHP_BIN=%APP_DIR%\bin\php\php.exe
set MYSQL_BIN=%APP_DIR%\bin\mysql\bin\mysqld.exe

:: Default Ports (Will be overridden by .env.desktop if exists)
set WEB_PORT=8899
set DB_PORT=3309
set TUNNEL_ENABLED=0
set TUNNEL_TOKEN=

title Siapps Desktop Launcher

:: 1. Read Dynamic Configuration from .env.desktop
if exist "%APP_DIR%\.env.desktop" (
    echo [INFO] Membaca konfigurasi dari .env.desktop...
    for /f "tokens=1,2 delims==" %%A in ('type "%APP_DIR%\.env.desktop" ^| findstr /r "^DESKTOP_"') do (
        if "%%A"=="DESKTOP_WEB_PORT" set WEB_PORT=%%B
        if "%%A"=="DESKTOP_DB_PORT" set DB_PORT=%%B
        if "%%A"=="DESKTOP_TUNNEL_ENABLED" set TUNNEL_ENABLED=%%B
        if "%%A"=="DESKTOP_TUNNEL_TOKEN" set TUNNEL_TOKEN=%%B
    )
)

:: 2. Setup Environment on First Run (Only if .env doesn't exist)
if not exist "%APP_DIR%\.env" (
    echo [INFO] Setting up environment for first run...
    copy "%APP_DIR%\.env.desktop" "%APP_DIR%\.env"
)

:: 3. Start MySQL (Portable Mode)
echo [INFO] Starting Database on Port !DB_PORT!...
start /b "SiappsDatabase" "%MYSQL_BIN%" --defaults-file="%APP_DIR%\bin\mysql\bin\my.ini" --port=!DB_PORT! --console

:: 4. Start Laravel Server
echo [INFO] Starting Application on Port !WEB_PORT!...
cd "%APP_DIR%"
start /b "SiappsServer" "%PHP_BIN%" artisan serve --port=!WEB_PORT! --host=127.0.0.1 --env=desktop

:: 5. Start Hybrid Cloudflare Tunnel (If Enabled)
if "!TUNNEL_ENABLED!"=="1" (
    if "!TUNNEL_TOKEN!"=="" (
        echo [WARNING] Cloudflare Tunnel aktif, tapi Token kosong. Tunnel gagal dijalankan.
    ) else (
        if exist "%APP_DIR%\bin\cloudflared.exe" (
            echo [INFO] Membuka Terowongan Hybrid ke Internet...
            start /b "SiappsTunnel" "%APP_DIR%\bin\cloudflared.exe" tunnel run --token !TUNNEL_TOKEN!
        ) else (
            echo [WARNING] cloudflared.exe tidak ditemukan di folder bin! Tunnel fitur dibatalkan.
        )
    )
)

:: 6. Wait a bit for server to boot
timeout /t 5 /nobreak >nul

:: 7. Open Browser (App Mode)
echo [INFO] Opening Application...
start chrome --app=http://localhost:!WEB_PORT! 2>nul || start msedge --app=http://localhost:!WEB_PORT! 2>nul || start http://localhost:!WEB_PORT!

echo.
echo [SUCCESS] Aplikasi berjalan!
echo Jangan tutup jendela ini agar aplikasi tetap berjalan.
echo Tekan sembarang tombol untuk mematikan aplikasi.
pause

:: 6. Cleanup / Stop on Exit
call "%~dp0stop-app.bat"
