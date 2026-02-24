@echo off
setlocal EnableDelayedExpansion
set "ROOT_DIR=%~dp0"
set "MYSQL_DIR=%ROOT_DIR%database"
set "MYSQL_DATA=%ROOT_DIR%database\data"

:: Fix slashes for MySQL my.ini format (must be forward slashes)
set "MYSQL_DIR=!MYSQL_DIR:\=/!"
set "MYSQL_DATA=!MYSQL_DATA:\=/!"

echo [INFO] Menyiapkan konfigurasi Database...
"%SystemRoot%\System32\WindowsPowerShell\v1.0\powershell.exe" -Command "(gc '%ROOT_DIR%database\bin\my.ini' -Encoding UTF8) -replace '__MYSQL_DIR__', '!MYSQL_DIR!' | Out-File '%ROOT_DIR%database\my.ini' -Encoding UTF8"

if exist "%ROOT_DIR%dataweb\.env.desktop" (
    echo [INFO] Menyesuaikan konfigurasi aplikasi ^(Port 3309^)...
    copy /Y "%ROOT_DIR%dataweb\.env.desktop" "%ROOT_DIR%dataweb\.env" >nul
)

echo [INFO] Menginstal Database Service (SiappsDBSrv)...
"%ROOT_DIR%database\bin\mysqld.exe" --install SiappsDBSrv --defaults-file="%ROOT_DIR%database\my.ini"
net start SiappsDBSrv

echo [INFO] Menginstal Web Server Service (SiappsWebSrv)...
"%ROOT_DIR%bin\siapps-web.exe" install
net start SiappsWebSrv

echo [INFO] Instalasi Service Berhasil!
