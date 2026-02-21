<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== TAGIHAN 415 ===\n";
$t = \App\Keuangan\Models\Tagihan::with('transaksis')->find(415);
if ($t) {
    echo "Tagihan {$t->id} | Jml: {$t->jumlah} | Terbayar: {$t->terbayar} | Status: {$t->status}\n";
    foreach($t->transaksis as $tr) {
        echo "   -> Trx {$tr->id} | {$tr->metode_pembayaran} | {$tr->jumlah_bayar} | {$tr->keterangan}\n";
    }
} else {
    echo "Tagihan 415 NOT FOUND!\n";
}

// Cek apakah Tagihan Baru untuk jenis biaya yang sama dan siswa yang sama ada?
// 415 dulu miliknya siswa siapa?
// Di debug_diskon50.php: 415 adalah SPP (ID 5).
// Let's get all Tagihan SPP for student of 415.
// But we don't know student ID if 415 is gone.
