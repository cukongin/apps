<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Menyimulasikan request AJAX ke endpoint Pembayaran
echo "=== SIMULASI PEMBAYARAN AJAX BARU ===\n";

// Bikin dummy request POST yang valid
$ctrl = new \App\Keuangan\Http\Controllers\TransaksiController(new \App\Services\FinancialService());
$request = new \Illuminate\Http\Request();
$request->setMethod('POST');
$request->merge([
    'bills' => [
        '415' => '5000'
    ],
    'tagihan_id' => ['415'],
    'metode' => 'tunai'
]);
// Simulate Ajax
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

try {
    // Jalankan controller store dengan asumsi ID santri = 30
    $res = $ctrl->store($request, 30);
    echo "Status Code: " . $res->getStatusCode() . "\n";
    echo "Content: \n" . $res->getContent() . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
