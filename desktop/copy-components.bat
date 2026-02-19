@echo off
setlocal enableextensions

echo ==========================================
echo      SIAPPS DESKTOP SETUP WIZARD v3
echo ==========================================
echo.

set "SOURCE_XAMPP=D:\XAMPP"
set "DEST_BIN=%~dp0..\bin"
set "XCOPY_CMD=%SystemRoot%\System32\xcopy.exe"
set "ATTRIB_CMD=%SystemRoot%\System32\attrib.exe"

echo [DIAGNOSTIC] Cek Kondisi Awal...
if exist "%SOURCE_XAMPP%\php\php.exe" (
    echo [OK] Sumber PHP.exe ditemukan.
) else (
    echo [ERROR] PHP.exe TIDAK DITEMUKAN di "%SOURCE_XAMPP%\php\"
    pause
    exit
)

if exist "%DEST_BIN%" (
    echo [INFO] Folder bin sudah ada.
) else (
    mkdir "%DEST_BIN%"
    echo [INFO] Folder bin dibuat.
)

echo.
echo ==========================================
echo      MULAI MENYALIN KOMPONEN
echo ==========================================
echo.

:: ---------------- PHP ----------------
echo [1/2] Menyalin PHP...
if not exist "%DEST_BIN%\php" (
    mkdir "%DEST_BIN%\php"
)

echo       Menyalin file dari %SOURCE_XAMPP%\php...
"%XCOPY_CMD%" "%SOURCE_XAMPP%\php" "%DEST_BIN%\php\" /E /I /H /Y /Q >nul

if exist "%DEST_BIN%\php\php.exe" (
    echo [OK] PHP Berhasil Disalin.
) else (
    echo [FATAL ERROR] Copy gagal! php.exe tidak masuk ke folder tujuan.
    echo Cek apakah antivirus memblokir?
    pause
    exit
)

:: ---------------- MySQL ----------------
echo.
echo [2/2] Menyalin MySQL...
if not exist "%DEST_BIN%\mysql" (
    mkdir "%DEST_BIN%\mysql"
)

:: Reset attribute read-only di tujuan kalau ada (biar bisa ditimpa)
if exist "%DEST_BIN%\mysql" (
    "%ATTRIB_CMD%" -R "%DEST_BIN%\mysql\*.*" /S /D >nul
)

echo       Menyalin file dari %SOURCE_XAMPP%\mysql...
"%XCOPY_CMD%" "%SOURCE_XAMPP%\mysql" "%DEST_BIN%\mysql\" /E /I /H /Y /Q >nul

if exist "%DEST_BIN%\mysql\bin\mysqld.exe" (
    echo [OK] MySQL Berhasil Disalin.

    :: FIX PERMISSION (Penting buat ibdata1)
    echo       Memperbaiki permission data...
    "%ATTRIB_CMD%" -R "%DEST_BIN%\mysql\*.*" /S /D
) else (
    echo [FATAL ERROR] Copy gagal! mysqld.exe tidak masuk.
    pause
    exit
)

echo.
echo ==========================================
echo           SETUP SELESAI & SUKSES!
echo ==========================================
echo Folder 'bin' sudah lengkap.
echo Silakan jalankan 'start-app.bat' sekarang.
echo.
pause
