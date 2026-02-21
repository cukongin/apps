<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

$tr2 = \Illuminate\Support\Facades\DB::table('transaksis')->where('id', 157)->first();
if ($tr2) {
    echo "Trx 157 found via DB raw: tagihan_id = {$tr2->tagihan_id}\n";
} else {
    echo "Trx 157 completely gone from DB.\n";
}

$tr92 = \Illuminate\Support\Facades\DB::table('transaksis')->where('id', 92)->first();
if ($tr92) {
    echo "Trx 92 found via DB raw: tagihan_id = {$tr92->tagihan_id}\n";
} else {
    echo "Trx 92 completely gone from DB.\n";
}
