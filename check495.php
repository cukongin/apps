<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "Transaksi Tagihan 495:\n";
foreach(\App\Keuangan\Models\Transaksi::where('tagihan_id', 495)->get() as $tr) {
    echo $tr->id . ' - ' . $tr->metode_pembayaran . ' - ' . $tr->jumlah_bayar . "\n";
}
