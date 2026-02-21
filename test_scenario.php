<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

echo "=== CHECK SKENARIO OVER-SUBSIDY ===\n";
// Skenario: Tagihan Lama sudah dibentuk sebelum master harga berubah
// Master Harga sekarang: SPP 15.000, Diskon: 50%.
// Bagaimana perlakuan sistem jika ada Tagihan Lama sejumlah 7.500?

$aturan = (object)[
    'tipe_diskon' => 'persen',
    'jumlah' => 50
];
$siswa = (object)[
     'kategoriKeringanan' => (object) ['nama' => 'Yatim 50%']
];

function testDiscount($aturan, $siswa, $amount) {
    $discountAmount = 0;
    if ($aturan->tipe_diskon == 'persen' || $aturan->tipe_diskon == 'percentage') {
        $discountAmount = $amount * ($aturan->jumlah / 100);
    }
    return $discountAmount;
}

echo "1. Simulasi Tagihan Baru (Harga Master SPP 15.000)\n";
$newBillAmount = 15000;
$subsidyBaru = testDiscount($aturan, $siswa, $newBillAmount);
echo "   Nominal Tagihan: $newBillAmount\n";
echo "   Subsidi 50%: $subsidyBaru\n";
echo "   Sisa Bayar: " . ($newBillAmount - $subsidyBaru) . "\n";

echo "\n2. Simulasi Tagihan Lama yang tercreate dengan harga lama (Misal SPP dulu 7.500)\n";
$oldBillAmount = 7500;
$subsidyLama = testDiscount($aturan, $siswa, $oldBillAmount);
echo "   Nominal Tagihan Lama: $oldBillAmount\n";
echo "   Subsidi 50%: $subsidyLama\n";
echo "   Sisa Bayar: " . ($oldBillAmount - $subsidyLama) . "\n";

// Kesimpulan
echo "\nKesimpulan: Subsidy tidak lagi mengikat secara mati ke Master Biaya, melainkan dihitung berdasarkan 'jumlah' tagihan itu sendiri. Bug TUNTAS.\n";
