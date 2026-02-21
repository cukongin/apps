<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

echo "=== CHECK HASIL SINKRONISASI BIAYA & DISKON ===\n";

// Ambil santri contoh yang memiliki potongan (Misal ID tertentu, mari kita cari yang punya kategori)
$siswa = \App\Models\Siswa::whereNotNull('kategori_keringanan_id')->first();

if (!$siswa) {
    echo "Tidak ada siswa yang punya kategori keringanan / diskon.\n";
    exit;
}

echo "Siswa: " . $siswa->nama . " (Diskon Kategori ID: " . $siswa->kategori_keringanan_id . ")\n";
$kategori = \App\Keuangan\Models\KategoriKeringanan::with('aturanDiskons.jenisBiaya')->find($siswa->kategori_keringanan_id);
echo "Kategori Keringanan: " . $kategori->nama . "\n";
echo "Aturan Diskon:\n";
foreach ($kategori->aturanDiskons as $aturan) {
    echo "- " . $aturan->jenisBiaya->nama . ": " . $aturan->jumlah . ($aturan->tipe_diskon == 'persen' ? '%' : ' Rupiah') . "\n";
}

echo "\n--- MENJALANKAN SYNC TAGIHAN ---\n";
\App\Keuangan\Services\BillService::syncForsiswa($siswa);

// Cek tagihan
echo "\n--- HASIL TAGIHAN ---\n";
$tagihans = \App\Keuangan\Models\Tagihan::where('siswa_id', $siswa->id)
    ->with(['jenisBiaya', 'transaksis' => function($q) {
        $q->where('metode_pembayaran', 'Subsidi');
    }])->get();

foreach ($tagihans as $t) {
    echo "Tagihan: " . $t->jenisBiaya->nama;
    echo " | Nominal Asli: Rp " . number_format($t->jumlah, 0, ',', '.');

    $subsidi = $t->transaksis->sum('jumlah_bayar');
    echo " | Subsidi: Rp " . number_format($subsidi, 0, ',', '.');

    $sisa = $t->jumlah - $t->terbayar;
    echo " | Total Terbayar: Rp " . number_format($t->terbayar, 0, ',', '.');
    echo " | SISA TAGIHAN MAHASISWA: Rp " . number_format($sisa, 0, ',', '.') . "\n";
}

echo "\nDONE.\n";
