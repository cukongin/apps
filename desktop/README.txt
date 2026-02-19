PANDUAN MENJALANKAN APLIKASI (MODE DESKTOP)
==============================================

1. Pastikan folder ini berisi file sistem lengkap (Laravel + Vendor).
2. Folder "php" dan "mysql" (Portable) harus ada di luar folder aplikasi ini (sejajar dengan folder project).

STRUKTUR FOLDER YANG DIHARAPKAN (GABUNGAN):
/Siapps-Portable
  |-- /bin
  |    |-- /mysql       <-- Masukkan folder MySQL Portable di sini
  |    |-- /php         <-- Masukkan folder PHP Portable di sini
  |
  |-- /app              <-- Source Code Laravel (Project ini)
  |-- /desktop          <-- Script Launcher ini
       |-- start-app.bat
       |-- stop-app.bat

CATATAN:
Agar lebih rapi, Boss bisa memindahkan semua isi project ini ke dalam folder baru, lalu buat folder "bin" di dalamnya.
Atau kalau mau cepat, cukup buat folder "bin" di dalam folder project ini.

STRUKTUR OPSI 2 (LANGSUNG DI DALAM PROJECT - RECOMMENDED):
/siapps (Project Ini)
  |-- /bin          <-- AKAN DIBUAT OTOMATIS OLEH SCRIPT "copy-components.bat"
       |-- /mysql
       |-- /php
  |-- /desktop
       |-- copy-components.bat  <-- KLIK INI DULU UNTUK SETUP PERTAMA KALI
       |-- start-app.bat        <-- SETELAH ITU KLIK INI UNTUK MAIN
  |-- .env
  |-- artisan

LANGKAH SETUP CEPAT:
1. Klik ganda "desktop/copy-components.bat". Tunggu sampai selesai menyalin PHP & MySQL dari XAMPP.
2. JIka sudah selesai, klik "desktop/start-app.bat".
3. Aplikasi siap digunakan!

CARA MENGGUNAKAN:
- Klik ganda "start-app.bat" di dalam folder "desktop" (atau buat shortcut-nya ke Desktop).
- Aplikasi akan terbuka otomatis di browser.
- Jangan tutup jendela cmd hitam agar server tetap jalan.

JIKA ADA MASALAH PORT:
- Edit file "start-app.bat" dengan Notepad.
- Ubah WEB_PORT atau DB_PORT jika bentrok.
