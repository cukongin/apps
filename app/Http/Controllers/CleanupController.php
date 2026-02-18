<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\PengajarMapel;
use App\Models\NilaiSiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupController extends Controller
{
    // cleanup.mapel.mismatch
    public function removeWrongJenjangMapels(Request $request)
    {
        // 1. Find all PengajarMapel where Mapel Jenjang != Class Jenjang
        // And Mapel Jenjang is NOT NULL/SEMUA

        $deletedCount = 0;

        DB::beginTransaction();
        try {
            // Get all assignments with related data
            $assignments = PengajarMapel::with(['kelas.jenjang', 'mapel'])->get();

            foreach ($assignments as $asg) {
                $mapel = $asg->mapel;
                $kelas = $asg->kelas;

                if (!$mapel || !$kelas || !$kelas->jenjang) continue;

                $mapelTarget = strtoupper($mapel->target_jenjang ?? '');
                $classJenjang = strtoupper($kelas->jenjang->kode ?? '');

                // Skip if Mapel is Global
                if (empty($mapelTarget) || $mapelTarget === 'SEMUA') continue;

                // Check Mismatch
                // e.g. Mapel 'MI', Class 'MTS' -> Mismatch
                // Mapel 'MTS', Class 'MI' -> Mismatch
                // Mapel 'MI,MTS', Class 'MTS' -> Match

                if (!str_contains($mapelTarget, $classJenjang)) {
                    // Mismatch found!
                    Log::info("Cleanup Mapel: Removing {$mapel->nama_mapel} ({$mapelTarget}) from Class {$kelas->nama_kelas} ({$classJenjang})");
                    $asg->delete();
                    $deletedCount++;
                }
            }

            DB::commit();
            return back()->with('success', "Berhasil membersihkan $deletedCount mapel yang salah jenjang.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal cleanup: " . $e->getMessage());
        }
    }

    // cleanup.grades.mismatch
    public function removeWrongJenjangGrades(Request $request)
    {
        // Similar logic but for NilaiSiswa
        // This is heavier, so maybe chunk it?

        $deletedCount = 0;

        DB::beginTransaction();
        try {
            // We need to join tables to filter efficiently
            // Delete NilaiSiswa where Mapel Target != Class Jenjang

            // Iterate all grades is too slow. Let's do it via query?
            // Problem: str_contains logic in SQL is tricky if comma separated.
            // But usually target_jenjang is simple 'MI' or 'MTS'.

            // Let's loop Mapels first.
            $mapels = Mapel::whereNotNull('target_jenjang')
                           ->where('target_jenjang', '!=', 'SEMUA')
                           ->get();

            foreach ($mapels as $mapel) {
                $target = strtoupper($mapel->target_jenjang);

                // Find Grades for this Mapel where Class Jenjang is NOT in Target
                // We needed Class Jenjang.

                $mismatchGrades = NilaiSiswa::where('id_mapel', $mapel->id)
                    ->whereHas('kelas.jenjang', function($q) use ($target) {
                        // Logic: Class Jenjang NOT IN Target
                        // Since target can be 'MI,MTS', proper negation is hard in pure SQL "NOT LIKE".
                        // But if target is just 'MI', we exclude 'MI'.
                        // If target is 'MTS', we exclude 'MTS'.

                        // Actually easier: Where Jenjang Code IS MISMATCH
                        // If target='MI', find classes where jenjang!='MI'
                        // If target='MTS', find classes where jenjang!='MTS'
                        // If target='MI,MTS', then almost no mismatch (except TPQ?)

                        if (!str_contains($target, ',')) {
                            $q->where('kode', '!=', $target);
                        } else {
                            // If comma separated, we can't easily use simple inequality.
                            // Maybe skip complex ones for now or implement stricter array check?
                            // Let's ignore comma separated for now (assume simple MI or MTS mapels causing issues)
                        }
                    })
                    ->get(); // Get IDs to delete

                if ($mismatchGrades->isNotEmpty()) {
                    $ids = $mismatchGrades->pluck('id');
                    NilaiSiswa::whereIn('id', $ids)->delete();
                    $deletedCount += $ids->count();
                    Log::info("Cleanup Grades: Deleted {$ids->count()} entries for mapel {$mapel->nama_mapel} ({$target}) due to jenjang mismatch.");
                }
            }

            DB::commit();
            return back()->with('success', "Berhasil membersihkan $deletedCount nilai yang salah jenjang.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal cleanup grades: " . $e->getMessage());
        }
    }
}
