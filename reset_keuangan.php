<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== MEMULAI RESET DATABASE KEUANGAN ===\n";

try {
    // Disable foreign key checks to allow truncating
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // Truncate tables
    \App\Keuangan\Models\Transaksi::truncate();
    echo "Tabel Transaksi (transaksis) berhasil direset.\n";

    \App\Keuangan\Models\Tagihan::truncate();
    echo "Tabel Tagihan (tagihans) berhasil direset.\n";

    // Enable foreign key checks back
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    echo "\nReset Selesai! Saat ini tidak ada satupun tagihan dan pembayaran yang tercatat di sistem.\n";
    echo "Bosku bisa mulai men-generate ulang tagihan dari awal.\n";
} catch (\Exception $e) {
    echo "Gagal melakukan reset: " . $e->getMessage() . "\n";
}
