<?php

use App\Models\DynamicMenu;
use App\Models\MenuRole;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ADDING LAPORAN SUBSIDI MENU ===\n\n";

// 1. Find Parent "Laporan & Rekap"
$parent = DynamicMenu::where('title', 'like', '%Laporan%Rekap%')->first();
if (!$parent) {
    // Fallback search
    $parent = DynamicMenu::find(53);
}

if (!$parent) {
    die("Error: Parent menu 'Laporan & Rekap' not found.\n");
}

echo "Parent Found: [{$parent->id}] {$parent->title}\n";

// 2. Check existing
$existing = DynamicMenu::where('title', 'Laporan Subsidi')->first();
if ($existing) {
    \App\Models\MenuRole::where('menu_id', $existing->id)->delete();
    $existing->delete();
    echo "Deleted existing menu to refresh.\n";
}

// 3. Create Menu
$menu = new DynamicMenu();
$menu->parent_id = $parent->id;
$menu->title = 'Laporan Subsidi';
$menu->url = 'keuangan/laporan/subsidi';
$menu->route = 'laporan.subsidi';
$menu->icon = 'verified_user';
$menu->order = 99; // Put last
$menu->is_active = true;
$menu->location = 'sidebar';
$menu->save();

echo "Menu Created: [{$menu->id}] {$menu->title}\n";

// 4. Assign Roles (Copy from Parent or Default)
$roles = ['admin', 'admin_utama', 'kepala_madrasah', 'bendahara'];
// Note: Adjust according to system roles. Assuming 'bendahara' exists if applicable.
// Let's check parent roles if possible, but hardcoding critical ones is safer for now.

foreach ($roles as $role) {
    MenuRole::create([
        'menu_id' => $menu->id,
        'role' => $role
    ]);
    echo "- Role assigned: $role\n";
}

echo "\nSuccess! Please refresh the page.\n";
