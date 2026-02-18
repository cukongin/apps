<?php

namespace App\Services;

use App\Models\TahunAjaran;
use App\Models\Periode;
use Illuminate\Support\Facades\Cache;

class AcademicService
{
    /**
     * Get the currently active Academic Year.
     * Uses generic request caching to avoid multiple DB hits in one request.
     */
    public function activeYear()
    {
        return Cache::remember('active_year_request_' . request()->id, 60, function() {
            // In a real request lifecycle, we might just use static property,
            // but Cache::remember with short TTL is safe enough or `once()` helper if Laravel 11.
            // For now, standard DB call with simple variable cache pattern usually matches typical service usage.
            static $year;
            if ($year) return $year;

            $year = TahunAjaran::where('status', 'aktif')->first();
            return $year;
        });
    }

    /**
     * Get Active Period for a specific Jenjang.
     * @param string $jenjangKode (MI, MTS, etc)
     */
    public function activePeriod($jenjangKode)
    {
        $year = $this->activeYear();
        if (!$year) return null;

        return Periode::where('id_tahun_ajaran', $year->id)
            ->where('status', 'aktif')
            ->where(function($q) use ($jenjangKode) {
                $q->where('lingkup_jenjang', $jenjangKode) // 'MI'
                  ->orWhere('lingkup_jenjang', strtoupper($jenjangKode)); // 'MTS' vs 'MTs' safety
            })
            ->first();
    }

    /**
     * Get All Active Periods Keyed by Jenjang
     */
    public function activePeriodsMap()
    {
        $year = $this->activeYear();
        if (!$year) return [];

        $periods = Periode::where('id_tahun_ajaran', $year->id)
            ->where('status', 'aktif')
            ->get();

        // Map: 'MI' => PeriodObj, 'MTS' => PeriodObj
        $map = [];
        foreach($periods as $p) {
            $map[strtoupper($p->lingkup_jenjang)] = $p;
        }

        return $map;
    }
}
