@echo off
setlocal EnableDelayedExpansion

:: ----------------------------------------------------
:: LAUNCHER MANDIRI (STANDALONE DAPODIK-STYLE)
:: Terikat pada struktur folder: dataweb, database, php
:: ----------------------------------------------------

:: Set Direktori Absolut
set "ROOT_DIR=%~dp0"
set "APP_DIR=%ROOT_DIR%dataweb"
set "PHP_BIN=%ROOT_DIR%php\php.exe"
set "MYSQL_BIN=%ROOT_DIR%database\bin\mysqld.exe"

:: Port Default (Bisa ditimpa oleh .env.desktop)
set WEB_PORT=8899
set DB_PORT=3309
set TUNNEL_ENABLED=0
set TUNNEL_TOKEN=

title Siapps Berjalan (Server Lokal)

:: 1. Membaca Konfigurasi dari .env.desktop di dalam dataweb
if exist "%APP_DIR%\.env.desktop" (
    echo [INFO] Membaca setelan jaringan dari .env.desktop...
    for /f "tokens=1,2 delims==" %%A in ('type "%APP_DIR%\.env.desktop" ^| findstr /r "^DESKTOP_"') do (
        if "%%A"=="DESKTOP_WEB_PORT" set WEB_PORT=%%B
        if "%%A"=="DESKTOP_DB_PORT" set DB_PORT=%%B
        if "%%A"=="DESKTOP_TUNNEL_ENABLED" set TUNNEL_ENABLED=%%B
        if "%%A"=="DESKTOP_TUNNEL_TOKEN" set TUNNEL_TOKEN=%%B
    )
)

:: 2. Setup Environment Pertama Kali Jika Belum Ada
if not exist "%APP_DIR%\.env" (
    echo [INFO] Menyiapkan lingkungan aplikasi untuk pertama kali...
    copy "%APP_DIR%\.env.desktop" "%APP_DIR%\.env" >nul
)

:: 3. Jalankan MySQL (Standalone Portable)
echo [INFO] Menyalakan Mesin Database di Port !DB_PORT!...
start /b "SiappsDatabase" "%MYSQL_BIN%" --basedir="%ROOT_DIR%database" --datadir="%ROOT_DIR%database\data" --port=!DB_PORT! --console

:: 4. Jalankan Laravel Web Server (PHP Artisan)
echo [INFO] Menyalakan Website di Port !WEB_PORT!...
cd "%APP_DIR%"
start /b "SiappsServer" "%PHP_BIN%" artisan serve --port=!WEB_PORT! --host=127.0.0.1 --env=desktop

:: 5. Jalankan Cloudflare Tunnel (Opsional)
if "!TUNNEL_ENABLED!"=="1" (
    if "!TUNNEL_TOKEN!"=="" (
        echo [WARNING] Cloudflare Tunnel aktif, tapi Token kosong. Tunnel gagal dijalankan.
    ) else (
        if exist "%ROOT_DIR%bin\cloudflared.exe" (
            echo [INFO] Membuka Akses Publik ke Internet...
            start /b "SiappsTunnel" "%ROOT_DIR%bin\cloudflared.exe" tunnel run --token !TUNNEL_TOKEN!
        ) else (
            echo [WARNING] cloudflared.exe tidak ditemukan di folder bin! Tunnel dibatalkan.
        )
    )
)

:: 6. Buka Browser
timeout /t 5 /nobreak >nul
echo [INFO] Membuka Tampilan Aplikasi...
start chrome --app=http://localhost:!WEB_PORT! 2>nul || start msedge --app=http://localhost:!WEB_PORT! 2>nul || start http://localhost:!WEB_PORT!

:: Script berakhir di sini. Background process start /b akan terus jalan.
exit

