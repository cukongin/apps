<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING ROUTE: laporan.subsidi ===\n\n";

try {
    $url = route('laporan.subsidi');
    echo "SUCCESS: $url\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== DUMPING ROUTES ===\n";
// $routes = Route::getRoutes();
// foreach ($routes as $route) {
//     if (strpos($route->getName(), 'laporan') !== false) {
//         echo $route->getName() . " -> " . $route->uri() . "\n";
//     }
// }
