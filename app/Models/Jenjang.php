<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenjang extends Model
{
    use HasFactory;

    protected $table = 'jenjang';
    protected $guarded = ['id'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_jenjang');
    }

    /**
     * Get Academic Settings for a specific Jenjang Code (e.g., 'MI', 'MTS', 'TPQ')
     * Replaces the legacy 'grading_settings' table.
     */
    public static function getSettings($kode)
    {
        $jkl = strtolower($kode);
        $key = 'kkm_default_' . $jkl; // Marker key

        // Check if migrated (Simple check: is Marker Key present?)
        $exists = \App\Models\GlobalSetting::where('key', $key)->exists();

        if (!$exists) {
            // Attempt Lazy Migration from Legacy Table
            try {
                $legacy = \Illuminate\Support\Facades\DB::table('grading_settings')
                    ->where('jenjang', strtoupper($kode)) // Enum usually uppercase
                    ->first();

                if ($legacy) {
                    // Migrate
                    \App\Models\GlobalSetting::updateOrCreate(['key' => 'kkm_default_' . $jkl], ['value' => $legacy->kkm_default]);
                    \App\Models\GlobalSetting::updateOrCreate(['key' => 'promotion_max_kkm_failure_' . $jkl], ['value' => $legacy->promotion_max_kkm_failure]);
                    \App\Models\GlobalSetting::updateOrCreate(['key' => 'promotion_min_attendance_' . $jkl], ['value' => $legacy->promotion_min_attendance]);
                    \App\Models\GlobalSetting::updateOrCreate(['key' => 'promotion_min_attitude_' . $jkl], ['value' => $legacy->promotion_min_attitude]);
                    \App\Models\GlobalSetting::updateOrCreate(['key' => 'promotion_requires_all_periods_' . $jkl], ['value' => $legacy->promotion_requires_all_periods]);
                    \App\Models\GlobalSetting::updateOrCreate(['key' => 'total_effective_days_' . $jkl], ['value' => $legacy->effective_days_year]);
                    \App\Models\GlobalSetting::updateOrCreate(['key' => 'rounding_enable_' . $jkl], ['value' => $legacy->rounding_enable ?? 1]);
                }
            } catch (\Exception $e) {
                // Ignore table missing error, stick to defaults
            }
        }

        return (object) [
            'jenjang' => $kode,
            'kkm_default' => \App\Models\GlobalSetting::val('kkm_default_' . $jkl, 70),
            'rounding_enable' => \App\Models\GlobalSetting::val('rounding_enable_' . $jkl, 1),
            'promotion_max_kkm_failure' => \App\Models\GlobalSetting::val('promotion_max_kkm_failure_' . $jkl, 3),
            'promotion_min_attendance' => \App\Models\GlobalSetting::val('promotion_min_attendance_' . $jkl, 60),
            'promotion_min_attitude' => \App\Models\GlobalSetting::val('promotion_min_attitude_' . $jkl, 'C'),
            'promotion_requires_all_periods' => \App\Models\GlobalSetting::val('promotion_requires_all_periods_' . $jkl, 1),
            'effective_days_year' => \App\Models\GlobalSetting::val('total_effective_days_' . $jkl, 200),

            // Dates (Helpers)
            'titimangsa' => \App\Models\GlobalSetting::val('titimangsa_' . $jkl, '-'),
            'titimangsa_tempat' => \App\Models\GlobalSetting::val('titimangsa_tempat_' . $jkl, '-'),
        ];
    }
}
