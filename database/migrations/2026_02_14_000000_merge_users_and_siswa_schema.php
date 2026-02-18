<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MergeUsersAndSiswaSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Merge Users Table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('email');
            }
            if (!Schema::hasColumn('users', 'foto')) {
                $table->string('foto')->nullable()->after('password');
            }
            // Ensure any other columns from Keuangan are present if needed
        });

        // 2. Ensure Siswa Table has Keuangan columns
        // Keuangan's 'santris' table had: nis, nama, kelas_id, gender, status, nama_wali, no_hp
        // Era's 'siswa' table has: nis_lokal, nama_lengkap, jenis_kelamin, status_siswa, nama_ayah, no_telp_ortu
        // We need to aliasing or adding columns if strictly needed by Keuangan code that uses 'Santri' model (which we will repurpose to use 'Siswa')

        Schema::table('siswa', function (Blueprint $table) {
            // Mapping:
            // nis -> nis_lokal (Era has nis_lokal, Keuangan used nis. We might need 'nis' alias or just update code)
            // Let's add 'nis' as a virtual column or just nullable if needed, but better to update code to use nis_lokal.
            // However, for strict compatibility with migrated code, let's check.

            // Keuangan: nama_wali -> Era: nama_ayah (or nama_ibu). Let's add nama_wali if missing for compatibility.
            if (!Schema::hasColumn('siswa', 'nama_wali')) {
                $table->string('nama_wali')->nullable()->after('nama_ibu');
            }

            // Keuangan: no_hp -> Era: no_telp_ortu.
            if (!Schema::hasColumn('siswa', 'no_hp')) {
                $table->string('no_hp')->nullable()->after('no_telp_ortu');
            }

            // Keuangan: kelas_id -> Era: uses AnggotaKelas pivot.
            // Keuangan uses `kelas_id` directly on Santri for current class.
            // To support Keuangan code without massive refactor, we can add `kelas_id` to siswa,
            // OR we must update all Keuangan logic to use AnggotaKelas.
            // Adding `kelas_id` is safer for "No Logic Change" requirement, allowing Keuangan to work as is.
            // But we must sync it with AnggotaKelas.

            if (!Schema::hasColumn('siswa', 'kelas_id')) {
                $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // We usually don't drop columns in merge/upgrades unless strictly necessary
        });
    }
}
