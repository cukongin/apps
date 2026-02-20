<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DynamicMenu;
use App\Models\MenuRole;

class AddSubsidyMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Find Parent "Laporan & Rekap"
        $parent = DynamicMenu::where('title', 'like', '%Laporan%Rekap%')->first();

        // Fallback search
        if (!$parent) {
            $parent = DynamicMenu::find(53);
        }

        if ($parent) {
            // 2. Check existing to avoid duplicates
            $existing = DynamicMenu::where('title', 'Laporan Subsidi')->first();

            if (!$existing) {
                // 3. Create Menu
                $menu = new DynamicMenu();
                $menu->parent_id = $parent->id;
                $menu->title = 'Laporan Subsidi';
                $menu->url = 'keuangan/laporan/subsidi';
                $menu->route = 'keuangan.laporan.subsidi'; // Corrected route name
                $menu->icon = 'verified_user';
                $menu->order = 99; // Put last
                $menu->is_active = true;
                $menu->location = 'sidebar';
                $menu->save();

                // 4. Assign Roles
                $roles = ['admin', 'admin_utama', 'kepala_madrasah', 'bendahara'];
                foreach ($roles as $role) {
                    MenuRole::create([
                        'menu_id' => $menu->id,
                        'role' => $role
                    ]);
                }
            } else {
                // Ensure route name is correct if exists
                if ($existing->route !== 'keuangan.laporan.subsidi') {
                    $existing->route = 'keuangan.laporan.subsidi';
                    $existing->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $menu = DynamicMenu::where('title', 'Laporan Subsidi')->first();
        if ($menu) {
            MenuRole::where('menu_id', $menu->id)->delete();
            $menu->delete();
        }
    }
}
