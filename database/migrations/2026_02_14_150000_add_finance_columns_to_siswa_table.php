<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinanceColumnsToSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('siswa', function (Blueprint $table) {
            // Status (Required for BillService::generateForAll scope)
            if (!Schema::hasColumn('siswa', 'status')) {
                $table->string('status')->default('Aktif')->index()->after('id');
            }

            // Gender (Used by SantriController)
            if (!Schema::hasColumn('siswa', 'gender')) {
                $table->string('gender', 10)->nullable()->after('nama_lengkap');
            }

            // NIS (Used by SantriController, distinct from nis_lokal)
            if (!Schema::hasColumn('siswa', 'nis')) {
                $table->string('nis')->nullable()->unique()->after('id');
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
        Schema::table('siswa', function (Blueprint $table) {
            if (Schema::hasColumn('siswa', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('siswa', 'gender')) {
                $table->dropColumn('gender');
            }
            if (Schema::hasColumn('siswa', 'nis')) {
                $table->dropColumn('nis');
            }
        });
    }
}
