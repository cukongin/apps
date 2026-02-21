<?php

use App\Models\Siswa;
use App\Keuangan\Services\BillService;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Find Student
$siswa = Siswa::where('nama_lengkap', 'like', '%Ali bin Abi Thalib%')->first();

if (!$siswa) {
    echo "Siswa Ali Not Found.\n";
    exit;
}

echo "Triggering Sync for Ali (ID: {$siswa->id})...\n";

// Clear previous logs
file_put_contents('d:/XAMPP/htdocs/siapps/debug_bill_service.log', "");

// Delete existing bills
foreach($siswa->tagihans as $t) {
    $t->transaksis()->delete();
    $t->delete();
}

// 2. Manual Test
echo "Running Manual Transaction Test...\n";
$validBiayaId = \App\Keuangan\Models\JenisBiaya::first()->id;
DB::beginTransaction();
try {
    $t = \App\Keuangan\Models\Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_biaya_id' => $validBiayaId,
        'jumlah' => 10000,
        'status' => 'belum',
        'terbayar' => 0
    ]);
    echo "Tagihan Created: {$t->id}\n";

    $tr = \App\Keuangan\Models\Transaksi::create([
        'tagihan_id' => $t->id,
        'jumlah_bayar' => 10000,
        'metode_pembayaran' => 'Subsidi',
        'keterangan' => 'Test Manual'
    ]);
    echo "Transaksi Created: {$tr->id}\n";

    $t->increment('terbayar', 10000);
    echo "Tagihan Updated Terbayar.\n";

    DB::commit();
    echo "Manual Test Valid.\n";
} catch (\Exception $e) {
    DB::rollback();
    echo "Manual Test Failed: " . $e->getMessage() . "\n";
}

echo "Total Transactions Before Sync: " . \App\Keuangan\Models\Transaksi::count() . "\n";

// 3. Run Sync (Real Test)
echo "Running Real Sync...\n";
BillService::syncForsiswa($siswa);

echo "Total Transactions After Sync: " . \App\Keuangan\Models\Transaksi::count() . "\n";

// Check Status of New Bills
echo "CHECKING STATUS OF NEW BILLS:\n";
$siswa->refresh(); // Reload relation
foreach($siswa->tagihans as $t) {
    echo "- {$t->jenisBiaya->nama}: Amount={$t->jumlah}, Terbayar={$t->terbayar}, Status={$t->status}\n";
    foreach($t->transaksis as $tr) {
        echo "   * Tx: {$tr->metode_pembayaran} Rp {$tr->jumlah_bayar}\n";
    }
}

echo "Sync Complete. Checking Logs...\n";
echo file_get_contents('d:/XAMPP/htdocs/siapps/debug_bill_service.log');
