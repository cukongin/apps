<?php

use App\Keuangan\Models\Tagihan;
use App\Keuangan\Services\BillService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SIMULATING 50% SUBSIDY ===\n\n";

// 1. Find a bill that is Not Paid (terbayar=0) and > 0
$bill = Tagihan::where('terbayar', 0)
    ->where('jumlah', '>', 10000)
    ->with('siswa.kelas')
    ->first();

if (!$bill) {
    die("No suitable unpaid bill found for testing.\n");
}

echo "Target Bill: ID {$bill->id} | Siswa: {$bill->siswa->nama} | Amount: " . number_format($bill->jumlah) . "\n";

// 2. Apply 50% Discount
$discountAmount = $bill->jumlah * 0.5;
echo "Applying 50% Discount: " . number_format($discountAmount) . "\n";

// Use Reflection to access private method or just copy logic?
// BillService::processDiscountUpdate is private.
// But wait, the user applies discounts via Config -> Sync.
// Or effectively, I can just CREATE the subsidy transaction manually as if the system did it.

DB::transaction(function() use ($bill, $discountAmount) {
    \App\Keuangan\Models\Transaksi::create([
        'tagihan_id' => $bill->id,
        'jumlah_bayar' => $discountAmount,
        'metode_pembayaran' => 'Subsidi',
        'keterangan' => 'Test Manual: Diskon 50% - Uji Coba Laporan',
        'created_at' => Carbon::now()
    ]);

    $bill->increment('terbayar', $discountAmount);
    // BillService::updateStatus($bill); // Ensure status updates
});

echo "Success. A 50% subsidy transaction has been created.\n";
echo "Please check Laporan Harian now.\n";
