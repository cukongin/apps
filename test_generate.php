<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

echo "=== CHECK GENERATE FUTURE BILLS ===\n";

// Mengambil siswa dengan kategori diskon
$siswa = \App\Models\Siswa::whereNotNull('kategori_keringanan_id')->first();
if (!$siswa) {
    echo "Tidak ada siswa yang punya diskon. Skip test.";
    exit;
}
echo "Siswa: " . $siswa->nama . "\n";

// Catat jumlah sebelum
$before = \App\Keuangan\Models\Tagihan::where('siswa_id', $siswa->id)->count();
echo "Total Tagihan Sebelum Generate: $before\n";

// Generate 3 bulan ke depan mulai bulan depan (misal: bulan dpn tgl 5)
$startDate = \Carbon\Carbon::now()->addMonth()->format('Y-m-05');
echo "Generate 3 Bulan dimulai dari: $startDate\n";

$added = \App\Keuangan\Services\BillService::generateFutureBills($siswa, 3, $startDate);

echo "Total Data Ditambahkan: $added baris.\n";

// Catat sesudah
$after = \App\Keuangan\Models\Tagihan::where('siswa_id', $siswa->id)->count();
echo "Total Tagihan Sesudah Generate: $after\n";

echo "\n--- CEK DETAIL TAGIHAN BARU (3 TERAKHIR) ---\n";
$newBills = \App\Keuangan\Models\Tagihan::where('siswa_id', $siswa->id)
    ->with('jenisBiaya')
    ->orderBy('created_at', 'desc')
    ->take($added)->get();

foreach ($newBills as $b) {
    echo "• " . $b->keterangan . " | Nominal Asli: " . $b->jumlah . " | Terbayar: " . $b->terbayar . " | Status: " . $b->status . "\n";
}

// Rollback / Clean up untuk test (supaya database tidak numpuk)
if ($added > 0) {
    // Delete tagihan baru
    $ids = $newBills->pluck('id')->toArray();
    \App\Keuangan\Models\Transaksi::whereIn('tagihan_id', $ids)->delete();
    \App\Keuangan\Models\Tagihan::whereIn('id', $ids)->delete();
    echo "\nTest cleanup: Data simulasi tagihan berhasil dihapus.\n";
}
