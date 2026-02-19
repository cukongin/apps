<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateSiswaStatusEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add 'meninggal' and 'tanpa_keterangan' to the ENUM list
        DB::statement("ALTER TABLE siswa MODIFY COLUMN status_siswa ENUM('aktif','lulus','mutasi','keluar','non-aktif','meninggal','tanpa_keterangan') DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original ENUM (Warning: This might fail if data exists with new values)
        // We generally don't revert constraints that cause data loss, but for completeness:
        // DB::statement("ALTER TABLE siswa MODIFY COLUMN status_siswa ENUM('aktif','lulus','mutasi','keluar','non-aktif') DEFAULT 'aktif'");
    }
}
