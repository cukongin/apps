<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmartScholarshipTables extends Migration
{
    public function up()
    {
        Schema::create('kategori_keringanans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('aturan_diskons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_keringanan_id')->constrained()->onDelete('cascade');
            $table->foreignId('jenis_biaya_id')->constrained()->onDelete('cascade');
            $table->enum('tipe_diskon', ['percentage', 'nominal']);
            $table->decimal('jumlah', 15, 2); // 100 or 50000
            $table->timestamps();
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->foreignId('kategori_keringanan_id')->nullable()->after('status_siswa')->constrained()->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['kategori_keringanan_id']);
            $table->dropColumn('kategori_keringanan_id');
        });
        Schema::dropIfExists('aturan_diskons');
        Schema::dropIfExists('kategori_keringanans');
    }
}

