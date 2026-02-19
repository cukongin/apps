@echo off
setlocal

:: Configuration
set APP_DIR=%~dp0..
set PHP_BIN=%APP_DIR%\bin\php\php.exe
set MYSQL_BIN=%APP_DIR%\bin\mysql\bin\mysqld.exe
set WEB_PORT=8899
set DB_PORT=3309

title Siapps Desktop Launcher

:: 1. Setup Environment on First Run
if not exist "%APP_DIR%\.env" (
    echo [INFO] Setting up environment for first run...
    copy "%APP_DIR%\.env.desktop" "%APP_DIR%\.env"

    :: Generate Key if needed (though .env.desktop has one)
    :: "%PHP_BIN%" "%APP_DIR%\artisan" key:generate
)

:: 2. Start MySQL (Portable Mode)
echo [INFO] Starting Database on Port %DB_PORT%...
start /b "SiappsDatabase" "%MYSQL_BIN%" --defaults-file="%APP_DIR%\bin\mysql\bin\my.ini" --port=%DB_PORT% --console

:: 3. Start Laravel Server
echo [INFO] Starting Application on Port %WEB_PORT%...
cd "%APP_DIR%"
start /b "SiappsServer" "%PHP_BIN%" artisan serve --port=%WEB_PORT% --host=127.0.0.1

:: 4. Wait a bit for server to boot
timeout /t 5 /nobreak >nul

:: 5. Open Browser (App Mode)
echo [INFO] Opening Application...
start chrome --app=http://localhost:%WEB_PORT% 2>nul || start msedge --app=http://localhost:%WEB_PORT% 2>nul || start http://localhost:%WEB_PORT%

echo.
echo [SUCCESS] Aplikasi berjalan!
echo Jangan tutup jendela ini agar aplikasi tetap berjalan.
echo Tekan sembarang tombol untuk mematikan aplikasi.
pause

:: 6. Cleanup / Stop on Exit
call "%~dp0stop-app.bat"
