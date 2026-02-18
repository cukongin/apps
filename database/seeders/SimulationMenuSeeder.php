<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimulationMenuSeeder extends Seeder
{
    public function run()
    {
        // 1. Check if menu exists
        $menuValue = DB::table('dynamic_menus')->where('route', 'keuangan.simulation.index')->first();

        if (!$menuValue) {
            // 2. Insert Menu
            $menuId = DB::table('dynamic_menus')->insertGetId([
                'title' => 'Simulasi Tagihan',
                'icon' => 'science',
                'route' => 'keuangan.simulation.index',
                'url' => null,
                'parent_id' => null, // Top Level
                'order' => 95,
                'is_active' => 1,
                'location' => 'sidebar',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "Menu inserted with ID: $menuId\n";

            // 3. Insert Roles
            $roles = ['admin_utama', 'bendahara'];
            foreach ($roles as $role) {
                DB::table('menu_roles')->insert([
                    'menu_id' => $menuId,
                    'role' => $role
                ]);
            }
            echo "Roles assigned.\n";

        } else {
            echo "Menu already exists (ID: {$menuValue->id}).\n";

            // Check roles just in case
            $roles = ['admin_utama', 'bendahara'];
            foreach ($roles as $role) {
                $exists = DB::table('menu_roles')
                    ->where('menu_id', $menuValue->id)
                    ->where('role', $role)
                    ->exists();

                if (!$exists) {
                    DB::table('menu_roles')->insert([
                        'menu_id' => $menuValue->id,
                        'role' => $role
                    ]);
                    echo "Role $role added to existing menu.\n";
                }
            }
        }
    }
}
