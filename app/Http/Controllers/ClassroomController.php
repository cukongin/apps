<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Jenjang;
use App\Models\TahunAjaran;
use App\Models\Mapel;
use App\Models\PengajarMapel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClassroomController extends Controller
{
    // Helper to standardize Class Name with Jenjang Suffix
    private function standardizeClassName($name, $jenjangId)
    {
        $jenjang = Jenjang::find($jenjangId);
        if (!$jenjang) return $name;

        $kode = trim($jenjang->kode); // MI, MTS, TPQ
        $name = trim($name);

        // Normalize Code for Display (MTs looks better than MTS)
        $suffix = $kode;
        if (strtoupper($kode) == 'MTS') $suffix = 'MTs';

        // SAFETY: If name ALREADY contains ANY Jenjang identifier, DO NOT APPEND.
        // This prevents "1 - MTs - MI" if user selects wrong Jenjang or during migration.
        if (preg_match('/(mi|mts|m\.ts|tpq)/i', $name)) {
            return $name;
        }

        // Check if specific suffix exists (Redundant now but safer)
        $pattern = '/' . preg_quote($suffix, '/') . '/i';

        if (!preg_match($pattern, $name)) {
            // Append format based on type
            if (strtoupper($kode) == 'TPQ') {
                 return $name . ' ' . $suffix; // "1 TPQ"
            } else {
                 return $name . ' - ' . $suffix; // "1 - MTs"
            }
        }

        return $name;
    }

    // Helper to Infer Grade Level (Smart Logic)
    private function inferGradeLevel($name, $jenjangId, $currentTingkat)
    {
        $firstChar = substr(trim($name), 0, 1);
        if (!is_numeric($firstChar)) return $currentTingkat;

        $val = (int)$firstChar;
        $jenjang = Jenjang::find($jenjangId);

        if ($jenjang && strtoupper($jenjang->kode) == 'MTS') {
            if ($val == 1) return 7;
            if ($val == 2) return 8;
            if ($val == 3) return 9;
        }

        return $currentTingkat; // Default: Trust user input or keep existing
    }

    public function index(Request $request)
    {
        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->first();

        $query = Kelas::with(['jenjang', 'tahun_ajaran', 'wali_kelas'])
            ->withCount(['anggota_kelas', 'pengajar_mapel']) // Eager load count for both
            ->where('id_tahun_ajaran', $activeYear->id ?? 0);

        // Filter by Jenjang
        if ($request->has('id_jenjang') && $request->id_jenjang != '') {
            $query->where('id_jenjang', $request->id_jenjang);
        }

        // Filter by Search (Name or Wali Kelas)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhereHas('wali_kelas', function($q_wali) use ($search) {
                      $q_wali->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $classes = $query->orderBy('id_jenjang')
            ->orderBy('tingkat_kelas')
            ->orderBy('nama_kelas')
            ->get();

        // Correct Stats for Tabs (Active Year Only)
        $levels = Jenjang::all();
        $stats = [
            'total_classes' => Kelas::where('id_tahun_ajaran', $activeYear->id ?? 0)->count(),
        ];
        foreach($levels as $lvl) {
            $stats['jenjang_' . $lvl->id] = Kelas::where('id_tahun_ajaran', $activeYear->id ?? 0)
                ->where('id_jenjang', $lvl->id)
                ->count();
        }

        $academicYears = TahunAjaran::where('status', 'aktif')->get();
        $teachers = User::where('role', 'teacher')->get();

        // Get Teachers who are ALREADY Wali Kelas in THIS Active Year
        $takenTeachers = [];
        if ($activeYear) {
            $takenTeachers = Kelas::where('id_tahun_ajaran', $activeYear->id)
                ->whereNotNull('id_wali_kelas')
                ->pluck('id_wali_kelas')
                ->toArray();
        }

        return view('classes.index', compact('classes', 'levels', 'academicYears', 'teachers', 'stats', 'takenTeachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'id_jenjang' => 'required',
            'tingkat_kelas' => 'required',
            'id_tahun_ajaran' => 'required',
        ]);

        // Validation: One Teacher One Class (Active Year)
        if ($request->id_wali_kelas) {
            $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->first();
            if ($activeYear) {
                $conflict = Kelas::where('id_tahun_ajaran', $activeYear->id)
                    ->where('id_wali_kelas', $request->id_wali_kelas)
                    ->first();

                if ($conflict) {
                    return back()->with('error', "Guru ini sudah menjadi Wali Kelas di kelas lain (Kelas {$conflict->nama_kelas}).");
                }
            }
        }

        $data = $request->all();
        $data['nama_kelas'] = $this->standardizeClassName($request->nama_kelas, $request->id_jenjang);

        // Smart Grade Inference (1 MTs -> 7, etc)
        $data['tingkat_kelas'] = $this->inferGradeLevel($request->nama_kelas, $request->id_jenjang, $request->tingkat_kelas);

        Kelas::create($data);

        return back()->with('success', 'Kelas berhasil dibuat');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'id_jenjang' => 'required',
            'tingkat_kelas' => 'required',
            'id_wali_kelas' => 'nullable|exists:users,id',
        ]);

        $kelas = Kelas::findOrFail($id);

        // Validation: One Teacher One Class (Same Year)
        // Only specific check if the Wali Kelas is CHANGED.
        // If they keep the same duplicate wali (legacy data), let them save other fields logic.
        if ($request->id_wali_kelas && $request->id_wali_kelas != $kelas->id_wali_kelas) {
            // Check existence in SAME Year, excluding THIS class
            $conflict = Kelas::where('id_tahun_ajaran', $kelas->id_tahun_ajaran)
                ->where('id_wali_kelas', $request->id_wali_kelas)
                ->where('id', '!=', $id)
                ->first();

            if ($conflict) {
                return back()->with('error', "Gagal disimpan: Guru ini sudah menjadi Wali Kelas di tempat lain (Kelas {$conflict->nama_kelas}). Silakan kosongkan atau ganti.");
            }
        }

        $data = $request->all();
        $data['nama_kelas'] = $this->standardizeClassName($request->nama_kelas, $request->id_jenjang);

        // Smart Grade Inference (1 MTs -> 7, etc)
        $data['tingkat_kelas'] = $this->inferGradeLevel($request->nama_kelas, $request->id_jenjang, $request->tingkat_kelas);

        $kelas->update($data);

        return back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        // Security check: Don't delete if students exist? Or soft delete?
        // User has "Reset Button" for mass delete, but single delete is nice too.
        // For now just standard delete, assuming FKs handles restriction or cascade.
        try {
            $kelas->delete();
            return back()->with('success', 'Kelas berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kelas (mungkin masih ada siswanya?)');
        }
    }

    public function show($id)
    {
        $class = Kelas::with(['jenjang', 'tahun_ajaran', 'wali_kelas', 'pengajar_mapel.guru', 'pengajar_mapel.mapel'])
            ->withCount('anggota_kelas')
            ->findOrFail($id);

        // Filter Mapel berdasarkan Jenjang Kelas + Mapel Umum (SEMUA)
        $jenjangKelas = $class->jenjang->kode; // MI atau MTS
        $subjects = Mapel::whereIn('target_jenjang', [$jenjangKelas, 'SEMUA'])->orderBy('nama_mapel')->get();

        $teachers = User::where('role', 'teacher')->get();

        // Hitung Kesiapan
        $totalMapel = $class->pengajar_mapel->count();
        $mapelTerisi = $class->pengajar_mapel->whereNotNull('id_guru')->count();
        $readiness = $totalMapel > 0 ? round(($mapelTerisi / $totalMapel) * 100) : 0;

        // Get Enrolled Students (For initial load)
        $query = $class->anggota_kelas()->with('siswa');

        \Illuminate\Support\Facades\Log::info('Debug Enrolled SQL: ' . $query->toSql(), $query->getBindings());

        $enrolledStudentsRaw = $query->get();

        \Illuminate\Support\Facades\Log::info('Debug Enrolled: Class ' . $id, [
            'count' => $enrolledStudentsRaw->count(),
            'first_item' => $enrolledStudentsRaw->first(),
            'first_siswa' => $enrolledStudentsRaw->first()?->siswa
        ]);

        $enrolledStudents = $enrolledStudentsRaw->map(function($m){
            // Safety check in case student is deleted
            if (!$m->siswa) {
                \Illuminate\Support\Facades\Log::warning('Orphaned AnggotaKelas: ' . $m->id . ' StudentID: ' . $m->id_siswa);
                return null;
            }
            return [
                'id' => $m->siswa->id,
                'nama_lengkap' => $m->siswa->nama_lengkap,
                'nis' => $m->siswa->nis_lokal,
                'initial' => substr($m->siswa->nama_lengkap, 0, 1)
            ];
        })->filter()->values();



        // Get All Classes for Move Modal (Same Year, Same Jenjang or Flexible?)
        // Let's allow SAME JENJANG for safety, or All? For TPQ, they might stay in TPQ.
        $allClasses = Kelas::where('id_tahun_ajaran', $class->id_tahun_ajaran)
            ->where('id', '!=', $id) // Exclude current
            ->where('id_jenjang', $class->id_jenjang) // Keep in same Jenjang
            ->orderBy('tingkat_kelas')
            ->orderBy('nama_kelas')
            ->get();

        return view('classes.show', compact('class', 'subjects', 'teachers', 'readiness', 'enrolledStudents', 'allClasses'));
    }

    public function getCandidates(Request $request, $classId)
    {
        try {
            $class = Kelas::findOrFail($classId);
            $search = $request->search;
            $activeYearId = $class->id_tahun_ajaran;

            // Get students already in a class for this year
            $bookedQuery = \App\Models\AnggotaKelas::whereHas('kelas', function($q) use ($activeYearId) {
                $q->where('id_tahun_ajaran', $activeYearId);
            });

            $bookedStudentIds = $bookedQuery->pluck('id_siswa');

            \Illuminate\Support\Facades\Log::info("GetCandidates Debug: Class {$classId}, Year {$activeYearId}", [
                'booked_count' => $bookedStudentIds->count(),
                'booked_ids_sample' => $bookedStudentIds->take(5),
                'sql' => $bookedQuery->toSql(),
                'bindings' => $bookedQuery->getBindings()
            ]);

            $query = \App\Models\Siswa::whereNotIn('id', $bookedStudentIds);

            // SPECIAL LOGIC: Grade 7 MTs can take Grade 6 MI Graduates (Status 'lulus')
            // But MUST NOT take Active MI Students (Grade 1-6).
            $isMtsGrade7 = ($class->tingkat_kelas == 7 && optional($class->jenjang)->kode == 'MTS');

            if ($isMtsGrade7) {
                $query->where(function($q) use ($class) {
                    // 1. Alumni / Lulusan ONLY FROM MI
                    $q->where(function($sub) {
                        $sub->where('status_siswa', 'lulus')
                            ->whereHas('jenjang', function($j) {
                                $j->where('kode', 'MI');
                            });
                    });

                    // 2. OR Active Students who are ALREADY MTs (New Registrants)
                    $q->orWhere(function($sub) use ($class) {
                        $sub->where('status_siswa', 'aktif')
                            ->where('id_jenjang', $class->id_jenjang);
                    });
                });
            } else {
                // NORMAL LOGIC: Strict Active & Jenjang
                $query->where('status_siswa', 'aktif');
                if ($class->id_jenjang) {
                    $query->where('id_jenjang', $class->id_jenjang);
                }
            }

            // Note: The previous jenjang check block is removed/merged into above logic

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nis_lokal', 'like', "%{$search}%");
                });
            }

            $students = $query->orderBy('nama_lengkap')->limit(50)->get();

            return response()->json($students);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('GetCandidates Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data santri.'], 500);
        }
    }

    public function addStudent(Request $request, $classId)
    {
        // HANDLE IMPLICIT BINDING (If Laravel passes a Model instead of ID)
        if (is_object($classId)) {
            $classId = $classId->id;
        }

        \Illuminate\Support\Facades\Log::info("AddStudent Hit: Class {$classId}", ['input' => $request->all()]);

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:siswa,id'
        ]);

        $count = 0;
        DB::beginTransaction();
        try {
            foreach ($request->student_ids as $studentId) {
                \Illuminate\Support\Facades\Log::info("Processing Student {$studentId} for Class {$classId}");

                // DELETE EXISTING (Force Reset)
                $deleted = \App\Models\AnggotaKelas::where('id_kelas', $classId)
                    ->where('id_siswa', $studentId)
                    ->delete();

                if ($deleted) {
                    \Illuminate\Support\Facades\Log::info("Deleted existing membership for {$studentId}");
                }

                // CREATE NEW
                $member = new \App\Models\AnggotaKelas();
                $member->id_kelas = $classId;
                $member->id_siswa = $studentId;
                $member->status = 'aktif';
                $member->save();

                \Illuminate\Support\Facades\Log::info("Created AnggotaKelas ID: {$member->id}");

                // SYNC SISWA
                $student = \App\Models\Siswa::find($studentId);
                $class = \App\Models\Kelas::find($classId);

                if ($student && $class) {
                    $updates = [];
                    if ($student->status_siswa == 'lulus') $updates['status_siswa'] = 'aktif';
                    if ($student->id_jenjang != $class->id_jenjang) $updates['id_jenjang'] = $class->id_jenjang;
                    $updates['kelas_id'] = $class->id; // Legacy Sync

                    if (!empty($updates)) {
                        $student->update($updates);
                         \Illuminate\Support\Facades\Log::info("Synced Siswa {$student->id}", $updates);
                    }
                }
                $count++;
            }

            DB::commit();
            \Illuminate\Support\Facades\Log::info("Transaction Committed. Count: {$count}");

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("AddStudent Failed: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menyimpan data.'], 500);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "{$count} Santri berhasil ditambahkan."]);
        }
        return back()->with('success', "{$count} Santri berhasil ditambahkan.");
    }

    public function removeStudent(Request $request, $classId, $studentId)
    {
        // Remove from AnggotaKelas
        \App\Models\AnggotaKelas::where('id_kelas', $classId)
            ->where('id_siswa', $studentId)
            ->delete();

        // Sync Legacy `kelas_id` to null
        $student = \App\Models\Siswa::find($studentId);
        if ($student && $student->kelas_id == $classId) {
            $student->update(['kelas_id' => null]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Santri berhasil dikeluarkan.']);
        }
        return back()->with('success', 'Santri berhasil dikeluarkan dari kelas.');
    }

    public function assignSubject(Request $request, $classId)
    {
        $request->validate([
            'id_mapel' => 'required|exists:mapel,id',
            'id_guru' => 'nullable|exists:users,id',
        ]);

        // Cek apakah mapel sudah ada di kelas ini
        $exists = PengajarMapel::where('id_kelas', $classId)
            ->where('id_mapel', $request->id_mapel)
            ->exists();

        if ($exists) {
            return back()->withErrors(['message' => 'Mata pelajaran ini sudah ada di kelas ini.']);
        }

        // Validate Jenjang Compatibility
        $mapel = Mapel::findOrFail($request->id_mapel);
        $kelas = Kelas::with('jenjang')->findOrFail($classId);

        // Logic: Mapel Target MUST match Class Jenjang OR be Universal (SEMUA/NULL)
        $mapelTarget = $mapel->target_jenjang;
        $classJenjang = $kelas->jenjang->kode ?? ''; // MI, MTS

        // Helper: Check if target contains class jenjang
        $isCompatible = false;
        if (empty($mapelTarget) || strtoupper($mapelTarget) === 'SEMUA') {
            $isCompatible = true;
        } elseif (str_contains(strtoupper($mapelTarget), strtoupper($classJenjang))) {
            $isCompatible = true;
        }

        if (!$isCompatible) {
            return back()->with('error', "Gagal: Mapel '{$mapel->nama_mapel}' diperuntukkan untuk jenjang {$mapelTarget}, tidak cocok dengan kelas ini ({$classJenjang}).");
        }

        PengajarMapel::create([
            'id_kelas' => $classId,
            'id_mapel' => $request->id_mapel,
            'id_guru' => $request->id_guru,
        ]);

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan ke kelas.');
    }
    public function updateSubjectTeacher(Request $request, $classId)
    {
        $request->validate([
            'id_mapel' => 'required|exists:mapel,id',
            'id_guru' => 'required|exists:users,id',
        ]);

        $assignment = PengajarMapel::where('id_kelas', $classId)
            ->where('id_mapel', $request->id_mapel)
            ->firstOrFail();

        $assignment->update([
            'id_guru' => $request->id_guru
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Guru pengampu berhasil diperbarui.']);
        }
        return back()->with('success', 'Guru pengampu berhasil diperbarui.');
    }

    public function autoAssignSubjects(Request $request, $classId)
    {
        try {
            $class = Kelas::with('jenjang')->findOrFail($classId);

            // 1. Get Template from MapelPlotting
            $templateMapelIds = \App\Models\MapelPlotting::where('id_jenjang', $class->id_jenjang)
                ->where('tingkat_kelas', $class->tingkat_kelas)
                ->pluck('id_mapel')
                ->toArray();

            if (empty($templateMapelIds)) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Belum ada konfigurasi plotting mapel untuk tingkat ini. Silakan atur di menu Master Mapel.']);
                }
                return back()->with('error', 'Belum ada konfigurasi plotting mapel untuk tingkat ini. Silakan atur di menu Master Mapel.');
            }

            $countAdded = 0;
            $countRemoved = 0; // Optional: if we want to enforce exact match

            // 2. Add New Mapels from Template
            foreach ($templateMapelIds as $mid) {
                // Check if already exists in class
                $exists = PengajarMapel::where('id_kelas', $class->id)
                    ->where('id_mapel', $mid)
                    ->exists();

                if (!$exists) {
                    PengajarMapel::create([
                        'id_kelas' => $class->id,
                        'id_mapel' => $mid,
                        'id_guru' => null // Optional: Copy from reference if needed later
                    ]);
                    $countAdded++;
                }
            }

            // 3. User requested "Ikut semua mapelnya" implies SYNC.
            // If they unchecked it in Plotting, it should theoretically disappear here if we "Auto Generate".
            // Let's REMOVE mapels that are NOT in the template?
            // "kalo mapel ini di plot ke kelas tertentu dia tidak berubah bosku meskipun di hapus kan beda tabel"
            // This suggests manual deletions in class should NOT affect template.
            // But does "Auto Generate" imply "Reset to Template"?
            // Usually Yes.

            // Safest: Add missing only. User can "Reset" first if they want valid sync.
            // But the complaint "otomatis... ikut semua mapelnya" when there was no separation caused the issue.
            // Now that we have separation, "Auto Generate" should purely Add Missing?
            // Or should it also Remove Extra?
            // Let's stick to ADD MISSING for now to be safe against deleting manual assignments.

            $msg = "Sinkronisasi Selesai: $countAdded mapel ditambahkan sesuai konfigurasi plotting.";

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AutoAssign Error: ' . $e->getMessage());
            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function resetSubjects(Request $request, $classId)
    {
        $count = \App\Models\PengajarMapel::where('id_kelas', $classId)->delete();
        return back()->with('success', "Berhasil menghapus $count mata pelajaran dari kelas ini.");
    }

    public function getSourceClasses(Request $request, Kelas $class)
    {
        // 1. Find Previous Year
        $currentYear = $class->tahun_ajaran;
        $prevYear = TahunAjaran::where('id', '!=', $currentYear->id)
            ->where('status', '!=', 'aktif')
            ->orderBy('id', 'desc')
            ->first();

        if (!$prevYear) {
            return response()->json(['error' => 'Tahun ajaran sebelumnya tidak ditemukan.'], 404);
        }

        // 2. Determine Target Grade (Tingkat Kelas - 1)
        $currentGrade = $class->tingkat_kelas;
        $targetGrade = $currentGrade - 1;

        // Special Logic: Grade 7 MTs pulls from Grade 6 (Any Jenjang, effectively MI)
        // If current is 7, target is 6.
        // Queries classes in Prev Year with Target Grade.

        $sources = Kelas::where('id_tahun_ajaran', $prevYear->id)
            ->where('tingkat_kelas', $targetGrade)
            ->with('jenjang')
            ->orderBy('nama_kelas')
            ->get()
            ->map(function($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->nama_kelas . " (" . $c->jenjang->nama . ")",
                    'count' => $c->anggota_kelas()->count()
                ];
            });

        return response()->json([
            'year' => $prevYear->nama,
            'target_grade' => $targetGrade,
            'sources' => $sources
        ]);
    }

    public function pullStudents(Request $request, Kelas $class)
    {
        $request->validate([
            'source_class_id' => 'required|exists:kelas,id'
        ]);

        $sourceClass = Kelas::findOrFail($request->source_class_id);

        // 1. Get Promoted Students from Source Class
        $promotedStudentIds = \Illuminate\Support\Facades\DB::table('promotion_decisions')
            ->where('id_kelas', $sourceClass->id)
            ->whereIn('final_decision', ['promoted', 'graduated'])
            ->pluck('id_siswa');

        if ($promotedStudentIds->isEmpty()) {
            return response()->json(['message' => 'Tidak ada siswa yang berstatus NAIK KELAS / LULUS di kelas asal.'], 422);
        }

        // 2. Filter already enrolled
        $existingIds = $class->anggota_kelas()->pluck('id_siswa')->toArray();
        $newIds = $promotedStudentIds->diff($existingIds);

        if ($newIds->isEmpty()) {
            return response()->json(['message' => 'Semua siswa yang naik kelas sudah ada di kelas ini.'], 422);
        }

        // 3. Insert
        $count = 0;
        foreach ($newIds as $sid) {
            \App\Models\AnggotaKelas::create([
                'id_kelas' => $class->id,
                'id_siswa' => $sid
            ]);
            $count++;
        }

        return response()->json([
            'message' => "Berhasil menarik $count siswa dari kelas {$sourceClass->nama_kelas}.",
            'count' => $count
        ]);
    }
    public function bulkPromote(Request $request)
    {
        // 1. Setup Context
        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->firstOrFail();
        $prevYear = \App\Models\TahunAjaran::where('id', '<', $activeYear->id) // Must be older than active
            ->orderBy('id', 'desc')
            ->first();

        if (!$prevYear) return back()->with('error', 'Tahun ajaran sebelumnya tidak ditemukan.');

        $jenjangId = $request->id_jenjang; // Optional filter

        // --- STEP 1: RESET / DELETE ONLY ---
        if ($request->has('reset_first') && $request->reset_first == '1') {
            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                $query = Kelas::where('id_tahun_ajaran', $activeYear->id);
                if ($jenjangId) $query->where('id_jenjang', $jenjangId);

                $classIds = $query->pluck('id');
                $count = $classIds->count();

                if ($classIds->isNotEmpty()) {
                    \Illuminate\Support\Facades\DB::table('anggota_kelas')->whereIn('id_kelas', $classIds)->delete();
                    \Illuminate\Support\Facades\DB::table('pengajar_mapel')->whereIn('id_kelas', $classIds)->delete();
                    \Illuminate\Support\Facades\DB::table('kelas')->whereIn('id', $classIds)->delete();
                }
                \Illuminate\Support\Facades\DB::commit();

                // FALLTHROUGH to Step 2 (Promote)
                // return back()->with('success', "BERHASIL MENGHAPUS $count KELAS...");

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                return back()->with('error', "Gagal menghapus data lama: " . $e->getMessage());
            }
        }

        // --- STEP 2: PROMOTE ---
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 2. Get Source Classes (Previous Year)
            $query = Kelas::where('id_tahun_ajaran', $prevYear->id);
            if ($jenjangId) {
                $query->where('id_jenjang', $jenjangId);
            }
            $prevClasses = $query->orderBy('tingkat_kelas', 'desc')->get();

            $countPromoted = 0;
            $countGraduated = 0;

            foreach ($prevClasses as $oldClass) {
                // Determine Logic based on Jenjang
                $jenjangKode = $oldClass->jenjang->kode; // MI / MTS
                $maxGrade = ($jenjangKode == 'MI') ? 6 : 9;

                // --- 1. HANDLE PROMOTION/GRADUATION ---
                if ($jenjangKode == 'TPQ') {
                    // TPQ Special Logic: Auto Promote ALL Active Students
                    // Unless they are specifically Retained? (No mechanism yet, so assume ALL promote)
                    $promotedIds = \App\Models\AnggotaKelas::where('id_kelas', $oldClass->id)->pluck('id_siswa');
                } else {
                    // Default Logic (Based on Report Card / Rapor)
                    $promotedIds = \Illuminate\Support\Facades\DB::table('promotion_decisions')
                        ->where('id_kelas', $oldClass->id)
                        ->whereIn('final_decision', ['promoted', 'graduated'])
                        ->pluck('id_siswa');
                }


                if ($promotedIds->isNotEmpty()) {
                    // Case A: GRADUATION
                    if ($oldClass->tingkat_kelas >= $maxGrade && $jenjangKode !== 'TPQ') {
                        \App\Models\Siswa::whereIn('id', $promotedIds)->update(['status_siswa' => 'lulus']);
                        $countGraduated += $promotedIds->count();
                    }
                    // Case B: PROMOTION (N -> N+1) OR TPQ RE-ENROLLMENT (N -> N)
                    else {
                        if ($jenjangKode == 'TPQ') {
                            // TPQ STAY IN SAME CLASS (Manual Move later)
                            $targetGrade = $oldClass->tingkat_kelas;
                            $targetName = $oldClass->nama_kelas;
                        } else {
                            // Normal Promotion
                            $targetGrade = $oldClass->tingkat_kelas + 1;
                            // Name: "1-A" -> "2-A"
                            $className = $oldClass->nama_kelas;
                            $targetName = preg_replace('/^\d+/', $targetGrade, $className);
                            if ($targetName == $className) {
                                $targetName = str_ireplace((string)$oldClass->tingkat_kelas, (string)$targetGrade, $className);
                                if ($targetName == $className) {
                                    $targetName = $targetGrade . " " . $className;
                                }
                            }
                        }

                        $targetClass = Kelas::firstOrCreate(
                            [
                                'id_tahun_ajaran' => $activeYear->id,
                                'nama_kelas' => $targetName,
                                'id_jenjang' => $oldClass->id_jenjang
                            ],
                            [
                                'tingkat_kelas' => $targetGrade,
                                'id_wali_kelas' => null
                            ]
                        );

                        foreach ($promotedIds as $sid) {
                            \App\Models\AnggotaKelas::firstOrCreate(['id_kelas' => $targetClass->id, 'id_siswa' => $sid]);
                            $countPromoted++;
                        }
                    }
                }

                // --- 2. HANDLE RETENTION (TINGGAL KELAS) ---
                $retainedIds = \Illuminate\Support\Facades\DB::table('promotion_decisions')
                    ->where('id_kelas', $oldClass->id)
                    ->where('final_decision', 'retained')
                    ->pluck('id_siswa');

                if ($retainedIds->isNotEmpty()) {
                    // Stay in Same Grade (N -> N)
                    // Name: "1-A" -> "1-A" (Same Name)
                    $targetClassRetention = Kelas::firstOrCreate(
                        [
                            'id_tahun_ajaran' => $activeYear->id,
                            'nama_kelas' => $oldClass->nama_kelas, // Keep original name
                            'id_jenjang' => $oldClass->id_jenjang
                        ],
                        [
                            'tingkat_kelas' => $oldClass->tingkat_kelas, // Keep same grade
                            'id_wali_kelas' => null
                        ]
                    );

                    foreach ($retainedIds as $sid) {
                        \App\Models\AnggotaKelas::firstOrCreate(['id_kelas' => $targetClassRetention->id, 'id_siswa' => $sid]);
                    }
                }
            }
            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', "Proses Selesai! $countPromoted Siswa Naik, $countGraduated Siswa Lulus.");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', "Terjadi kesalahan: " . $e->getMessage());
        }
    }

    public function resetActiveClasses(Request $request)
    {
        $activeYear = \App\Models\TahunAjaran::where('status', 'aktif')->firstOrFail();

        $classIds = Kelas::where('id_tahun_ajaran', $activeYear->id)->pluck('id');

        if ($classIds->isEmpty()) {
            return back()->with('error', 'Data sudah kosong boss!');
        }

        // FORCE DELETE via DB Query Builder (Nuclear Option)
        try {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

            \Illuminate\Support\Facades\DB::table('anggota_kelas')->whereIn('id_kelas', $classIds)->delete();
            \Illuminate\Support\Facades\DB::table('pengajar_mapel')->whereIn('id_kelas', $classIds)->delete();
            // Add any other potential dependencies here if discovered later
            \Illuminate\Support\Facades\DB::table('kelas')->whereIn('id', $classIds)->delete();

            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

            return back()->with('success', "BERHASIL! Data tahun ini sudah dibersihkan total.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            return back()->with('error', "Gagal reset: " . $e->getMessage());
        }
    }

    public function moveStudents(Request $request, $classId)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:siswa,id',
            'target_class_id' => 'required|exists:kelas,id|different:'.$classId,
        ]);

        $targetClass = Kelas::findOrFail($request->target_class_id);
        $count = 0;

        DB::beginTransaction();
        try {
            foreach ($request->student_ids as $studentId) {
                // 1. Remove from Current Class (Softly? No, Move means Move)
                // Actually, AnggotaKelas is unique per Student per Active Year (usually).
                // Or allows history?
                // For flexible moving in SAME year, we update the `id_kelas`.

                // Check if student is already in target class (safety)
                $exists = \App\Models\AnggotaKelas::where('id_kelas', $targetClass->id)
                    ->where('id_siswa', $studentId)
                    ->exists();

                if (!$exists) {
                    // Update existing record for this class (Move)
                    // Or Create New?
                    // If we treat it as "Promotion" in same year, maybe just update id_kelas.

                    // Find current membership
                    $currentMember = \App\Models\AnggotaKelas::where('id_kelas', $classId)
                        ->where('id_siswa', $studentId)
                        ->first();

                    if ($currentMember) {
                        $currentMember->update(['id_kelas' => $targetClass->id]);

                        // Sync Siswa Data
                        $student = \App\Models\Siswa::find($studentId);
                        $student->update([
                            'id_jenjang' => $targetClass->id_jenjang,
                            // 'kelas_id' => $targetClass->id // Legacy
                        ]);

                        $count++;
                    }
                }
            }
            DB::commit();
            return back()->with('success', "Berhasil memindahkan $count santri ke kelas {$targetClass->nama_kelas}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal memindahkan santri: " . $e->getMessage());
        }
    }
}
