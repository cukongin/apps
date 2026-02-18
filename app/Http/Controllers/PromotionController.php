<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\AnggotaKelas;
use App\Models\NilaiSiswa;
use App\Models\TahunAjaran;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Initial Checks
        if (!$this->checkActiveYear()) {
             return redirect()->back()->with('error', '⚠️ AKSES DITOLAK: Periode terkunci atau bukan tahun aktif.');
        }

        $activeYear = TahunAjaran::where('status', 'aktif')->firstOrFail();

        // 2. Class Selection
        $userId = Auth::id();
        $user = Auth::user();

        // Admin Access
        if ($user->role === 'admin' || $userId == 1 || $user->isStaffTu()) {
            $allClasses = Kelas::where('id_tahun_ajaran', $activeYear->id)
                ->whereHas('jenjang', function($q) {
                    $q->where('has_rapor', true);
                })
                ->orderBy('nama_kelas')
                ->get();
        } else {
            // Wali Kelas Access
            $allClasses = Kelas::where('id_tahun_ajaran', $activeYear->id)
                ->where('id_wali_kelas', $userId) // Note: Changed id_wali to id_wali_kelas to match schema
                ->whereHas('jenjang', function($q) {
                    $q->where('has_rapor', true);
                })
                ->orderBy('nama_kelas')
                ->get();
        }

        $selectedClass = $allClasses->first();
        if ($request->has('class_id')) {
            $selectedClass = $allClasses->where('id', $request->class_id)->first() ?? $selectedClass;
        }

        // Period Selection
        $allPeriods = Periode::where('id_tahun_ajaran', $activeYear->id)->get();
        $selectedPeriod = $allPeriods->where('status', 'aktif')->first();
        if ($request->has('period_id')) {
            $selectedPeriod = $allPeriods->where('id', $request->period_id)->first() ?? $selectedPeriod;
        }

        $students = collect([]);
        $metrics = ['total' => 0, 'promoted' => 0, 'retained' => 0];
        $isLocked = false;
        $warningMessage = null;
        $isFinalYear = false;
        $debugLog = [];
        $pageContext = [
            'type' => 'promotion',
            'title' => 'Kenaikan Kelas',
            'success_label' => 'Naik Kelas',
            'fail_label' => 'Tinggal Kelas',
            'success_badge' => 'Naik Kelas',
            'fail_badge' => 'Tinggal Kelas'
        ];
        $isMi = false;
        $isMts = false;
        $gradeLevel = 0;

        // 3. Logic Engine
        if ($selectedClass) {
            // Check Lock (Manual Override Logic)
            $isLocked = DB::table('promotion_decisions')
                ->where('id_kelas', $selectedClass->id)
                ->where('id_tahun_ajaran', $activeYear->id)
                ->whereNotNull('override_by')
                ->exists();

            // Run Calculation
            $this->calculate($selectedClass->id, $selectedPeriod ? $selectedPeriod->id : null);

            // Fetch Results
            $students = DB::table('promotion_decisions')
                ->join('siswa', 'promotion_decisions.id_siswa', '=', 'siswa.id')
                ->where('promotion_decisions.id_kelas', $selectedClass->id)
                ->where('promotion_decisions.id_tahun_ajaran', $activeYear->id)
                ->select('siswa.nama_lengkap as nama_siswa', 'siswa.nis_lokal as nis', 'promotion_decisions.*')
                ->orderBy('siswa.nama_lengkap')
                ->get();

            $metrics['total'] = $students->count();
            $metrics['promoted'] = $students->whereIn('system_recommendation', ['promoted', 'graduated', 'conditional'])->count();
            $metrics['retained'] = $students->whereIn('system_recommendation', ['retained', 'not_graduated'])->count();


            // --- REVAMPED FINAL YEAR LOGIC WITH LOGGING ---
            $debugLog = [];

            // 1. Force Load Jenjang
            if (!$selectedClass->relationLoaded('jenjang')) {
                $selectedClass->load('jenjang');
            }

            $jenjangCode = optional($selectedClass->jenjang)->kode;
            $debugLog[] = "Raw Jenjang: " . ($jenjangCode ?? 'NULL');

            if (!$jenjangCode) {
                // Fallback: Guess from name
                if (stripos($selectedClass->nama_kelas, 'MTs') !== false) {
                    $jenjangCode = 'MTS';
                    $debugLog[] = "Fallback Jenjang: MTS (from name)";
                } elseif (stripos($selectedClass->nama_kelas, 'MI') !== false) {
                    $jenjangCode = 'MI';
                    $debugLog[] = "Fallback Jenjang: MI (from name)";
                } else {
                    $debugLog[] = "Fallback Jenjang: Failed";
                }
            }
            $jenjangCode = strtoupper($jenjangCode);
            $debugLog[] = "Normalized Jenjang: $jenjangCode";

            $grade = (int) filter_var($selectedClass->nama_kelas, FILTER_SANITIZE_NUMBER_INT);
            $debugLog[] = "Grade Level: $grade";

            $finalGradeMI = (int) \App\Models\GlobalSetting::val('final_grade_mi', 6);
            $finalGradeMTS = (int) \App\Models\GlobalSetting::val('final_grade_mts', 9);
            $debugLog[] = "Config MI: $finalGradeMI, MTS: $finalGradeMTS";

            // Strict Logic
            if ($jenjangCode === 'MI') {
                if ($grade == $finalGradeMI) {
                    $isFinalYear = true;
                    $debugLog[] = "DECISION: FINAL YEAR (MI Match)";
                } else {
                    $debugLog[] = "DECISION: NOT FINAL (MI mismatch $grade != $finalGradeMI)";
                }
            } elseif ($jenjangCode === 'MTS') {
                 if ($grade == $finalGradeMTS) {
                     $isFinalYear = true; // Config match
                     $debugLog[] = "DECISION: FINAL YEAR (MTS Config Match)";
                 } elseif ($finalGradeMTS == 9 && $grade == 3) {
                     $isFinalYear = true; // Legacy support (Class 3 MTs)
                     $debugLog[] = "DECISION: FINAL YEAR (MTS Legacy 3==9 Match)";
                 } else {
                     $debugLog[] = "DECISION: NOT FINAL (MTS mismatch Grade $grade != $finalGradeMTS)";
                 }
            } else {
                $debugLog[] = "DECISION: NOT FINAL (Unknown Jenjang '$jenjangCode')";
            }

            $isMi = ($jenjangCode === 'MI');
            $isMts = ($jenjangCode === 'MTS');
            $gradeLevel = $grade;

            // --- FINAL PERIOD VALIDATION ---
            // Even if it's the final year grade, we must be in the final semester/cawu
            if ($isFinalYear) {
                 $activePeriodVal = \App\Models\Periode::where('status', 'aktif')->first();
                 if ($activePeriodVal) {
                     $pName = strtolower($activePeriodVal->nama_periode);
                     // If explicit Odd Semester -> Revoke Graduation Status
                     if (str_contains($pName, 'ganjil') || str_contains($pName, 'semester 1') || str_contains($pName, 'cawu 1') || str_contains($pName, 'cawu 2')) {
                         $isFinalYear = false;
                         $debugLog[] = "DECISION: FINAL YEAR REVOKED (Period '$pName' is not final)";
                     }
                 }
            }

            if ($isFinalYear) {
                $pageContext = [
                    'type' => 'graduation',
                    'title' => 'Kelulusan Akhir',
                    'success_label' => 'LULUS',
                    'fail_label' => 'TIDAK LULUS',
                    'success_badge' => 'LULUS',
                    'fail_badge' => 'TIDAK LULUS'
                ];
            }

            // --- FETCH PROMOTION CRITERIA FOR VIEW ---
            $criteria = \App\Models\Jenjang::getSettings($jenjangCode);
            if (!$criteria) {
                $criteria = (object) [
                    'promotion_max_kkm_failure' => 3,
                    'promotion_min_attendance' => 85,
                    'promotion_min_attitude' => 'B',
                    'kkm_default' => 70
                ];
            }
        } else {
             // Fallback if selectedClass is null
             $criteria = (object) [
                'promotion_max_kkm_failure' => 3,
                'promotion_min_attendance' => 85,
                'promotion_min_attitude' => 'B',
                'kkm_default' => 70
            ];
        }

        if ($selectedPeriod) {
            $isLast = $allPeriods->last() && $selectedPeriod->id === $allPeriods->last()->id;
            // Admin Access: Allow viewing any period
            // Wali Kelas/User: Only allow if Active
            // Wali Kelas/User: Only allow if Active

            if (!$isLast) {
                 $warningMessage = "⚠️ NOTE: Menampilkan data Periode: {$selectedPeriod->nama_periode}. Status akhir (Naik/Lulus) hanya valid di periode akhir.";
            }
        }

        return view('promotion.index', compact(
            'allClasses',
            'selectedClass',
            'allPeriods',
            'selectedPeriod',
            'students',
            'metrics',
            'isLocked',
            'warningMessage',
            'isFinalYear',
            'pageContext',
            'debugLog',
            'gradeLevel',
            'isMi',
            'isMts',
            'criteria'
        ));
    }

    // THE LOGIC ENGINE
    // THE LOGIC ENGINE
    // THE LOGIC ENGINE (ALIGNED WITH WALIKELAS LOGIC)
    public function calculate($kelasId, $filterPeriodId = null)
    {
        $activeYear = TahunAjaran::where('status', 'aktif')->firstOrFail();
        $kelas = Kelas::find($kelasId);
        if (!$kelas->relationLoaded('jenjang')) $kelas->load('jenjang');

        $jenjangCode = strtoupper(trim(optional($kelas->jenjang)->kode));
        if (!$jenjangCode) {
            if (stripos($kelas->nama_kelas, 'MTs') !== false) $jenjangCode = 'MTS';
            elseif (stripos($kelas->nama_kelas, 'MI') !== false) $jenjangCode = 'MI';
            else $jenjangCode = 'MI';
        }

        // Get Settings
        $settings = \App\Models\Jenjang::getSettings($jenjangCode);
        if (!$settings) {
            $settings = (object) [
                'promotion_max_kkm_failure' => 3,
                'promotion_min_attendance' => 85,
                'promotion_min_attitude' => 'B',
                'kkm_default' => 70
            ];
        }

        $students = AnggotaKelas::where('id_kelas', $kelasId)->get();
        if ($students->isEmpty()) return;

        $studentIds = $students->pluck('id_siswa');

        $periods = Periode::where('id_tahun_ajaran', $activeYear->id)
            ->where('lingkup_jenjang', $jenjangCode)
            ->get();
        $periodIds = $periods->pluck('id');

        // EAGER LOADING
        $allGrades = NilaiSiswa::with('mapel')
            ->whereIn('id_siswa', $studentIds)
            ->where('id_kelas', $kelasId)
            ->whereIn('id_periode', $periodIds)
            ->get()
            ->groupBy('id_siswa');

        $allKkm = \App\Models\KkmMapel::where('id_tahun_ajaran', $activeYear->id)
            ->where('jenjang_target', $jenjangCode)
            ->pluck('nilai_kkm', 'id_mapel');

        $allAttendance = DB::table('catatan_kehadiran')
            ->whereIn('id_siswa', $studentIds)
            ->where('id_kelas', $kelasId)
            ->whereIn('id_periode', $periodIds)
            ->get()
            ->groupBy('id_siswa');

        $allIjazah = DB::table('nilai_ijazah')
            ->whereIn('id_siswa', $studentIds)
            ->get()
            ->groupBy('id_siswa');

        $assignedMapelIds = \App\Models\PengajarMapel::where('id_kelas', $kelas->id)->pluck('id_mapel');

        // Fix: Use Jenjang-specific minimum grade
        $minLulusKey = ($jenjangCode === 'MTS') ? 'ijazah_min_lulus_mts' : 'ijazah_min_lulus_mi';
        $minLulusIjazah = (float) \App\Models\GlobalSetting::val($minLulusKey, 60);
        $totalDays = \App\Models\GlobalSetting::val('total_effective_days', 220);
        if ($totalDays <= 0) $totalDays = 220;

        // Att Rank
        $attRank = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];
        $minAttRank = $attRank[$settings->promotion_min_attitude] ?? 3;

         // Grade Level & Graduation Check
        $gradeLevel = $this->parseGradeLevel($kelas);
        $finalGradeMI = (int) \App\Models\GlobalSetting::val('final_grade_mi', 6);
        $finalGradeMTS = (int) \App\Models\GlobalSetting::val('final_grade_mts', 9);
        $isMts = $jenjangCode == 'MTS';
        $isMi = $jenjangCode == 'MI';

        $isFinalYear = ($isMi && $gradeLevel == $finalGradeMI) ||
                       ($isMts && ($gradeLevel == $finalGradeMTS || ($finalGradeMTS == 9 && $gradeLevel == 3)));


        foreach ($students as $student) {
            $report = [];
            $failConditions = [];
            $sid = $student->id_siswa;

            $studentGrades = $allGrades[$sid] ?? collect([]);

            // LOGIC 1: Cumulative Failures (Wali Kelas Syle)
            $underKkmCount = 0;
            $failedMapelNames = [];
            $yearlySum = 0;
            $mapelCount = 0;
            $hasAnyGrade = $studentGrades->isNotEmpty(); // Check if student has data at all

            foreach ($assignedMapelIds as $mId) {
                // Get KKM
                $kkm = $allKkm[$mId] ?? ($settings->kkm_default ?? 70);
                $theseGrades = $studentGrades->where('id_mapel', $mId);
                $avgMapel = $theseGrades->avg('nilai_akhir') ?? 0;
                $yearlySum += $avgMapel;
                $mapelCount++;

                // Cumulative Failure Check
                $hasFailure = false;
                foreach ($periods as $p) {
                     $pGrade = $theseGrades->where('id_periode', $p->id)->first();
                     // STRICT: Missing Grade = 0 (Failure)
                     $val = $pGrade ? $pGrade->nilai_akhir : 0;

                     if ($val < $kkm) {
                         $underKkmCount++;
                         $hasFailure = true;
                     }
                }
                if ($hasFailure) {
                    $mapelName = $mapelNames[$mId] ?? 'Mapel (' . $mId . ')';
                    $failedMapelNames[] = $mapelName;
                }
            }

            $finalAvg = $mapelCount > 0 ? round($yearlySum / $mapelCount, 2) : 0;

             // CHECK FAILURES vs SETTINGS
            if ($isFinalYear) {
                 // GRADUATION: Mapel failures are ignored for status, but noted.
            } else {
                 // PROMOTION: Strict
                 if ($underKkmCount > $settings->promotion_max_kkm_failure) {
                     $uniqueNames = array_unique($failedMapelNames);
                     $list = implode(', ', array_slice($uniqueNames, 0, 3));
                     if (count($uniqueNames) > 3) $list .= ", dll";
                     if (count($uniqueNames) > 3) $list .= ", dll";
                     $failConditions[] = "Gagal KKM ($underKkmCount > {$settings->promotion_max_kkm_failure}): $list";
                 }
            }

            // CHECK: No Mapel Data (Avoid Empty Pass)
            if ($mapelCount == 0) {
                 $failConditions[] = "Belum Ada Nilai / Mapel";
            }

            // CHECK: Requires All Periods (Data Completeness)
            // If student has NO grades in a period, they fail.
            // Copied from WaliKelasController logic
            $requiresAllPeriods = isset($settings->promotion_requires_all_periods) ? (bool)$settings->promotion_requires_all_periods : true;

            if ($requiresAllPeriods) {
                 // Check if student has participated in all periods (at least one grade entry per period)
                 $attendedPeriodIds = $studentGrades->pluck('id_periode')->unique();
                 $missingPeriodNames = [];

                 foreach($periods as $p) {
                     if (!$attendedPeriodIds->contains($p->id)) {
                         $missingPeriodNames[] = $p->nama_periode;
                     }
                 }

                 if (count($missingPeriodNames) > 0) {
                     $pNames = implode(', ', $missingPeriodNames);
                     $failConditions[] = "Nilai Kosong di Periode: $pNames";
                 }
            } elseif (!$hasAnyGrade) {
                 $failConditions[] = "Belum Ada Data Nilai";
            }


            // LOGIC 2: Attendance
            $studentAttendance = $allAttendance[$sid] ?? collect([]);
            $totalSakit = $studentAttendance->sum('sakit');
            $totalIzin = $studentAttendance->sum('izin');
            $totalAlpa = $studentAttendance->sum('tanpa_keterangan');

            $attendancePct = ($totalDays > 0) ? round((($totalDays - $totalAlpa) / $totalDays) * 100) : 0;
            $attendancePct = max(0, min(100, $attendancePct));

            if ($attendancePct < $settings->promotion_min_attendance) {
                 $failConditions[] = "Kehadiran {$attendancePct}% (Min: {$settings->promotion_min_attendance}%)";
            }

            // LOGIC 3: Attitude
            $lastAttitude = $studentAttendance->sortByDesc('id_periode')->first();
            $attitudeCode = 'B';
            if ($lastAttitude) {
                $attMap = ['Baik' => 'A', 'Cukup' => 'B', 'Kurang' => 'C'];
                $attitudeCode = $attMap[$lastAttitude->kelakuan] ?? 'B';
            }
            $currAttRank = $attRank[$attitudeCode] ?? 3;

            if ($currAttRank < $minAttRank) {
                 $failConditions[] = "Sikap {$attitudeCode} (Min: {$settings->promotion_min_attitude})";
            }

            // DECISION
            $fail = count($failConditions) > 0;
            if ($isFinalYear) {
                $recommendation = $fail ? 'not_graduated' : 'graduated';
            } else {
                $recommendation = $fail ? 'retained' : 'promoted';
            }

            // IJAZAH CHECK (Final Year Only)
            $ijazahNote = null;
            if ($isFinalYear) {
                $ijazahGrades = $allIjazah[$sid] ?? collect([]);
                $iCount = $ijazahGrades->where('nilai_ijazah', '>', 0)->count();
                $iSum = $ijazahGrades->where('nilai_ijazah', '>', 0)->sum('nilai_ijazah');

                if ($iCount > 0) {
                    $iAvg = $iSum / $iCount;
                    if ($iAvg >= $minLulusIjazah) {
                        if ($recommendation == 'not_graduated') {
                            $ijazahNote = "Ijazah LULUS ($iAvg), tapi syarat sekolah tidak terpenuhi.";
                        } else {
                            $recommendation = 'graduated';
                            $ijazahNote = "Ijazah LULUS (Avg: " . round($iAvg, 2) . ")";
                        }
                    } else {
                        $recommendation = 'not_graduated';
                        $ijazahNote = "Ijazah TIDAK LULUS (Avg: " . round($iAvg, 2) . " < $minLulusIjazah)";
                        if (!$fail) $failConditions[] = "Nilai Ijazah Kurang";
                    }
                }
            }

            if (count($failConditions) > 0) {
                $reason = implode(' | ', $failConditions);
            } else {
                $reason = 'Memenuhi semua syarat.';
            }
            if ($ijazahNote) {
                $reason .= " [$ijazahNote]";
            }

            // FETCH EXISTING TO CHECK LOCK STATUS
            $existingDecision = DB::table('promotion_decisions')
                ->where('id_siswa', $sid)
                ->where('id_kelas', $kelasId)
                ->where('id_tahun_ajaran', $activeYear->id)
                ->first();

            $isLocked = $existingDecision ? $existingDecision->is_locked : false;

            $dataToUpdate = [
                'average_score' => $finalAvg,
                'kkm_failure_count' => $underKkmCount,
                'attendance_percent' => $attendancePct,
                'attitude_grade' => $attitudeCode,
                'system_recommendation' => $recommendation,
                'notes' => $reason,
                'updated_at' => now()
            ];

            // AUTO-SYNC: If not locked, update final_decision to match recommendation
            if (!$isLocked) {
                $dataToUpdate['final_decision'] = $recommendation;
                $dataToUpdate['manual_override_by'] = null; // Clear old manual tracks if not locked
            }

            // SAVE
            DB::table('promotion_decisions')->updateOrInsert(
                [
                    'id_siswa' => $sid,
                    'id_kelas' => $kelasId,
                    'id_tahun_ajaran' => $activeYear->id
                ],
                $dataToUpdate
            );

            // Sync Override
             $existing = DB::table('promotion_decisions')
                ->where('id_siswa', $sid)
                ->where('id_kelas', $kelasId)
                ->where('id_tahun_ajaran', $activeYear->id)
                ->first();

            if ($existing && is_null($existing->override_by)) {
                DB::table('promotion_decisions')
                    ->where('id', $existing->id)
                    ->update(['final_decision' => $recommendation]);
            }
        }
    }

    public function updateDecision(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Promotion Update Request:', $request->all());

        if (!$this->checkActiveYear()) {
             return response()->json(['message' => '⚠️ AKSES DITOLAK: Periode terkunci.'], 403);
        }

        $decisionId = $request->decision_id;
        $studentId = $request->student_id;
        $classId = $request->class_id;
        $activeYear = TahunAjaran::where('status', 'aktif')->firstOrFail();

        \Illuminate\Support\Facades\Log::info("Promotion Update Processing: Sid: $studentId, Cid: $classId, Year: {$activeYear->id}, Status: " . $request->status);

        if ($decisionId) {
            $decision = DB::table('promotion_decisions')->where('id', $decisionId)->first();
            // Allow Admin to bypass lock
            if ($decision && !is_null($decision->override_by) && !Auth::user()->isAdmin()) {
                return response()->json(['message' => '⚠️ GAGAL: Data sudah dikunci permanen.'], 403);
            }
            DB::table('promotion_decisions')->where('id', $decisionId)->update([
                'final_decision' => $request->status,
                'override_by' => Auth::id(), // Update locker
                'updated_at' => now()
            ]);
        } elseif ($studentId && $classId) {
             $exists = DB::table('promotion_decisions')
                ->where('id_siswa', $studentId)
                ->where('id_kelas', $classId)
                ->where('id_tahun_ajaran', $activeYear->id)
                ->first();

             if ($exists && !is_null($exists->override_by) && !Auth::user()->isAdmin()) {
                 return response()->json(['message' => '⚠️ GAGAL: Data sudah dikunci permanen.'], 403);
             }
             DB::table('promotion_decisions')->updateOrInsert(
                ['id_siswa' => $studentId, 'id_kelas' => $classId, 'id_tahun_ajaran' => $activeYear->id],
                ['final_decision' => $request->status, 'updated_at' => now()]
             );
        } else {
             return response()->json(['message' => 'Missing ID parameters'], 400);
        }

        return response()->json(['message' => 'Status saved']);
    }

    public function bulkUpdateDecision(Request $request)
    {
        if (!$this->checkActiveYear()) {
             return response()->json(['message' => '⚠️ AKSES DITOLAK: Periode terkunci.'], 403);
        }

        return DB::transaction(function() use ($request) {
            $activeYear = TahunAjaran::where('status', 'aktif')->firstOrFail();
            $decisionIds = $request->decision_ids;
            $studentIds = $request->student_ids;
            $classId = $request->class_id;
            $status = $request->status;
            $count = 0;

            if ($decisionIds && count($decisionIds) > 0) {
                // Admin Bypass Logic for Bulk by ID
               $query = DB::table('promotion_decisions')->whereIn('id', $decisionIds);
               if (!Auth::user()->isAdmin()) {
                   $query->whereNull('override_by');
               }
               $dataToUpdate = ['final_decision' => $status, 'updated_at' => now()];
               if (Auth::user()->isAdmin()) {
                   $dataToUpdate['override_by'] = Auth::id();
               }
               $count = $query->update($dataToUpdate);

           } elseif ($studentIds && count($studentIds) > 0 && $classId) {
               foreach ($studentIds as $sid) {
                    $exists = DB::table('promotion_decisions')
                       ->where('id_siswa', $sid)
                       ->where('id_kelas', $classId)
                       ->where('id_tahun_ajaran', $activeYear->id)
                       ->first();

                    // Admin Bypass Logic for Bulk by Student ID
                    if ($exists && !is_null($exists->override_by) && !Auth::user()->isAdmin()) continue;

                    $dataToUpdate = ['final_decision' => $status, 'updated_at' => now()];
                    if (Auth::user()->isAdmin()) {
                        $dataToUpdate['override_by'] = Auth::id();
                    }

                    DB::table('promotion_decisions')->updateOrInsert(
                       ['id_siswa' => $sid, 'id_kelas' => $classId, 'id_tahun_ajaran' => $activeYear->id],
                       $dataToUpdate
                    );
                    $count++;
               }
           }

            return response()->json(['message' => "$count Santri Berhasil Diupdate.", 'count' => $count]);
        });
    }

    public function processPromotion(Request $request)
    {
        if (!$this->checkActiveYear()) {
             return back()->with('error', '⚠️ AKSES DITOLAK: Periode terkunci.');
        }

        return back()->with('success', 'Data kenaikan kelas berhasil diproses. Siswa akan dipindahkan pada Tutup Tahun Buku.');
    }

    public function processAll(Request $request)
    {
        if (!$this->checkActiveYear()) {
             return back()->with('error', '⚠️ AKSES DITOLAK: Periode terkunci.');
        }

        $activeYear = TahunAjaran::where('status', 'aktif')->firstOrFail();
        $allClasses = Kelas::where('id_tahun_ajaran', $activeYear->id)->get();

        $count = 0;
        foreach ($allClasses as $kelas) {
            $this->calculate($kelas->id);
            $count++;
        }

        return back()->with('success', "Berhasil menghitung ulang status kenaikan untuk $count kelas.");
    }

    public function finalize(Request $request)
    {
        if (!$this->checkActiveYear()) {
             return back()->with('error', '⚠️ AKSES DITOLAK: Periode terkunci.');
        }

        $activeYear = TahunAjaran::where('status', 'aktif')->firstOrFail();

        // Lock all decisions by setting override_by (so they are treated as manual decisions)
        // Only lock those that are not yet locked/overridden
        $affected = DB::table('promotion_decisions')
            ->where('id_tahun_ajaran', $activeYear->id)
            ->whereNull('override_by')
            ->update([
                'override_by' => Auth::id(),
                'updated_at' => now()
            ]);

        // --- SYNC TO ANGGOTA KELAS (BUKU INDUK CONTROL) ---
        // Ensures 'Keterangan' in Student Profile is Automatic
        $decisions = DB::table('promotion_decisions')
            ->where('id_tahun_ajaran', $activeYear->id)
            ->get();

        $syncCount = 0;
        foreach ($decisions as $dec) {
            $status = 'aktif';
            $final = $dec->final_decision ?? $dec->system_recommendation; // Use system if final is null? No, prefer final.

            // Map Decision to AnggotaKelas Status
            if ($final == 'promoted' || $final == 'conditional') $status = 'naik_kelas';
            elseif ($final == 'retained') $status = 'tinggal_kelas';
            elseif ($final == 'graduated') $status = 'lulus';
            elseif ($final == 'not_graduated') $status = 'tinggal_kelas';

            // Update AnggotaKelas
            // We need to match precise class member record
            $updated = AnggotaKelas::where('id_siswa', $dec->id_siswa)
                ->where('id_kelas', $dec->id_kelas)
                ->update(['status' => $status]);

            if ($updated) $syncCount++;
        }

        return back()->with('success', "Status Kenaikan Kelas BERHASIL DIKUNCI PERMANEN. $affected Data Di-finalisasi. $syncCount Data Riwayat/Buku Induk diperbarui otomatis.");
    }

    private function checkActiveYear()
    {
        $activeYear = TahunAjaran::where('status', 'aktif')->first();
        if (!$activeYear) return true; // Setup mode

        // 1. Check Global Switch
        $allowEdit = \App\Models\GlobalSetting::val('allow_edit_past_data', 0);
        if ($allowEdit) return true;

        // 2. Check if Current Year is Latest
        $latestYear = TahunAjaran::orderBy('id', 'desc')->first();
        if ($latestYear && $activeYear->id === $latestYear->id) {
            return true; // Latest year is always editable
        }

        // If Old Year & Lock is ON -> Block
        return false;
    }

    // Helper for Roman Numerals
    private function parseGradeLevel($kelas) {
        // 1. Try DB Column
        if (!empty($kelas->tingkat_kelas)) {
            return (int) $kelas->tingkat_kelas;
        }

        // 2. Try Standard Number Extract (e.g. "9A" -> 9)
        $num = (int) filter_var($kelas->nama_kelas, FILTER_SANITIZE_NUMBER_INT);
        if ($num > 0) return $num;

        // 3. Try Roman Numerals (Common in MTS)
        $romans = [
            'XII' => 12, 'XI' => 11, 'X' => 10,
            'IX' => 9, 'VIII' => 8, 'VII' => 7,
            'VI' => 6, 'V' => 5, 'IV' => 4,
            'III' => 3, 'II' => 2, 'I' => 1
        ];

        // Clean Name: Remove "KELAS" word if present
        $cleanName = trim(str_replace(['KELAS', 'Kelas', 'kelas'], '', $kelas->nama_kelas));
        $upperName = strtoupper($cleanName);

        foreach ($romans as $key => $val) {
            // Check if name START with Roman (e.g. "IX A")
            // Use word boundary check to avoid partial matches (e.g. "VI" in "DAVID")
            // But usually class names are simple.
            if (str_starts_with($upperName, $key . ' ') || $upperName === $key) {
                return $val;
            }
        }

        return 0; // Unknown
    }
}
