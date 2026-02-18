<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Level;

class KenaikanKelasController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $allClasses = Kelas::with('level')->get();
        $levels = \App\Models\Level::all();

        $siswas = collect([]);
        $sourceClass = null;
        $promotionPlan = [];

        // Manual Mode: Single Class Source
        if ($request->filled('source_class_id')) {
            $sourceClass = Kelas::find($request->source_class_id);
            if ($sourceClass) {
                $siswas = Siswa::where('kelas_id', $sourceClass->id)
                    ->where('status', 'Aktif')
                    ->orderBy('nama')
                    ->get();
            }
        }

        // Magic Mode: Specific Level
        if ($request->filled('level_id')) {
            $promotionPlan = $this->generatePromotionPlan($request->level_id);
        }

        // History
        $historyBatches = \App\Models\PromotionBatch::with('user')->latest()->limit(20)->get();

        return view('keuangan.kenaikan_kelas.index', compact('allClasses', 'levels', 'siswas', 'sourceClass', 'promotionPlan', 'historyBatches'));
    }

    private function generatePromotionPlan($levelId)
    {
        $currentLevel = Level::find($levelId);
        if (!$currentLevel) {
            return []; // Return empty plan if level not found to avoid crash
        }

        $classes = Kelas::where('level_id', $levelId)->orderBy('nama', 'desc')->get();
        $plan = [];

        foreach ($classes as $kelas) {
            $currentGrade = null;
            $nextClassName = null;
            $patternType = null; // 'prefix' (1 Ula) or 'suffix' (Ula 1)
            $baseName = '';

            // Pattern 1: Number First ("1 Ula A")
            if (preg_match('/^(\d+)\s+(.*)$/', $kelas->nama, $matches)) {
                $currentGrade = intval($matches[1]);
                $baseName = $matches[2]; // "Ula A"
                $patternType = 'prefix';
            }
            // Pattern 2: Number Last ("Ula 1", "Juz Amma 1")
            elseif (preg_match('/^(.*)\s+(\d+)$/', $kelas->nama, $matches)) {
                $baseName = $matches[1]; // "Ula"
                $currentGrade = intval($matches[2]);
                $patternType = 'suffix';
            }

            if ($currentGrade !== null) {
                $nextGrade = $currentGrade + 1;

                if ($patternType === 'prefix') {
                    $nextClassName = $nextGrade . ' ' . $baseName;
                } else {
                    $nextClassName = $baseName . ' ' . $nextGrade;
                }

                // 1. Try to find NEXT GRADE in SAME LEVEL
                $targetClass = Kelas::where('nama', $nextClassName)
                    ->where('level_id', $levelId)
                    ->first();

                // 2. If not found, try NEXT LEVEL (Cross-Level Promotion)
                if (!$targetClass) {
                    // Pass the expected Format to helper
                    $targetClass = $this->findNextLevelClass($currentLevel, $baseName, $patternType);
                }

                if ($targetClass) {
                    $plan[] = [
                        'source' => $kelas,
                        'type' => 'promote',
                        'target' => $targetClass,
                        'student_count' => $kelas->siswas()->where('status', 'Aktif')->count()
                    ];
                } else {
                    // No next class found -> Graduate
                    $plan[] = [
                        'source' => $kelas,
                        'type' => 'graduate',
                        'target' => null,
                        'student_count' => $kelas->siswas()->where('status', 'Aktif')->count()
                    ];
                }
            } else {
                // Non-numeric class (e.g. "Juz Amma"), attempt simple mapping or manual
                // For TPQ "Juz Amma", check if there is "Al-Quran 1"?
                // Hardcoded fallback for common patterns
                $targetClass = null;
                if (stripos($kelas->nama, 'Juz Amma') !== false) {
                     // Try finding "Al-Quran 1" or "1 Al-Quran"
                     $targetClass = Kelas::where('level_id', $levelId)->where(function($q) {
                         $q->where('nama', 'like', 'Al-Quran 1%')
                           ->orWhere('nama', 'like', '1 Al-Quran%');
                     })->first();
                }

                if ($targetClass) {
                    $plan[] = [
                        'source' => $kelas,
                        'type' => 'promote',
                        'target' => $targetClass,
                        'student_count' => $kelas->siswas()->where('status', 'Aktif')->count()
                    ];
                } else {
                    $plan[] = [
                        'source' => $kelas,
                        'type' => 'manual',
                        'target' => null,
                        'student_count' => $kelas->siswas()->where('status', 'Aktif')->count()
                    ];
                }
            }
        }

        return $plan;
    }

    private function findNextLevelClass($currentLevel, $baseName = '', $patternType = 'prefix')
    {
        // Define Hierarchy (Must valid match 'nama' in levels table)
        $levelOrder = ['TPQ', 'MDT Ula', 'MDT Wustho', 'Aliyah'];

        $currentIndex = array_search($currentLevel->nama, $levelOrder);

        if ($currentIndex !== false && isset($levelOrder[$currentIndex + 1])) {
            $nextLevelName = $levelOrder[$currentIndex + 1];
            $nextLevel = Level::where('nama', $nextLevelName)->first();

            if ($nextLevel) {
                // Get ALL classes in next level to analyze
                $nextLevelClasses = Kelas::where('level_id', $nextLevel->id)->get();

                $gradeOneCandidates = [];

                foreach ($nextLevelClasses as $cls) {
                    // Check if class name contains "1" and is likely Grade 1
                    // Patterns: "^1 ", " 1$", "^1$"
                    $isGrade1 = false;
                    $suffix = '';

                    if (preg_match('/^1\s+(.*)$/', $cls->nama, $m)) {
                        $isGrade1 = true;
                        $suffix = $m[1]; // "A" from "1 A"
                    } elseif (preg_match('/^(.*)\s+1$/', $cls->nama, $m)) {
                        $isGrade1 = true;
                        $suffix = $m[1]; // "Ula" from "Ula 1"? No, could be "Ula 1 A"?
                        // Wait, regex for "Ula 1" -> $m[1]="Ula".
                        // Logic needs to be robust.
                    } elseif ($cls->nama == '1') {
                        $isGrade1 = true;
                    }

                    if ($isGrade1) {
                         $gradeOneCandidates[] = $cls;
                    }
                }

                // If candidates found
                if (count($gradeOneCandidates) > 0) {
                     // 1. Try to find match based on current suffix logic if complex?
                     // Currently simple: Just return the first Grade 1 we find.
                     // Making it too complex might fail again.
                     // PROPOSAL: Return the first "1" found, OR if there's "1 A" and "1 B", prompt manual?
                     // Let's just return the first one for now as auto-suggestion.
                     return $gradeOneCandidates[0];
                }

                // FALLBACK: Try finding by Name containing "1" even if regex strict failed
                $fuzzy = $nextLevelClasses->filter(function($c) {
                    return str_contains($c->nama, '1');
                })->first();

                if ($fuzzy) return $fuzzy;
            }
        }

        return null;
    }

    // 1. Show Confirmation Page
    public function magicProcess(Request $request)
    {
        $request->validate(['level_id' => 'required|exists:levels,id']);

        $plan = $this->generatePromotionPlan($request->level_id);
        $levelId = $request->level_id;

        return view('keuangan.kenaikan_kelas.confirm', compact('plan', 'levelId'));
    }

    // 2. Execute Actual Process
    public function executeMagicProcess(Request $request)
    {
        $request->validate(['level_id' => 'required|exists:levels,id']);

        $plan = $this->generatePromotionPlan($request->level_id);
        $level = \App\Models\Level::find($request->level_id);

        \Illuminate\Support\Facades\DB::transaction(function() use ($plan, $level) {
            // Create Batch
            $batch = \App\Models\PromotionBatch::create([
                'user_id' => auth()->id(),
                'action_type' => 'magic_level',
                'batch_name' => 'Kenaikan Otomatis Level ' . $level->nama,
                'details_count' => 0
            ]);

            $totalDetails = 0;

            foreach ($plan as $step) {
                if ($step['type'] === 'manual') continue;

                // Get Candidate Students
                $students = Siswa::where('kelas_id', $step['source']->id)
                    ->where('status', 'Aktif')
                    ->get();

                foreach ($students as $student) {
                    $oldClassId = $step['source']->id;
                    $newClassId = $step['target'] ? $step['target']->id : null;
                    $newStatus = ($step['type'] === 'graduate') ? 'Lulus' : 'Aktif';

                    // Record Detail
                    \App\Models\PromotionDetail::create([
                        'batch_id' => $batch->id,
                        'siswa_id' => $student->id,
                        'old_kelas_id' => $oldClassId,
                        'new_kelas_id' => $newClassId,
                        'old_status' => 'Aktif',
                        'new_status' => $newStatus
                    ]);

                    // Execute Update
                    $student->update([
                        'kelas_id' => $newClassId,
                        'status' => $newStatus
                    ]);

                    $totalDetails++;
                }
            }

            $batch->update(['details_count' => $totalDetails]);
        });

        return redirect()->route('kenaikan-kelas.index', ['level_id' => $request->level_id])
            ->with('success', "Proses Ajaib Selesai dan Tercatat di Riwayat!");
    }

    public function undoBatch($id)
    {
        $batch = \App\Models\PromotionBatch::with('details')->findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function() use ($batch) {
            foreach ($batch->details as $detail) {
                $siswa = Siswa::find($detail->siswa_id);
                if ($siswa) {
                    $siswa->update([
                        'kelas_id' => $detail->old_kelas_id,
                        'status' => $detail->old_status
                    ]);
                }
            }
            // Delete record or mark as reverted? Currently delete to keep history clean.
            $batch->delete();
        });

        return back()->with('success', 'Riwayat Kenaikan berhasil dibatalkan (Undo). Data siswa telah dikembalikan.');
    }

    public function process(Request $request)
    {
        $request->validate([
            "siswa_ids" => "required|array",
            "action_type" => "required|in:promote,graduate",
            "target_class_id" => "required_if:action_type,promote|nullable|exists:kelas,id",
        ]);

        $siswaIds = $request->siswa_ids;
        $action = $request->action_type;

        if ($action === "promote") {
            $targetClass = Kelas::find($request->target_class_id);

            if (!$targetClass) {
                 return back()->with("error", "Kelas tujuan tidak ditemukan.");
            }

            Siswa::whereIn("id", $siswaIds)->update([
                "kelas_id" => $targetClass->id
            ]);

            $message = count($siswaIds) . " siswa berhasil dipindahkan ke kelas " . $targetClass->nama;

        } else {
            Siswa::whereIn("id", $siswaIds)->update([
                "kelas_id" => null,
                "status" => "Lulus"
            ]);

            $message = count($siswaIds) . " siswa berhasil diluluskan (Alumni).";
        }

        return redirect()->route("kenaikan-kelas.index", ["source_class_id" => $request->source_class_id])
            ->with("success", $message);
    }
}
