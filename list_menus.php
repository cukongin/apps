<?php

use App\Models\DynamicMenu;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== EXISTING MENUS ===\n";
$menus = DynamicMenu::whereNull('parent_id')->orderBy('order')->get();

foreach ($menus as $menu) {
    echo "[{$menu->id}] {$menu->title} (URL: {$menu->url})\n";
    foreach ($menu->children as $child) {
        echo "   - [{$child->id}] {$child->title} (URL: {$child->url})\n";
    }
}
