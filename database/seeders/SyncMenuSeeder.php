<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DynamicMenu;
use App\Models\MenuRole;

class SyncMenuSeeder extends Seeder
{
    public function run()
    {
        // Find Parent "Pengaturan"
        $parent = DynamicMenu::where('title', 'Looking For Pengaturan...')->first();
        // Wait, title might vary. Let's look for icon 'settings' or title 'Pengaturan'
        $parent = DynamicMenu::where('title', 'Pengaturan')->first();

        if (!$parent) {
            $this->command->error("Menu 'Pengaturan' not found. Creating it...");
            $parent = DynamicMenu::create([
                'title' => 'Pengaturan',
                'icon' => 'settings',
                'order' => 99
            ]);
            // Assign admin role
             MenuRole::create(['menu_id' => $parent->id, 'role' => 'admin']);
        }

        // Check if Sync menu already exists
        $exists = DynamicMenu::where('route', 'settings.sync.index')->exists();

        if (!$exists) {
            $menu = DynamicMenu::create([
                'title' => 'Sinkronisasi Data',
                'icon' => 'sync', // Material Symbol
                'route' => 'settings.sync.index',
                'parent_id' => $parent->id,
                'order' => 99, // Last item
                'is_active' => 1
            ]);

            // Assign to Admin
            MenuRole::create([
                'menu_id' => $menu->id,
                'role' => 'admin'
            ]);

            $this->command->info("Menu 'Sinkronisasi Data' created successfully.");
        } else {
            $this->command->info("Menu 'Sinkronisasi Data' already exists.");
        }
    }
}
