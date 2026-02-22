@echo off
setlocal EnableDelayedExpansion
title Siapps Standalone Builder

echo ===================================================
echo    SIAPPS STANDALONE BUNDLE BUILDER (DAPODIK STYLE)
echo ===================================================
echo.
echo Skrip ini akan menyalin seluruh aplikasi dan mensentralisasikannya
echo ke dalam folder 'dist' tanpa merusak folder development (XAMPP).
echo.

:: Tentukan Direktori Sumber
set "SOURCE_DIR=%~dp0.."
set "DEST_DIR=%~dp0dist"
set "ROBOCOPY_CMD=%SystemRoot%\System32\robocopy.exe"
set "ATTRIB_CMD=%SystemRoot%\System32\attrib.exe"

:: Pastikan membersihkan folder dist lama jika ada
if exist "%DEST_DIR%" (
    echo [INFO] Menghapus build lama...
    rmdir /s /q "%DEST_DIR%"
)

:: Buat Struktur Direktori Standalone
echo [INFO] Membuat struktur direktori baru...
mkdir "%DEST_DIR%"
mkdir "%DEST_DIR%\php"
mkdir "%DEST_DIR%\database"
mkdir "%DEST_DIR%\dataweb"
mkdir "%DEST_DIR%\bin"

echo.
echo [1/3] Memajang Mesin PHP (php)...
:: Meng-copy dari bin\php ke dist\php
"%ROBOCOPY_CMD%" "%SOURCE_DIR%\bin\php" "%DEST_DIR%\php" /E /MT:8 /NDL /NFL /NJH /NJS

echo [2/3] Memajang Mesin Database (database)...
:: Meng-copy dari bin\mysql ke dist\database
"%ROBOCOPY_CMD%" "%SOURCE_DIR%\bin\mysql" "%DEST_DIR%\database" /E /MT:8 /NDL /NFL /NJH /NJS

:: Membersihkan attribute Read-Only pada database agar tidak crash
"%ATTRIB_CMD%" -R "%DEST_DIR%\database\*.*" /S /D

echo [SSL] Memperbaiki Sertifikat HTTPS untuk Mesin PHP dan MySQL...
powershell -Command "(gc '%DEST_DIR%\database\bin\my.ini' -Encoding UTF8) -replace 'D:/XAMPP/mysql', '' | Out-File '%DEST_DIR%\database\bin\my.ini' -Encoding UTF8"
copy "%SOURCE_DIR%\..\apache\bin\curl-ca-bundle.crt" "%DEST_DIR%\php\curl-ca-bundle.crt" >nul
powershell -Command "(gc '%DEST_DIR%\php\php.ini' -Encoding UTF8) -replace 'D:\\XAMPP\\apache\\bin\\curl-ca-bundle.crt', 'curl-ca-bundle.crt' | Out-File '%DEST_DIR%\php\php.ini' -Encoding UTF8"

echo [3/3] Memajang Source Code Website (dataweb)...
:: Clone seluruh codebase KECUALI folder-folder eksternal (node_modules, tests, bin, desktop)
:: Kami TETAP menyalin .git agar Klien bisa Update via tombol Sync Web UI
"%ROBOCOPY_CMD%" "%SOURCE_DIR%" "%DEST_DIR%\dataweb" /E /XD node_modules tests bin desktop /MT:8 /NDL /NFL /NJH /NJS

:: Mengamankan file penting ke root bundle
if exist "%SOURCE_DIR%\bin\cloudflared.exe" (
    echo [INFO] Menyertakan Cloudflare Tunnel...
    copy "%SOURCE_DIR%\bin\cloudflared.exe" "%DEST_DIR%\bin\cloudflared.exe" >nul
)

:: Menyalin Startup Scripts khusus dist
echo [INFO] Menyalin Launcher...
copy "%~dp0siapps-start.bat" "%DEST_DIR%\siapps-start.bat" >nul
copy "%~dp0siapps-stop.bat" "%DEST_DIR%\siapps-stop.bat" >nul
copy "%~dp0Siapps.vbs" "%DEST_DIR%\Siapps.vbs" >nul
copy "%~dp0Stop Siapps.vbs" "%DEST_DIR%\Stop Siapps.vbs" >nul

echo.
echo ===================================================
echo                 BUILD SELESAI!
echo ===================================================
echo Cek folder: %DEST_DIR%
echo Strukturnya sekarang murni mandiri (dataweb, php, database).
echo Silakan jalankan 'siapps-start.bat' di dalam folder dist tersebut.
echo.
