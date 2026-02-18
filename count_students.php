<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$miCount = \App\Models\Siswa::whereHas('kelas', function($q){
    $q->where('tingkat_kelas', '<', 7);
})->count();

$mtsCount = \App\Models\Siswa::whereHas('kelas', function($q){
    $q->where('tingkat_kelas', '>=', 7);
})->count();

echo "MI_Students: $miCount\n";
echo "MTS_Students: $mtsCount\n";
