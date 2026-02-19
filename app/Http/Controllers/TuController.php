<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Periode;
use App\Models\NilaiSiswa;
use App\Models\Mapel;

class TuController extends Controller
{
    protected $financialService;

    public function __construct(\App\Services\FinancialService $financialService, \App\Services\DknReportService $dknService)
    {
        $this->financialService = $financialService;
        $this->dknService = $dknService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // --- BENDAHARA DASHBOARD (Premium) ---
        if ($user->isBendahara() || $user->isStafKeuangan() || $user->isAdminUtama()) {
            // Determine active period or just this month
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');

            // Get Key Metrics
            $summary = $this->financialService->getSummary($startDate, $endDate);
            $chartData = $this->financialService->getChartData($startDate, $endDate);

            // Total Tunggakan (All time)
            $totalTunggakan = $this->financialService->getTotalTunggakan();

            return view('tu.dashboard_bendahara', compact('summary', 'chartData', 'totalTunggakan', 'startDate', 'endDate'));
        }

        // --- ACADEMIC DASHBOARD (TU/Admin) ---
        $activeYear = TahunAjaran::where('status', 'aktif')->firstOrFail();

        // Stats
        $totalSiswa = Siswa::where('status_siswa', 'aktif')->count();
        $totalKelas = Kelas::where('id_tahun_ajaran', $activeYear->id)->count();

        // --- Cohort Analysis (Grafik Angkatan) ---
        // 1. Get List of Available "Angkatan" (Base it on Tahun Ajaran list)
        $availableAngkatan = TahunAjaran::orderBy('id', 'desc')->get();

        // 2. Determine Selected Angkatan (Default to 3 years ago or active year)
        // If no selection, pick reasonable default (e.g. 2023/2024 or latest logic)
        $selectedAngkatanId = $request->get('angkatan_id', $activeYear->id);
        $selectedAngkatan = $availableAngkatan->find($selectedAngkatanId);

        // 3. Logic: Find students who were in Tingkat 1 (MI) or Tingkat 7 (MTs) in that Year
        // If we can't find exact entry data, we check for enrollment in that year with lowest level.
        $startLevel = 1; // Default MI
        // You could add a filter for Jenjang later. For now assume MI/Integrated.

        // Get Class IDs for that Year + Level 1
        $cohortClassIds = Kelas::where('id_tahun_ajaran', $selectedAngkatanId)
            ->where('tingkat_kelas', $startLevel) // Grab Level 1 classes
            ->pluck('id');

        // Get Students in those classes (The Cohort Members)
        $cohortStudentIds = \App\Models\AnggotaKelas::whereIn('id_kelas', $cohortClassIds)
            ->pluck('id_siswa');

        // 4. Trace their grades over years
        // We need all years starting from Selected Angkatan
        $futureYears = TahunAjaran::where('id', '>=', $selectedAngkatanId)->orderBy('id', 'asc')->get();

        $chartLabels = [];
        $chartData = [];
        $cohortDescription = "Data Angkatan Masuk " . ($selectedAngkatan->nama ?? '-');

        if ($cohortStudentIds->isEmpty()) {
            // Fallback: If no cohort found (maybe user selected a future year or empty year), show zeros or try simpler query
            // Attempt: Just show school average for those years if cohort is empty to avoid broken chart
             foreach($futureYears as $index => $yr) {
                $chartLabels[] = $yr->nama_tahun . " (Thn ke-" . ($index + 1) . ")";
                $chartData[] = 0;
             }
             $cohortDescription .= " (Data Siswa Tidak Ditemukan)";
        } else {
             $currentLevel = $startLevel;
             foreach($futureYears as $yr) {
                $lbl = "Kelas " . $currentLevel . " (" . $yr->nama_tahun . ")";

                $avg = NilaiSiswa::whereIn('id_siswa', $cohortStudentIds)
                    ->whereHas('kelas', fn($q) => $q->where('id_tahun_ajaran', $yr->id))
                    ->avg('nilai_akhir');

                $chartLabels[] = $lbl;
                $val = $avg ? round($avg, 2) : 0;
                $chartData[] = $val;

                $currentLevel++;
             }
             $cohortDescription .= " (" . $cohortStudentIds->count() . " Siswa)";
        }

        // --- EXTRAS: Best Student, Best Class, Trend ---
        // (Existing Top Student & Best Class logic...)
        $topStudent = null;
        if ($cohortStudentIds->isNotEmpty()) {
            $bestSiswaRaw = NilaiSiswa::whereIn('id_siswa', $cohortStudentIds)
                ->selectRaw('id_siswa, AVG(nilai_akhir) as average_score')
                ->groupBy('id_siswa')
                ->orderByDesc('average_score')
                ->first();

            if ($bestSiswaRaw) {
                $s = Siswa::find($bestSiswaRaw->id_siswa);
                // Safe access to relation
                $anggotaKelas = $s->kelas_saat_ini;
                $className = ($anggotaKelas && $anggotaKelas->kelas) ? $anggotaKelas->kelas->nama_kelas : '-';

                $topStudent = [
                    'name' => $s->nama_lengkap,
                    'score' => round($bestSiswaRaw->average_score, 2),
                    'class' => $className
                ];
            }
        }

        $bestClass = null;
        if ($cohortStudentIds->isNotEmpty()) {
            $lastYearWithData = TahunAjaran::where('id', '>=', $selectedAngkatanId)->orderBy('id', 'desc')->first();
            $bestClassRaw = NilaiSiswa::whereHas('kelas', function($q) use ($lastYearWithData) {
                    $q->where('id_tahun_ajaran', $lastYearWithData->id);
                })
                ->whereIn('id_siswa', $cohortStudentIds)
                ->join('kelas', 'nilai_siswa.id_kelas', '=', 'kelas.id')
                ->selectRaw('kelas.nama_kelas, AVG(nilai_siswa.nilai_akhir) as average_score')
                ->groupBy('kelas.nama_kelas')
                ->orderByDesc('average_score')
                ->first();

            if ($bestClassRaw) {
                $bestClass = [
                    'name' => $bestClassRaw->nama_kelas,
                    'score' => round($bestClassRaw->average_score, 2)
                ];
            }
        }

        $trend = 'stable';
        if (count($chartData) >= 2) {
            $last = end($chartData);
            $prev = prev($chartData);
            if ($last > $prev) $trend = 'up';
            elseif ($last < $prev) $trend = 'down';
        }

        // --- NEW: Subject Analysis ---
        $hardestSubjects = [];
        $easiestSubjects = [];
        $gradeDistribution = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];

        if ($cohortStudentIds->isNotEmpty()) {
            // Hardest (Lowest Avg)
            $hardestSubjects = NilaiSiswa::whereIn('id_siswa', $cohortStudentIds)
                ->join('mapel', 'nilai_siswa.id_mapel', '=', 'mapel.id')
                ->select('mapel.nama_mapel', \DB::raw('AVG(nilai_siswa.nilai_akhir) as avg_score'))
                ->groupBy('mapel.nama_mapel')
                ->orderBy('avg_score', 'asc')
                ->take(5)
                ->get(); // Collection

            // Easiest (Highest Avg)
            $easiestSubjects = NilaiSiswa::whereIn('id_siswa', $cohortStudentIds)
                ->join('mapel', 'nilai_siswa.id_mapel', '=', 'mapel.id')
                ->select('mapel.nama_mapel', \DB::raw('AVG(nilai_siswa.nilai_akhir) as avg_score'))
                ->groupBy('mapel.nama_mapel')
                ->orderBy('avg_score', 'desc')
                ->take(5)
                ->get();

            // Distribution based on KKM (simplified for now: >90 A, >80 B, >70 C, else D)
            $allGrades = NilaiSiswa::whereIn('id_siswa', $cohortStudentIds)->pluck('nilai_akhir');
            foreach($allGrades as $g) {
                if ($g >= 90) $gradeDistribution['A']++;
                elseif ($g >= 80) $gradeDistribution['B']++;
                elseif ($g >= 70) $gradeDistribution['C']++;
                else $gradeDistribution['D']++;
            }
        }

        // --- NEW: HALL OF FAME (All-Time) ---
        // Ranking siswa terbaik dari SELURUH ANGKATAN (Global)
        // Indicator: Rata-Rata Nilai Akhir Tertinggi
        $hallOfFame = NilaiSiswa::select('id_siswa', \DB::raw('AVG(nilai_akhir) as global_avg'), \DB::raw('COUNT(id) as record_count'))
            ->groupBy('id_siswa')
            ->having('record_count', '>=', 5) // Minimal punya 5 nilai (supaya adil, siswa baru 1 nilai 100 tidak langsung juara 1)
            ->orderByDesc('global_avg')
            ->take(5)
            ->with('siswa')
            ->get()
            ->map(function($item) {
                $item->global_avg = round($item->global_avg, 2);
                // Get current class
                $cls = $item->siswa->kelas_saat_ini;
                $item->class_name = ($cls && $cls->kelas) ? $cls->kelas->nama_kelas : 'Alumni/Lulus';

                // Get Year/Angkatan based on entry or current class
                // Approximation
                return $item;
            });

        // --- NEW: BINTANG PELAJAR (Active Students Only) ---
        // Ranking khusus siswa yang masih status 'aktif'
        // Bisa digunakan untuk pemilihan Siswa Tauladan / Bintang Pelajar
        $activeStars = Siswa::where('status_siswa', 'aktif')
            ->join('nilai_siswa', 'siswa.id', '=', 'nilai_siswa.id_siswa')
            ->select('siswa.id', 'siswa.nama_lengkap', \DB::raw('AVG(nilai_siswa.nilai_akhir) as avg_score'), \DB::raw('COUNT(nilai_siswa.id) as record_count'))
            ->groupBy('siswa.id', 'siswa.nama_lengkap')
            ->having('record_count', '>=', 5)
            ->orderByDesc('avg_score')
            ->take(5) // Top 5 Candidates
            ->with(['kelas_saat_ini.kelas']) // Eager load class on Siswa model directly
            ->get()
            ->map(function($item) {
                // $item is now a Siswa instance
                $item->avg_score = round($item->avg_score, 2);

                $cls = $item->kelas_saat_ini;
                $item->class_name = ($cls && $cls->kelas) ? $cls->kelas->nama_kelas : '-';
                return $item;
            });

        return view('tu.dashboard', compact(
            'totalSiswa', 'totalKelas', 'activeYear',
            'chartLabels', 'chartData',
            'availableAngkatan', 'selectedAngkatanId', 'cohortDescription',
            'topStudent', 'bestClass', 'trend',
            'hardestSubjects', 'easiestSubjects', 'gradeDistribution',
            'hallOfFame', 'activeStars'
        ));
    }

    public function dkn()
    {
        // Default to Class 6 MI (First available)
        // Ideally user selects class. For now, fetch first final year class.
        $activeYear = TahunAjaran::where('status', 'aktif')->first();

        // Cari kelas akhir (Kelas 6 MI or Kelas 9 MTs)
        // Cari kelas akhir (Kelas 6 MI, Kelas 9/3 MTs, Kelas 12/3 MA)
        $finalClasses = Kelas::where('id_tahun_ajaran', $activeYear->id)
            ->where(function($q) {
                $q->where('nama_kelas', 'LIKE', '%6%')   // MI Class 6
                  ->orWhere('nama_kelas', 'LIKE', '%9%') // MTs Class 9
                  ->orWhere('nama_kelas', 'LIKE', '%3%')  // MTs Class 3 (Relative)
                  ->orWhere('nama_kelas', 'LIKE', '%IX%')  // Roman 9
                  ->orWhere('nama_kelas', 'LIKE', '%VI%')  // Roman 6
                  ->orWhere('nama_kelas', 'LIKE', '%III%'); // Roman 3
            })->get();

        return view('tu.dkn-selector', compact('finalClasses'));
    }

    protected $dknService;

    // Constructor merged at top
    // public function __construct(\App\Services\DknReportService $dknService)
    // {
    //    $this->dknService = $dknService;
    // }

    public function showDknArchive(Kelas $kelas)
    {
        // Use Service to get Data
        $data = $this->dknService->getDknData($kelas);

        $mapels = $data['mapels'];
        $dknData = $data['dknData'];
        $targetLevels = $data['targetLevels'];
        $periods = $data['periods'];
        $periodLabel = $data['periodLabel'];

        // Fetch Promotion Decisions for Veto Logic
        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        $promotionDecisions = \Illuminate\Support\Facades\DB::table('promotion_decisions')
            ->where('id_kelas', $kelas->id)
            ->where('id_tahun_ajaran', $activeYear->id)
            ->select('id_siswa', 'final_decision', 'notes') // Fetch notes too
            ->get()
            ->keyBy('id_siswa');

        $school = \App\Models\IdentitasSekolah::first();
        return view('tu.dkn-archive', compact('kelas', 'mapels', 'dknData', 'school', 'promotionDecisions', 'targetLevels', 'periods', 'periodLabel'));
    }

    public function showDknSimple(Kelas $kelas)
    {
        // Use Service (Standardized Logic)
        $data = $this->dknService->getDknData($kelas);

        $dknData = $data['dknData'];
        $mapels = $data['mapels'];

        /*
         * Manual fetching removed in favor of Service
         * The service returns 'dknData' where each item has ['student', 'summary', 'data']
         * This matches the structure expected by the stats calculation below.
         */

        // --- Calculate Summary Stats (Mirroring IjazahController) ---
        $highestScore = 0; $highestStudent = '-';
        $lowestScore = 100; $lowestStudent = '-';
        $totalClassScore = 0;
        $studentCountWithGrades = 0;
        $passCount = 0;
        $failCount = 0;

        $minLulus = \App\Models\GlobalSetting::val('ijazah_min_lulus', 60);

        foreach ($dknData as $row) {
            $s = $row['student'];
            // Calculate Average NA from Summary
            $sumNA = 0;
            $countMapel = 0;

            // $row['summary']['na'] contains [mapel_id => score]
            // We need to re-average it for the student stats
            foreach ($row['summary']['na'] as $score) {
                if ($score > 0) {
                    $sumNA += $score;
                    $countMapel++;
                }
            }

            $avgNA = $countMapel > 0 ? $sumNA / $countMapel : 0;

            if ($countMapel > 0) {
                if ($avgNA > $highestScore) { $highestScore = $avgNA; $highestStudent = $s->nama_lengkap; }
                if ($avgNA < $lowestScore) { $lowestScore = $avgNA; $lowestStudent = $s->nama_lengkap; }

                $totalClassScore += $avgNA;
                $studentCountWithGrades++;
            }

            // Check Pass/Fail
            if ($avgNA >= $minLulus && $countMapel > 0) {
                $passCount++;
            } else {
                $failCount++;
            }
        }

        if ($lowestScore == 100 && $studentCountWithGrades == 0) $lowestScore = 0;
        $classAverage = $studentCountWithGrades > 0 ? $totalClassScore / $studentCountWithGrades : 0;

        $stats = [
            'highest' => ['score' => round($highestScore, 2), 'student' => $highestStudent],
            'lowest' => ['score' => round($lowestScore, 2), 'student' => $lowestStudent],
            'average' => round($classAverage, 2),
            'pass' => $passCount,
            'fail' => $failCount,
            'total' => count($dknData)
        ];

        return view('tu.dkn-simple', compact('kelas', 'mapels', 'dknData', 'stats'));
    }



    public function storeNilaiIjazah(Request $request, Kelas $kelas)
    {
        // Format: grades[siswa_id][mapel_id][rr|um]
        $data = $request->grades;

        if ($data) {
            foreach ($data as $siswaId => $mapels) {
                foreach ($mapels as $mapelId => $scores) {

                    $rr = isset($scores['rr']) && $scores['rr'] !== '' ? $scores['rr'] : null;
                    $um = isset($scores['um']) && $scores['um'] !== '' ? $scores['um'] : null;

                    if ($rr !== null || $um !== null) {

                        $na = null;
                        if ($rr !== null && $um !== null) {
                            $na = ($rr * 0.6) + ($um * 0.4);
                        }

                        \App\Models\NilaiIjazah::updateOrCreate(
                            ['id_siswa' => $siswaId, 'id_mapel' => $mapelId],
                            [
                                'rata_rata_rapor' => $rr,
                                'nilai_ujian_madrasah' => $um,
                                'nilai_ijazah' => $na
                            ]
                        );
                    }
                }
            }
        }

        return back()->with('success', 'Data Nilai Ijazah berhasil disimpan.');
    }

    public function globalMonitoring(Request $request)
    {
        // 1. Filter Context (Year & Period)
        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->firstOrFail();

        $selectedYearId = $request->year_id ?? $activeYear->id;
        $activeYear = \App\Models\TahunAjaran::find($selectedYearId);

        $years = \App\Models\TahunAjaran::orderBy('id', 'desc')->get();

        // Fetch ALL periods for this year
        $periods = Periode::where('id_tahun_ajaran', $selectedYearId)->get();

        // Filter Logic
        // Default to 'all' so User sees EVERYTHING immediately (Requested by Boss)
        $selectedPeriodId = $request->get('period_id', 'all');
        $currentPeriod = null;

        if ($selectedPeriodId === 'all') {
            $currentPeriod = null; // Mode: All Periods
        } elseif ($selectedPeriodId) {
            $currentPeriod = $periods->find($selectedPeriodId);
        }

        // 2. Fetch Classes (Filtered by Jenjang & Selection)
        $classesQuery = Kelas::where('id_tahun_ajaran', $selectedYearId)
            ->with(['wali_kelas', 'jenjang'])
            ->withCount('anggota_kelas');

        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user->isAdmin() && !$user->isTu()) {
            // Wali Kelas Limitation: Only show their classes
            $classesQuery->where('id_wali_kelas', $user->id);
        }

        if ($request->jenjang) {
            $classesQuery->whereHas('jenjang', fn($q) => $q->where('kode', $request->jenjang));
        }

        // If a specific class is selected
        if ($request->kelas_id) {
            $classesQuery->where('id', $request->kelas_id);
        }

        $classes = $classesQuery->orderBy('id_jenjang')
            ->orderBy('tingkat_kelas')
            ->orderBy('nama_kelas')
            ->get();

        // For Dropdown List (Unfiltered by class selection, but filtered by Jenjang AND PERMISSION)
        $allClassesQuery = Kelas::where('id_tahun_ajaran', $selectedYearId);
        if (!$user->isAdmin() && !$user->isTu()) {
             $allClassesQuery->where('id_wali_kelas', $user->id);
        }
        if ($request->jenjang) {
             $allClassesQuery->whereHas('jenjang', fn($q) => $q->where('kode', $request->jenjang));
        }
        $allClasses = $allClassesQuery->orderBy('nama_kelas')->get();

        // Periods (Filtered by Jenjang)
        $periodsQuery = Periode::where('id_tahun_ajaran', $selectedYearId);
        if ($request->jenjang) {
            $periodsQuery->where('lingkup_jenjang', $request->jenjang);
        }
        $periods = $periodsQuery->orderBy('nama_periode')->get(); // Replaced 'semester' with 'nama_periode' just in case

        // 3. Pre-calculate Period Counts per Jenjang (for 'All' mode)
        $periodCountsByJenjang = []; // ['MI' => 3, 'MTS' => 2]
        if ($selectedPeriodId === 'all') {
            // Map Jenjang Name (or ID if linked) to count
            // Assumes periode.lingkup_jenjang matches jenjang.nama/kode?
            // Or we iterate all periods.
            $miCount = $periods->where('lingkup_jenjang', 'MI')->count();
            $mtsCount = $periods->where('lingkup_jenjang', 'MTS')->count();
            // Fallback if null
            if ($miCount == 0) $miCount = $periods->where('tipe', 'CAWU')->count();
            if ($mtsCount == 0) $mtsCount = $periods->where('tipe', 'SEMESTER')->count();

            $periodCountsByJenjang['MI'] = $miCount > 0 ? $miCount : 1;
            $periodCountsByJenjang['MTS'] = $mtsCount > 0 ? $mtsCount : 1;
        }

        $monitoringData = [];

        if ($classes->count() > 0) {
            // Assignments (Mapel Count per Class)
            // Note: assignments are per year typically.
            $assignments = \App\Models\PengajarMapel::whereIn('id_kelas', $classes->pluck('id'))
                ->selectRaw('id_kelas, count(*) as mapel_count')
                ->groupBy('id_kelas')
                ->pluck('mapel_count', 'id_kelas');

            // Actual Grades Count
            $gradesQuery = NilaiSiswa::whereIn('id_kelas', $classes->pluck('id'));

            if ($currentPeriod) {
                $gradesQuery->where('id_periode', $currentPeriod->id);
            } else {
                // All Periods: Filter by Year via Class/Period linking
                // NilaiSiswa has id_periode. We want periods in THIS year.
                $gradesQuery->whereIn('id_periode', $periods->pluck('id'));
            }

            $gradesCount = $gradesQuery->selectRaw('id_kelas, count(*) as total_grades')
                ->groupBy('id_kelas')
                ->pluck('total_grades', 'id_kelas');

            foreach ($classes as $cls) {
                $studentCount = $cls->anggota_kelas_count;
                $mapelCount = $assignments[$cls->id] ?? 0;

                // Determine Multiplier
                $periodMultiplier = 1;
                if ($selectedPeriodId === 'all') {
                    // Match Jenjang
                    $jName = strtoupper($cls->jenjang->nama ?? ''); // MI, MTS, MA
                    if (str_contains($jName, 'MI')) $periodMultiplier = $periodCountsByJenjang['MI'] ?? 1;
                    else if (str_contains($jName, 'MTS')) $periodMultiplier = $periodCountsByJenjang['MTS'] ?? 1;
                }

                $expected = $studentCount * $mapelCount * $periodMultiplier;
                $actual = $gradesCount[$cls->id] ?? 0;

                $progress = ($expected > 0) ? round(($actual / $expected) * 100) : 0;
                if ($progress > 100) $progress = 100;

                // Status
                $status = 'Belum Mulai';
                $color = 'slate';

                if ($progress == 100) {
                    $status = 'Selesai';
                    $color = 'emerald';
                } elseif ($progress > 50) {
                     $status = 'Proses ' . $progress . '%';
                     $color = 'blue';
                } elseif ($progress > 0) {
                     $status = 'Proses ' . $progress . '%';
                     $color = 'amber';
                }

                // Add Period Label Info for UI
                $periodLabel = '';
                if ($selectedPeriodId === 'all') {
                    $periodLabel = (str_contains(strtoupper($cls->jenjang->nama ?? ''), 'MI')) ? 'Semua Cawu' : 'Semua Semester';
                } else {
                    $periodLabel = $currentPeriod->nama_periode ?? '-';
                }

                $monitoringData[] = (object) [
                    'class' => $cls,
                    'progress' => $progress,
                    'status' => $status,
                    'color' => $color,
                    'mapel_count' => $mapelCount,
                    'student_count' => $studentCount,
                    'period_label' => $periodLabel // New data for UI
                ];
            }
        }

        return view('tu.monitoring_global', compact('years', 'periods', 'currentPeriod', 'activeYear', 'monitoringData', 'selectedPeriodId', 'allClasses'));
    }

    public function downloadDknExcel(Kelas $kelas)
    {
        $school = \App\Models\IdentitasSekolah::first();

        // Use Service
        $data = $this->dknService->getDknData($kelas);

        $mapels = $data['mapels'];
        $dknData = $data['dknData']; // Contains student, data(levels), summary
        $jenjang = $data['jenjang'];
        $targetLevels = $data['targetLevels'];
        $periods = $data['periods'];
        $periodLabel = $data['periodLabel'];

        // Match Controller logic for Period Label Map
        // Construct label map based on Period Label from Service
        // If 'Smt' -> Ganjil, Genap. If 'Cawu' -> Cawu 1, Cawu 2, etc.
        $periodLabelMap = [];
        foreach($periods as $p) {
            if ($periodLabel == 'Smt') {
                $periodLabelMap[] = ($p % 2 != 0) ? 'Ganjil' : 'Genap';
            } else {
                $periodLabelMap[] = $periodLabel . ' ' . $p;
            }
        }

        // Fetch Promotion Decisions for Veto Logic (Match showDknArchive)
        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        $promotionDecisions = \Illuminate\Support\Facades\DB::table('promotion_decisions')
            ->where('id_kelas', $kelas->id)
            ->where('id_tahun_ajaran', $activeYear->id)
            ->select('id_siswa', 'final_decision', 'notes')
            ->get()
            ->keyBy('id_siswa');

        // Spreadsheet Setup
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Info
        $sheet->setCellValue('A1', 'DAFTAR KUMPULAN NILAI (DKN) IJAZAH');
        $sheet->setCellValue('A2', strtoupper($school->nama_sekolah ?? 'SEKOLAH'));
        $sheet->setCellValue('A3', 'Kelas: ' . $kelas->nama_kelas . ' | Tahun: ' . ($kelas->active_year_name ?? date('Y')));

        // Table Header
        $row = 5;
        $sheet->setCellValue('A'.$row, 'NO');
        $sheet->setCellValue('B'.$row, 'NAMA SISWA');
        $sheet->setCellValue('C'.$row, 'KELAS / ' . strtoupper($periodLabel));

        $col = 4; // D
        foreach($mapels as $mapel) {
            $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colStr.$row, $mapel->nama_mapel);
            $col++;
        }
        $colStrAvg = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $sheet->setCellValue($colStrAvg.$row, 'RATA-RATA');
        $col++;
        $colStrKet = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $sheet->setCellValue($colStrKet.$row, 'KETERANGAN');

        // Header Style
        $lastCol = $colStrKet;
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);

        $headerRange = "A$row:$lastCol$row";
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Merge Title
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->getStyle("A1:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}3")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Autofit
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $colLimit = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastCol);
        for ($i = 4; $i <= $colLimit; $i++) {
            $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colStr)->setAutoSize(true);
        }

        $row++; // Start Data
        $no = 1;
        $jkl = strtolower($jenjang);
        $minLulus = \App\Models\GlobalSetting::val('ijazah_min_lulus_' . $jkl, 60);

        \Illuminate\Support\Facades\Log::info('DKN Export Debug:', [
            'class_id' => $kelas->id,
            'jenjang' => $jenjang,
            'min_lulus' => $minLulus,
            'target_levels' => $targetLevels,
            'active_year' => $activeYear->id ?? 'null',
            'promo_decisions_count' => $promotionDecisions->count(),
            'first_promo' => $promotionDecisions->first()
        ]);

        foreach($dknData as $studentRow) {
            $startRow = $row;
            $s = $studentRow['student'];
            // Access pre-calculated data
            $levelsData = $studentRow['data'];
            $summary = $studentRow['summary'];

            // Prepare Student Data

            foreach ($targetLevels as $lvl) {
                foreach($periods as $pIndex => $pCode) {
                    // Determine display level
                    if ($jenjang === 'MTS') $displayLvl = $lvl - 6;
                    else $displayLvl = $lvl;

                    $lvlSuffix = ($jenjang === 'MTS') ? (' ' . $jenjang) : '';
                    if ($displayLvl < 1) $displayLvl = $lvl;

                    // Row Label: "1 MTs | Smt 1" or "4 MI | Cawu 1"
                    // Use the map we built earlier
                    // Note: periods from Service might be [0 => 1, 1 => 2] or [0 => 'Semester 1'...]
                    // We need to ensure we use the correct value for Data Access ($pCode) and Label ($pLabel)

                    // Service returns periods as array of values (e.g. [1, 2, 3, 4, 5, 6]).
                    // So $pCode is the value (1, 2...). $pIndex is the key (0, 1...).

                    $pLabel = $periodLabelMap[$pIndex] ?? ($periodLabel . ' ' . $pCode);

                    $sheet->setCellValue('C'.$row, "$displayLvl$lvlSuffix | $pLabel");

                    // Mapel Scores
                    $currCol = 4;
                    $rowSum = 0; $rowCount = 0;

                    foreach ($mapels as $m) {
                        // Get Value from Service Data
                        $score = $levelsData[$lvl][$pCode][$m->id] ?? null;

                        // Fallback logic for MTS relative levels if needed
                        if ($score === null && ($jenjang === 'MTS')) {
                             $relativeLvl = $lvl - 6;
                             $score = $levelsData[$relativeLvl][$pCode][$m->id] ?? null;
                        }

                        $cStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currCol);
                        $valStr = $score !== null ? round($score) : '-';
                        $sheet->setCellValue($cStr.$row, $valStr);
                        if ($valStr === '-') {
                                $sheet->getStyle($cStr.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
                        }

                        if($score !== null) { $rowSum += $score; $rowCount++; }
                        $currCol++;
                    }

                    // Row Average
                    $rowAvg = $rowCount > 0 ? $rowSum / $rowCount : 0;
                    $avgVal = $rowCount > 0 ? $rowAvg : '-';
                    $sheet->setCellValue($colStrAvg.$row, $avgVal);
                    if ($avgVal === '-') {
                        $sheet->getStyle($colStrAvg.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
                    }
                    if($rowCount > 0) {
                        $sheet->getStyle($colStrAvg.$row)->getNumberFormat()->setFormatCode('0.00');
                    }

                    $row++;
                }
            }

            // RR Row
            $sheet->setCellValue('C'.$row, 'Rata-Rapor (RR)');
            $sheet->getStyle('C'.$row)->getFont()->setBold(true);
            $sheet->getStyle("A$row:$colStrKet$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF0E0');

            $currCol = 4;
            foreach ($mapels as $m) {
                $cStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currCol);
                $val = $summary['rr'][$m->id] ?? 0;
                $valStr = $val != 0 ? $val : '-';
                $sheet->setCellValue($cStr.$row, $valStr);
                if ($valStr === '-') $sheet->getStyle($cStr.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
                if($val != 0) $sheet->getStyle($cStr.$row)->getNumberFormat()->setFormatCode('0.00');
                $currCol++;
            }

            // Layout: Merge RR & UM Last Column for Label "RATA-RATA TOTAL"
            $sheet->setCellValue($colStrAvg.$row, "RATA-RATA\nTOTAL");
            $sheet->mergeCells("$colStrAvg$row:$colStrAvg".($row+1));
            $sheet->getStyle($colStrAvg.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle($colStrAvg.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($colStrAvg.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle($colStrAvg.$row)->getFont()->setBold(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
            $sheet->getStyle($colStrAvg.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF666666'); // Dark Grey

            $row++;

            // UM Row
            $sheet->setCellValue('C'.$row, 'Ujian Mdr (UM)');
            $sheet->getStyle('C'.$row)->getFont()->setBold(true);
            $sheet->getStyle("A$row:$colStrKet$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFF6FF');

            $currCol = 4;
            foreach ($mapels as $m) {
                $cStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currCol);
                $val = $summary['um'][$m->id] ?? 0; // Already rounded
                $valStr = $val != 0 ? $val : '-';
                $sheet->setCellValue($cStr.$row, $valStr);
                if ($valStr === '-') $sheet->getStyle($cStr.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
                if($val != 0) $sheet->getStyle($cStr.$row)->getNumberFormat()->setFormatCode('0');
                $currCol++;
            }
            // Last Column skipped (Merged with above)
            $row++;

            // NA Row
            $sheet->setCellValue('C'.$row, 'Nilai Akhir (NA)');
            $sheet->getStyle('C'.$row)->getFont()->setBold(true);
            $sheet->getStyle("A$row:$colStrKet$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFDCFCE7');
            $sheet->getStyle("A$row:$colStrKet$row")->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);

            $currCol = 4;
            foreach ($mapels as $m) {
                $cStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currCol);
                $val = $summary['na'][$m->id] ?? 0;
                $valStr = $val != 0 ? $val : '-';
                $sheet->setCellValue($cStr.$row, $valStr);
                if ($valStr === '-') $sheet->getStyle($cStr.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
                if($val != 0) $sheet->getStyle($cStr.$row)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle($cStr.$row)->getFont()->setBold(true);
                $currCol++;
            }

            // Write NA Average (Calculated to match Screen View exactly)
            $naValues = array_filter($summary['na']); // Remove 0/null
            $naAvg = count($naValues) > 0 ? array_sum($naValues) / count($naValues) : 0;

            // Check Academic Pass
            $academicPass = $naAvg >= $minLulus;

            $sheet->setCellValue($colStrAvg.$row, $naAvg > 0 ? $naAvg : '-');
            if($naAvg > 0) $sheet->getStyle($colStrAvg.$row)->getNumberFormat()->setFormatCode('0.00');

            // Style for Final Score
            $sheet->getStyle($colStrAvg.$row)->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
            $sheet->getStyle($colStrAvg.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF333333'); // Darker Grey
            $sheet->getStyle($colStrAvg.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($colStrAvg.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // Veto Logic (Match Screen View)
            $promoRecord = $promotionDecisions[$s->id] ?? null;
            $promoDecision = $promoRecord->final_decision ?? null;
            $promoNote = $promoRecord->notes ?? '';

            // "Lulus" only if Academic Pass AND Not Vetoed (Retained/Not Graduated)
            // Note: If promoDecision is present, it OVERRIDES academic pass?
            // Screen logic: if (isVetoed) Fail; elseif (academicPass) Pass; else Fail.

            $isVetoed = in_array($promoDecision, ['retained', 'not_graduated']);

            if ($no <= 2) { // Log first 2 students
                 \Illuminate\Support\Facades\Log::info("Student {$s->nama_lengkap} ($no):", [
                     'naAvg' => $naAvg,
                     'academicPass' => $academicPass,
                     'promoDecision' => $promoDecision,
                     'isVetoed' => $isVetoed,
                     'status' => ($isVetoed ? 'Tidak Lulus' : ($academicPass ? 'Lulus' : 'Tidak Lulus'))
                 ]);
            }

            if ($isVetoed) {
                $status = 'Tidak Lulus';
            } elseif ($academicPass) {
                $status = 'Lulus';
            } else {
                $status = 'Tidak Lulus';
            }

            // Merge Columns for Student Info
            $endRow = $row;

            $sheet->setCellValue('A'.$startRow, $no++);
            $sheet->mergeCells("A$startRow:A$endRow");
            $sheet->getStyle("A$startRow")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

            $nisVal = $s->nis_lokal ?? $s->nis ?? $s->nisn ?? '-';
            $sheet->setCellValue('B'.$startRow, $s->nama_lengkap . "\nNIS: " . $nisVal);
            $sheet->getStyle('B'.$startRow)->getAlignment()->setWrapText(true);
            $sheet->mergeCells("B$startRow:B$endRow");
            $sheet->getStyle("B$startRow")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

            // Status Column Merge
            $statusText = $status;
            if ($status === 'Tidak Lulus' && !empty($promoNote)) {
                $statusText .= "\n(" . $promoNote . ")";
            }

            $sheet->setCellValue($colStrKet.$startRow, $statusText);
            $sheet->mergeCells("$colStrKet$startRow:$colStrKet$endRow");
            $sheet->getStyle("$colStrKet$startRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("$colStrKet$startRow")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle("$colStrKet$startRow")->getAlignment()->setWrapText(true);
            $sheet->getStyle("$colStrKet$startRow")->getFont()->setBold(true);
            $sheet->getStyle("$colStrKet$startRow")->getFont()->setColor(
                $status === 'Lulus'
                ? new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN)
                : new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED)
            );

            $row++;
        }

        // Borders All
        $lastRow = $row - 1;
        $sheet->getStyle("A5:$lastCol$lastRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Alignment
        $sheet->getStyle("A5:$lastCol$lastRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A5:$lastCol$lastRow")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // --- LEGEND & SIGNATURE ---
        // Fetch Weight Config from Settings
        $bRapor = \App\Models\GlobalSetting::val('ijazah_bobot_rapor_' . $jkl, 60);
        $bUjian = \App\Models\GlobalSetting::val('ijazah_bobot_ujian_' . $jkl, 40);

        $row += 2;
        $sheet->setCellValue('A'.$row, 'Keterangan:');
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A'.$row, '1. Rata-Rapor (RR) diambil dari Rata-rata Nilai Rapor semester/kelas yang ditentukan.');
        $row++;
        // Use Dynamic Weights in Legend
        $sheet->setCellValue('A'.$row, "2. Rumus Nilai Akhir: NA = (Rapor × $bRapor%) + (Ujian × $bUjian%).");
        $row++;
        $sheet->setCellValue('A'.$row, '3. Kriteria Kelulusan: Rata-rata Nilai Akhir minimal ' . number_format($minLulus,2));

        // Signature Block
        $lastColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastCol);
        $sigStartColIndex = max(1, $lastColIndex - 6);
        $sigCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sigStartColIndex);

        $row += 2;
        $dDate = date('d F Y');
        $city = $school->kabupaten ?? 'Kabupaten';

        $sheet->setCellValue($sigCol.$row, "$city, $dDate");
        $sheet->mergeCells("$sigCol$row:$lastCol$row");
        $sheet->getStyle("$sigCol$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $hmTitle = 'Kepala Madrasah'; // Generic or specific
        if ($jenjang === 'MI') $hmTitle = 'Kepala Madrasah Ibtidaiyah';
        if ($jenjang === 'MTS') $hmTitle = 'Kepala Madrasah Tsanawiyah';

        $sheet->setCellValue($sigCol.$row, $hmTitle . ',');
        $sheet->mergeCells("$sigCol$row:$lastCol$row");
        $sheet->getStyle("$sigCol$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row += 4;

        // Dynamic Headmaster Config (Simplified with Jenjang Settings)
        $hmName = \App\Models\GlobalSetting::val('hm_name_' . $jkl) ?: ($school->kepala_madrasah ?? '......................');
        $hmNip = \App\Models\GlobalSetting::val('hm_nip_' . $jkl) ?: ($school->nip_kepala ?? '-');

        $sheet->setCellValue($sigCol.$row, $hmName);
        $sheet->mergeCells("$sigCol$row:$lastCol$row");
        $sheet->getStyle("$sigCol$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("$sigCol$row")->getFont()->setBold(true)->setUnderline(true);

        $row++;
        $sheet->setCellValue($sigCol.$row, "NIP. $hmNip");
        $sheet->mergeCells("$sigCol$row:$lastCol$row");
        $sheet->getStyle("$sigCol$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Print Setup
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setBottom(0.5);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 5);

        // Output
        $fileName = 'DKN_Archive_' . preg_replace('/[^A-Za-z0-9]/', '_', $kelas->nama_kelas) . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName) .'"');
        $writer->save('php://output');
        exit;
    }

}
