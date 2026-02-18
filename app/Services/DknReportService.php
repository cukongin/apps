<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\NilaiSiswa;
use App\Models\NilaiIjazah;
use App\Models\GlobalSetting;
use App\Models\UjianMapel;

class DknReportService
{
    /**
     * Get structured DKN data for a class.
     * Used by both Web View (Archive) and Excel Export.
     */
    public function getDknData(Kelas $kelas)
    {
        // 1. Get Students
        $students = $kelas->anggota_kelas()->with('siswa')->get();
        $studentIds = $students->pluck('id_siswa');

        // 2. Fetch ALL Grades needed
        $allGrades = NilaiSiswa::whereIn('id_siswa', $studentIds)
            ->with(['mapel', 'periode', 'kelas'])
            ->get();

        // 3. Identify Mapels (Respect UjianMapel config if exists, else fallback)
        $jenjang = $kelas->jenjang->kode ?? ($kelas->tingkat_kelas > 6 ? 'MTS' : 'MI');
        $activeYearId = $kelas->id_tahun_ajaran;

        $selectedMapelIds = UjianMapel::where('id_tahun_ajaran', $activeYearId)
                                ->where('jenjang', $jenjang)
                                ->pluck('id_mapel');

        if ($selectedMapelIds->isNotEmpty()) {
             $mapelIds = $selectedMapelIds;
             $mapels = Mapel::whereIn('id', $mapelIds)
                            ->orderBy('kategori', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();
        } else {
             $mapelIds = $allGrades->pluck('id_mapel')->unique();
             // Fallback: Filter by Jenjang to avoid cross-contamination (e.g. MI mapels in MTs)
             $mapels = Mapel::whereIn('id', $mapelIds)
                            ->where(function($q) use ($jenjang) {
                                $q->where('target_jenjang', $jenjang)
                                  ->orWhere('target_jenjang', 'SEMUA') // Include General Subjects
                                  ->orWhereNull('target_jenjang')
                                  ->orWhere('target_jenjang', 'LIKE', '%'.$jenjang.'%'); // In case of "MI,MTS"
                            })
                            ->orderBy('kategori', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();
        }

        // 4. Fetch Saved Ijazah Grades
        $ijazahGrades = NilaiIjazah::whereIn('id_siswa', $studentIds)->get();

        // 5. Structure Data
        $dknData = [];

        // Determine Range based on Jenjang
        $targetLevels = $this->getTargetLevels($jenjang);

        // Fetch Period Config
        $jkl = strtolower($jenjang);
        $periodLabel = GlobalSetting::val('ijazah_period_label_' . $jkl, ($jenjang === 'MTS' ? 'Semester' : 'Catur Wulan'));
        $periodCount = GlobalSetting::val('ijazah_period_count_' . $jkl, ($jenjang === 'MTS' ? 2 : 3));

        $periods = range(1, $periodCount);
        $periodLabelShort = ($periodLabel == 'Semester') ? 'Smt' : 'Cawu';

        foreach ($students as $ak) {
            $sId = $ak->id_siswa;
            $sGrades = $allGrades->where('id_siswa', $sId);
            $sIjazah = $ijazahGrades->where('id_siswa', $sId);

            $studentData = [
                'student' => $ak->siswa,
                'data' => [],
                'summary' => []
            ];

            // Organize by Level (Tingkat) -> Periode
            foreach ($targetLevels as $lvl) {
                // Try Absolute Level First
                $lvlGrades = $sGrades->filter(fn($g) => $g->kelas && $g->kelas->tingkat_kelas == $lvl);

                // Fallback for MTs (if stored as 1-3 relative)
                if ($lvlGrades->isEmpty() && $jenjang === 'MTS') {
                     $relativeLvl = $lvl - 6;
                     $lvlGrades = $sGrades->filter(fn($g) => $g->kelas && $g->kelas->tingkat_kelas == $relativeLvl);
                }

                foreach ($periods as $p) {
                    $pGrades = $lvlGrades->filter(function($g) use ($p, $periodLabelShort) {
                        if (!$g->periode) return false;
                        $pName = $g->periode->nama_periode;

                        // Strict Number Check
                        if (stripos($pName, (string)$p) !== false) return true;

                        // Semantic Check
                        if ($periodLabelShort == 'Smt') {
                            if ($p == 1 && stripos($pName, 'Ganjil') !== false) return true;
                            if ($p == 2 && stripos($pName, 'Genap') !== false) return true;
                        } elseif ($periodLabelShort == 'Cawu') {
                             if (stripos($pName, "Cawu $p") !== false) return true;
                        }

                        return false;
                    });

                    $mapelScores = [];
                    foreach ($mapels as $m) {
                        $val = $pGrades->where('id_mapel', $m->id)->sortByDesc('updated_at')->first();
                        $mapelScores[$m->id] = $val ? $val->nilai_akhir : null;
                    }
                    $studentData['data'][$lvl][$p] = $mapelScores;
                }
            }

            // Calculate Summary
            $studentData['summary'] = $this->calculateSummary($mapels, $sIjazah, $jenjang);
            $dknData[] = $studentData;
        }

        return [
            'mapels' => $mapels,
            'dknData' => $dknData,
            'targetLevels' => $targetLevels,
            'periods' => $periods,
            'periodLabel' => $periodLabelShort,
            'jenjang' => $jenjang
        ];
    }

    public function getTargetLevels($jenjang)
    {
        $jkl = strtolower($jenjang);
        $default = ($jenjang === 'MTS') ? '7,8,9' : '4,5,6';

        $config = GlobalSetting::val('ijazah_range_' . $jkl, $default);

        // Convert "4,5,6" string to [4, 5, 6] array
        return array_map('intval', explode(',', $config));
    }

    private function calculateSummary($mapels, $ijazahGrades, $jenjangCode)
    {
        $summary = [
            'rr' => [], // Rata Rapor
            'um' => [], // Ujian Madrasah
            'na' => []  // Nilai Akhir
        ];

        // Fetch configured weights (Jenjang Specific)
        $jkl = strtolower($jenjangCode);
        $bRapor = GlobalSetting::val('ijazah_bobot_rapor_' . $jkl, 60);
        $bUjian = GlobalSetting::val('ijazah_bobot_ujian_' . $jkl, 40);

        $sums = ['rr' => 0, 'um' => 0, 'na' => 0];
        $counts = ['rr' => 0, 'um' => 0, 'na' => 0];

        foreach ($mapels as $m) {
            $ijazahRecord = $ijazahGrades->where('id_mapel', $m->id)->first();
            $rr = $ijazahRecord ? $ijazahRecord->rata_rata_rapor : 0;

            $summary['rr'][$m->id] = $rr;
            if ($rr > 0) { $sums['rr'] += $rr; $counts['rr']++; }

            $um = $ijazahRecord ? round($ijazahRecord->nilai_ujian_madrasah) : 0;

            $summary['um'][$m->id] = $um;
            if ($um > 0) { $sums['um'] += $um; $counts['um']++; }

            if ($rr > 0 || $um > 0) {
                $na = ($rr * ($bRapor/100)) + ($um * ($bUjian/100));
                $valNa = round($na, 2);
                $summary['na'][$m->id] = $valNa;
                if ($valNa > 0) { $sums['na'] += $valNa; $counts['na']++; }
            } else {
                $summary['na'][$m->id] = 0;
            }
        }

        $summary['averages'] = [
             'rr' => $counts['rr'] > 0 ? $sums['rr'] / $counts['rr'] : 0,
             'um' => $counts['um'] > 0 ? $sums['um'] / $counts['um'] : 0,
             'na' => $counts['na'] > 0 ? $sums['na'] / $counts['na'] : 0,
        ];

        return $summary;
    }
}
