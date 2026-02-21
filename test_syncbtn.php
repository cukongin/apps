<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Menyimulasikan controller call
echo "=== SIMULASI MENEKAN TOMBOL TONGKAT AJAIB (Siswa 30) ===\n";
$controller = new \App\Keuangan\Http\Controllers\SantriKeuanganController();
$controller->sync(30);
echo "Selesai Eksekusi Sync Siswa 30\n";
