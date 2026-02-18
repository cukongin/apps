<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nilai_siswa', function (Blueprint $table) {
            if (!Schema::hasColumn('nilai_siswa', 'nilai_akhir_asli')) {
                $table->decimal('nilai_akhir_asli', 5, 2)->nullable()->after('nilai_akhir');
            }
            if (!Schema::hasColumn('nilai_siswa', 'katrol_note')) {
                $table->string('katrol_note')->nullable()->after('nilai_akhir_asli');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_siswa', function (Blueprint $table) {
            if (Schema::hasColumn('nilai_siswa', 'katrol_note')) {
                $table->dropColumn('katrol_note');
            }
            if (Schema::hasColumn('nilai_siswa', 'nilai_akhir_asli')) {
                $table->dropColumn('nilai_akhir_asli');
            }
        });
    }
};
