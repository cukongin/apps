<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaran;
use App\Models\Mapel;
use App\Models\KkmMapel;
use App\Models\GlobalSetting;

class KkmHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Get Default KKMs
        $defaultKkmMI = (int) GlobalSetting::val('kkm_default_mi', 70);
        $defaultKkmMTS = (int) GlobalSetting::val('kkm_default_mts', 75); // Use 75 if not set, or fall back to 70

        $this->command->info("Defaults detected -> MI: $defaultKkmMI, MTS: $defaultKkmMTS");

        // 2. Get All Years
        $years = TahunAjaran::all();
        $mapels = Mapel::all();
        $jenjangs = ['MI', 'MTS'];

        foreach ($years as $year) {
            $this->command->info("Processing Year: {$year->nama}");
            $count = 0;

            foreach ($mapels as $mapel) {
                // Determine Target Jenjang for this Mapel
                // If mapel has target_jenjang, serve only that.
                // If 'SEMUA' or null, serve both.

                $targets = [];
                if ($mapel->target_jenjang == 'MI') $targets = ['MI'];
                elseif ($mapel->target_jenjang == 'MTS') $targets = ['MTS'];
                else $targets = ['MI', 'MTS'];

                foreach ($targets as $jenjang) {
                    // Check if exists
                    $exists = KkmMapel::where('id_tahun_ajaran', $year->id)
                        ->where('id_mapel', $mapel->id)
                        ->where('jenjang_target', $jenjang)
                        ->exists();

                    if (!$exists) {
                        $kkmValue = ($jenjang == 'MI') ? $defaultKkmMI : $defaultKkmMTS;

                        KkmMapel::create([
                            'id_tahun_ajaran' => $year->id,
                            'id_mapel' => $mapel->id,
                            'jenjang_target' => $jenjang,
                            'nilai_kkm' => $kkmValue
                        ]);
                        $count++;
                    }
                }
            }
            $this->command->info("  - Created $count KKM records.");
        }
    }
}
