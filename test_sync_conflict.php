<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

echo "--- Testing Sync System: Safe Push (updated_at Check) ---\n";

// 1. Get an existing Siswa
$siswa = DB::table('siswa')->first();
if (!$siswa) {
    die("No siswa found in DB to test.\n");
}

echo "Testing on Siswa ID: {$siswa->id}\n";
echo "Server Current Name : {$siswa->nama_lengkap}\n";
echo "Server updated_at   : {$siswa->updated_at}\n\n";

// 2. Prepare a "Stale" Payload (Simulating an old offline edit)
// The updated_at is deliberately set to older than the server's
$staleTime = date('Y-m-d H:i:s', strtotime($siswa->updated_at . ' - 1 hour'));
$stalePayload = [
    'siswa' => [
        [
            'id' => $siswa->id,
            'nama_lengkap' => 'STALE NAME SHOULD NOT OVERWRITE',
            'updated_at' => $staleTime
        ]
    ]
];

echo "Simulating Stale Push:\n";
echo "Client incoming Name: STALE NAME SHOULD NOT OVERWRITE\n";
echo "Client updated_at   : {$staleTime}\n";

// 3. Prepare an explicit POST request to receiveFinancePush
$request = Illuminate\Http\Request::create('/api/sync/finance-push', 'POST', $stalePayload);
$controller = new \App\Http\Controllers\Api\SyncController();
$response = $controller->receiveFinancePush($request);

$result = json_decode($response->getContent(), true);

echo "\n--- Result ---\n";
echo "Success: " . ($result['success'] ? 'true' : 'false') . "\n";
echo "Message: " . $result['message'] . "\n";
echo "Stats Siswa Skipped : " . ($result['stats']['siswa']['skipped'] ?? 0) . " (Should be > 0)\n";
echo "Stats Siswa Updated : " . ($result['stats']['siswa']['updated'] ?? 0) . " (Should be exactly 0)\n";

// 4. Verify DB hasn't changed
$siswaAfter = DB::table('siswa')->where('id', $siswa->id)->first();
echo "\n--- Verification ---\n";
echo "Server Name After Sync : {$siswaAfter->nama_lengkap}\n";
if ($siswaAfter->nama_lengkap === $siswa->nama_lengkap) {
    echo "✅ TEST PASSED: Server rejected stale data.\n";
} else {
    echo "❌ TEST FAILED: Server data was overwritten!\n";
}
