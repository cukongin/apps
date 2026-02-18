<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clean up duplicates first
        // We keep the latest entry (highest ID) for each combination
        $duplicates = DB::table('catatan_kehadiran')
            ->select('id_siswa', 'id_kelas', 'id_periode', DB::raw('MAX(id) as max_id'))
            ->groupBy('id_siswa', 'id_kelas', 'id_periode')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('catatan_kehadiran')
                ->where('id_siswa', $dup->id_siswa)
                ->where('id_kelas', $dup->id_kelas)
                ->where('id_periode', $dup->id_periode)
                ->where('id', '<', $dup->max_id) // Delete older ones
                ->delete();
        }

        // 2. Add Unique Constraint
        Schema::table('catatan_kehadiran', function (Blueprint $table) {
            $table->unique(['id_siswa', 'id_kelas', 'id_periode'], 'unique_attendance_entry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catatan_kehadiran', function (Blueprint $table) {
            $table->dropUnique('unique_attendance_entry');
        });
    }
};
